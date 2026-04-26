<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Jobs\SendTelegramOrderNotification;
use App\Models\Order;
use App\Models\Payment;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class TochkaWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $raw = trim($request->getContent());

        if ($raw === '') {
            return response()->json(['message' => 'empty body'], 400);
        }

        try {
            $payload = $this->decodeWebhookJwt($raw);
        } catch (Throwable) {
            return response()->json(['message' => 'invalid webhook'], 403);
        }

        if (($payload['webhookType'] ?? '') !== 'acquiringInternetPayment') {
            return response()->json(['message' => 'ignored'], 200);
        }

        $operationId = (string) ($payload['operationId'] ?? '');
        if ($operationId === '') {
            return response()->json(['message' => 'missing operation id'], 422);
        }

        $eventId = hash('sha256', $raw);

        $payment = Payment::query()->where('payment_id', $operationId)->first();
        if (! $payment) {
            return response()->json(['message' => 'payment not found'], 404);
        }

        if ($payment->event_id === $eventId) {
            return response()->json(['message' => 'duplicate ignored']);
        }

        $status = strtoupper((string) ($payload['status'] ?? ''));

        DB::transaction(function () use ($payment, $status, $payload, $eventId) {
            $payment->update([
                'event_id' => $eventId,
                'payment_status' => strtolower($status),
                'payload' => array_merge($payment->payload ?? [], [
                    'webhook' => $payload,
                    'webhook_raw_received_at' => now()->toIso8601String(),
                ]),
            ]);

            /** @var Order $order */
            $order = $payment->order()->lockForUpdate()->firstOrFail();

            if ($status === 'APPROVED') {
                foreach ($order->items as $item) {
                    $variant = $item->variant()->lockForUpdate()->firstOrFail();
                    if ($variant->stock_quantity < $item->quantity) {
                        abort(422, "Недостаточно остатка для {$variant->sku_variant}");
                    }
                    $variant->decrement('stock_quantity', $item->quantity);
                }

                $order->update([
                    'status' => 'paid',
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                ]);

                SendTelegramOrderNotification::dispatch($order->id);
            } elseif ($status === 'AUTHORIZED') {
                $order->update([
                    'payment_status' => 'authorized',
                ]);
            }
        });

        return response()->json(['ok' => true]);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws Throwable
     */
    private function decodeWebhookJwt(string $jwt): array
    {
        $decoded = JWT::decode($jwt, $this->webhookVerificationKey());

        return json_decode(json_encode($decoded, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
    }

    private function webhookVerificationKey(): Key
    {
        $pem = config('services.tochka.webhook_public_key_pem');
        if (is_string($pem) && $pem !== '') {
            return new Key($pem, 'RS256');
        }

        $jwkJson = config('services.tochka.webhook_jwk_public');
        if (is_string($jwkJson) && $jwkJson !== '') {
            /** @var array<string, mixed> $jwk */
            $jwk = json_decode($jwkJson, true, 512, JSON_THROW_ON_ERROR);

            return JWK::parseKey($jwk, 'RS256');
        }

        $default = (string) config('services.tochka.webhook_jwk_default');
        /** @var array<string, mixed> $jwk */
        $jwk = json_decode($default, true, 512, JSON_THROW_ON_ERROR);

        return JWK::parseKey($jwk, 'RS256');
    }
}
