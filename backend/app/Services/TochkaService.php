<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class TochkaService
{
    /**
     * Создание платёжной ссылки (Create Payment Operation).
     *
     * @param  array{payment_link_id:string, amount:float|int, purpose:string}  $params
     * @return array{payment_id:string,payment_url:?string,status:string,raw:array}
     */
    public function createPaymentLink(array $params): array
    {
        $baseUrl = rtrim((string) config('services.tochka.base_url'), '/');
        $apiKey = (string) config('services.tochka.api_key');
        $customerCode = (string) config('services.tochka.customer_code');

        if ($baseUrl === '' || $apiKey === '' || $customerCode === '') {
            throw new RuntimeException('Tochka API is not configured (base_url, api_key, customer_code).');
        }

        $paymentModes = config('services.tochka.payment_modes');
        if (! is_array($paymentModes) || $paymentModes === []) {
            $paymentModes = ['card', 'sbp'];
        }

        $data = array_filter([
            'customerCode' => $customerCode,
            'amount' => (float) $params['amount'],
            'purpose' => $params['purpose'],
            'paymentMode' => array_values($paymentModes),
            'paymentLinkId' => $params['payment_link_id'],
            'merchantId' => config('services.tochka.merchant_id'),
            'redirectUrl' => config('services.tochka.success_redirect_url'),
            'failRedirectUrl' => config('services.tochka.fail_redirect_url'),
        ], fn ($v) => $v !== null && $v !== '');

        $url = $baseUrl.'/acquiring/v1.0/payments';

        $response = Http::withToken($apiKey)
            ->acceptJson()
            ->asJson()
            ->withHeaders([
                'customer-code' => $customerCode,
            ])
            ->post($url, ['Data' => $data]);

        if (! $response->successful()) {
            throw new RuntimeException(
                'Tochka payment creation failed: '.json_encode($response->json(), JSON_UNESCAPED_UNICODE)
            );
        }

        $json = $response->json();
        $operation = $this->extractPaymentOperation($json);

        $operationId = $operation['operationId']
            ?? $operation['operation_id']
            ?? null;
        $paymentLink = $operation['paymentLink']
            ?? $operation['payment_link']
            ?? $operation['PaymentLink']
            ?? null;
        $status = (string) ($operation['status'] ?? 'CREATED');

        if ($operationId === null || $operationId === '') {
            throw new RuntimeException(
                'Tochka response missing operationId: '.json_encode($json, JSON_UNESCAPED_UNICODE)
            );
        }

        return [
            'payment_id' => (string) $operationId,
            'payment_url' => $paymentLink,
            'status' => strtolower($status),
            'raw' => is_array($json) ? $json : [],
        ];
    }

    /**
     * @param  array<string, mixed>  $json
     * @return array<string, mixed>
     */
    private function extractPaymentOperation(array $json): array
    {
        $block = $json['Data'] ?? $json['data'] ?? $json;

        if (! is_array($block)) {
            return [];
        }

        foreach (['Operation', 'operation', 'Operations', 'operations'] as $key) {
            if (empty($block[$key]) || ! is_array($block[$key])) {
                continue;
            }
            $list = $block[$key];
            $first = $list[0] ?? null;
            if (is_array($first)) {
                return $first;
            }
        }

        if (isset($block['operationId']) || isset($block['operation_id'])) {
            return $block;
        }

        return [];
    }
}
