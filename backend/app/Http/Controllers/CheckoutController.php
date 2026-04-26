<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\TochkaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function store(Request $request, TochkaService $tochkaService): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'array'],
            'address.value' => ['required', 'string'],
            'address.city' => ['nullable', 'string'],
            'address.postal_code' => ['nullable', 'string'],
        ]);

        $sessionId = $request->session()->getId();
        $cart = Cart::query()
            ->with('items.variant.product')
            ->where(function ($query) use ($request, $sessionId) {
                $query->where('session_id', $sessionId);
                if ($request->user()) {
                    $query->orWhere('user_id', $request->user()->id);
                }
            })
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return response()->json(['message' => 'Корзина пуста.'], 422);
        }

        $wasCreated = false;
        $temporaryPassword = Str::password(12);

        $user = $request->user() ?? User::query()->firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'password' => Hash::make($temporaryPassword),
            ]
        );
        $wasCreated = $user->wasRecentlyCreated;

        try {
            $order = DB::transaction(function () use ($cart, $user, $validated, $tochkaService) {
            $subtotal = 0;

            $order = Order::query()->create([
                'user_id' => $user->id,
                'number' => 'CB-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'status' => 'new',
                'payment_status' => 'awaiting_payment',
                'customer_name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'delivery_address_json' => $validated['address'],
                'address_line' => $validated['address']['value'],
                'city' => $validated['address']['city'] ?? null,
                'postal_code' => $validated['address']['postal_code'] ?? null,
                'subtotal_amount' => 0,
                'total_amount' => 0,
            ]);

            foreach ($cart->items as $item) {
                if ($item->variant->stock_quantity < $item->quantity) {
                    abort(422, "Недостаточно остатка по SKU {$item->variant->sku_variant}");
                }

                $lineTotal = $item->quantity * $item->unit_price;
                $subtotal += $lineTotal;

                $order->items()->create([
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->variant->product->name,
                    'sku_variant' => $item->variant->sku_variant,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'line_total' => $lineTotal,
                ]);
            }

            $order->update([
                'subtotal_amount' => $subtotal,
                'total_amount' => $subtotal,
            ]);

            $paymentData = $tochkaService->createPaymentLink([
                'payment_link_id' => $order->number,
                'amount' => $order->total_amount,
                'purpose' => "Оплата заказа {$order->number}",
            ]);

            $payment = Payment::query()->create([
                'order_id' => $order->id,
                'provider' => 'tochka',
                'payment_id' => $paymentData['payment_id'],
                'payment_status' => $paymentData['status'],
                'payload' => $paymentData,
            ]);

            $cart->items()->delete();

                return $order->load('items', 'payments')->setRelation('payments', collect([$payment]));
            });
        } catch (RuntimeException $exception) {
            return response()->json([
                'message' => 'Не удалось создать платеж. Проверьте настройки эквайринга Точка.',
                'details' => $exception->getMessage(),
            ], 502);
        }

        auth()->login($user);

        if ($wasCreated) {
            Password::broker()->sendResetLink(['email' => $user->email]);
        }

        return response()->json([
            'order' => $order,
            'payment_url' => $order->payments->first()?->payload['payment_url'] ?? null,
            'password_setup_email_sent' => $wasCreated,
        ], 201);
    }
}
