<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CheckoutCreatesUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_order_for_authenticated_user(): void
    {
        $this->withoutMiddleware([VerifyCsrfToken::class, ValidateCsrfToken::class]);
        config()->set('services.tochka.base_url', 'https://tochka.test');
        config()->set('services.tochka.api_key', 'api_key');
        config()->set('services.tochka.customer_code', '300123123');
        Http::fake([
            'https://tochka.test/acquiring/v1.0/payments' => Http::response([
                'Data' => [
                    'Operation' => [[
                        'operationId' => 'pay_checkout_1',
                        'paymentLink' => 'https://pay.tochka.test/1',
                        'status' => 'CREATED',
                    ]],
                ],
            ], 200),
        ]);
        $user = User::factory()->create([
            'email' => 'ivan@example.com',
            'phone' => '+79991234567',
        ]);

        $theme = Theme::query()->create([
            'name' => 'Test Theme',
            'slug' => 'test-theme',
        ]);

        $product = Product::query()->create([
            'theme_id' => $theme->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'article' => 'ART-1',
            'base_price' => 1000,
        ]);

        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'model' => 'short',
            'size' => 'M',
            'color' => 'black',
            'sku_variant' => 'SKU-1',
            'price' => 1000,
            'stock_quantity' => 10,
        ]);

        $cart = Cart::query()->create(['user_id' => $user->id, 'session_id' => 'checkout-session']);
        CartItem::query()->create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
            'unit_price' => 1000,
        ]);

        $response = $this->actingAs($user)->postJson('/checkout', [
            'name' => 'Ivan Petrov',
            'email' => 'ivan@example.com',
            'phone' => '+79991234567',
            'address' => [
                'value' => 'Moscow, Tverskaya 1',
                'city' => 'Moscow',
                'postal_code' => '125009',
            ],
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('orders', ['email' => 'ivan@example.com']);
        $this->assertAuthenticated();
    }
}
