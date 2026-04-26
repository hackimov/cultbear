<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\TochkaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function store(Request $request, TochkaService $tochkaService): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'array'],
            'address.value' => ['nullable', 'string', 'max:255', 'required_without:address_line'],
            'address.city' => ['nullable', 'string', 'max:120'],
            'address.postal_code' => ['nullable', 'string', 'max:20'],
            'address_line' => ['nullable', 'string', 'max:255', 'required_without:address.value'],
            'city' => ['nullable', 'string', 'max:120'],
            'postal_code' => ['nullable', 'string', 'max:20'],
        ]);

        $addressLine = $validated['address']['value'] ?? $validated['address_line'] ?? '';
        $city = $validated['address']['city'] ?? $validated['city'] ?? null;
        $postalCode = $validated['address']['postal_code'] ?? $validated['postal_code'] ?? null;
        $deliveryAddress = [
            'value' => $addressLine,
            'city' => $city,
            'postal_code' => $postalCode,
        ];

        $sessionId = $request->session()->getId();
        $cart = Cart::query()
            ->with('items.variant.product')
            ->where(function ($query) use ($request, $sessionId) {
                $query->where('session_id', $sessionId);
                if ($request->user()) {
                    $query->orWhere('user_id', $request->user()->id);
                }
            })
            ->first();

        if (! $cart) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Корзина не найдена.'], 404);
            }

            return redirect('/cart')->with('error', 'Корзина не найдена.');
        }

        if ($cart->items->isEmpty()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Корзина пуста.'], 422);
            }

            return redirect('/cart')->with('error', 'Корзина пуста.');
        }

        foreach ($cart->items as $item) {
            if ($item->variant->stock_quantity < $item->quantity) {
                $message = "Недостаточно остатка по SKU {$item->variant->sku_variant}";

                if ($request->expectsJson()) {
                    return response()->json(['message' => $message], 422);
                }

                return redirect('/cart')->with('error', $message);
            }
        }

        $wasCreated = false;
        $temporaryPassword = Str::password(12);

        $user = $request->user() ?? User::query()->firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'password' => Hash::make($temporaryPassword),
                'address_line' => $addressLine,
                'city' => $city,
                'postal_code' => $postalCode,
            ]
        );
        $wasCreated = $user->wasRecentlyCreated;

        if (! $wasCreated) {
            $user->forceFill([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'address_line' => $addressLine,
                'city' => $city,
                'postal_code' => $postalCode,
            ])->save();
        }

        try {
            $order = DB::transaction(function () use ($cart, $user, $validated, $tochkaService, $deliveryAddress, $addressLine, $city, $postalCode) {
                $subtotal = 0;

            $order = Order::query()->create([
                'user_id' => $user->id,
                'number' => 'CB-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'status' => 'new',
                'payment_status' => 'awaiting_payment',
                'customer_name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'delivery_address_json' => $deliveryAddress,
                'address_line' => $addressLine,
                'city' => $city,
                'postal_code' => $postalCode,
                'subtotal_amount' => 0,
                'total_amount' => 0,
            ]);

                foreach ($cart->items as $item) {
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
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Не удалось создать платеж. Проверьте настройки эквайринга Точка.',
                    'details' => $exception->getMessage(),
                ], 502);
            }

            return redirect('/cart')->withInput()->with('error', 'Не удалось создать платеж. Проверьте настройки эквайринга Точка.');
        }

        auth()->login($user);

        if ($wasCreated) {
            Password::broker()->sendResetLink(['email' => $user->email]);
        }

        $paymentUrl = $order->payments->first()?->payload['payment_url'] ?? null;

        if ($request->expectsJson()) {
            return response()->json([
                'order' => $order,
                'payment_url' => $paymentUrl,
                'password_setup_email_sent' => $wasCreated,
            ], 201);
        }

        if ($paymentUrl) {
            return redirect()->away($paymentUrl);
        }

        return redirect('/account/orders/'.$order->id)->with('status', 'Заказ создан.');
    }
}
