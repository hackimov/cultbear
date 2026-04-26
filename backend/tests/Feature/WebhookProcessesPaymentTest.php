<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Theme;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;

class WebhookProcessesPaymentTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{0: string, 1: string} [privatePem, publicPem] */
    private function generateRsaPemKeyPair(): array
    {
        $resource = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        if ($resource === false) {
            self::fail('Could not generate RSA key');
        }
        openssl_pkey_export($resource, $privatePem);
        $details = openssl_pkey_get_details($resource);
        self::assertIsArray($details);

        return [$privatePem, $details['key']];
    }

    private function postPlainTextWebhook(string $body): \Illuminate\Testing\TestResponse
    {
        return $this->call(
            'POST',
            '/webhooks/tochka/payment',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'text/plain',
                'HTTP_ACCEPT' => 'application/json',
            ],
            $body
        );
    }

    public function test_payment_webhook_marks_order_paid_and_decrements_stock(): void
    {
        $this->withoutMiddleware([VerifyCsrfToken::class, ValidateCsrfToken::class]);
        [$privatePem, $publicPem] = $this->generateRsaPemKeyPair();
        config()->set('services.tochka.webhook_public_key_pem', $publicPem);
        config()->set('services.tochka.webhook_jwk_public', null);

        $user = User::factory()->create();
        $theme = Theme::query()->create(['name' => 'Theme', 'slug' => 'theme']);
        $product = Product::query()->create([
            'theme_id' => $theme->id,
            'name' => 'Product',
            'slug' => 'product',
            'article' => 'A-1',
            'base_price' => 1000,
        ]);
        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'model' => 'short',
            'size' => 'L',
            'color' => 'black',
            'sku_variant' => 'SKU-WH-1',
            'price' => 1000,
            'stock_quantity' => 5,
        ]);

        $order = Order::query()->create([
            'user_id' => $user->id,
            'number' => 'CB-TEST-1',
            'status' => 'awaiting_payment',
            'payment_status' => 'awaiting_payment',
            'customer_name' => 'Test',
            'email' => 'test@example.com',
            'phone' => '+79990000000',
            'delivery_address_json' => ['value' => 'address'],
            'address_line' => 'address',
            'subtotal_amount' => 2000,
            'total_amount' => 2000,
        ]);
        OrderItem::query()->create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'product_name' => 'Product',
            'sku_variant' => 'SKU-WH-1',
            'quantity' => 2,
            'unit_price' => 1000,
            'line_total' => 2000,
        ]);
        Payment::query()->create([
            'order_id' => $order->id,
            'provider' => 'tochka',
            'payment_id' => 'pay-1',
            'payment_status' => 'awaiting_payment',
        ]);

        $jwt = JWT::encode([
            'webhookType' => 'acquiringInternetPayment',
            'operationId' => 'pay-1',
            'status' => 'APPROVED',
        ], $privatePem, 'RS256');

        $this->postPlainTextWebhook($jwt)
            ->assertOk();

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'paid', 'payment_status' => 'paid']);
        $this->assertDatabaseHas('product_variants', ['id' => $variant->id, 'stock_quantity' => 3]);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        $this->withoutMiddleware([VerifyCsrfToken::class, ValidateCsrfToken::class]);
        [, $publicPem] = $this->generateRsaPemKeyPair();
        config()->set('services.tochka.webhook_public_key_pem', $publicPem);
        config()->set('services.tochka.webhook_jwk_public', null);

        $payment = Payment::query()->create([
            'order_id' => Order::query()->create([
                'number' => 'CB-TEST-2',
                'status' => 'awaiting_payment',
                'payment_status' => 'awaiting_payment',
                'customer_name' => 'Test',
                'email' => 'test2@example.com',
                'phone' => '+79990000001',
                'delivery_address_json' => ['value' => 'address'],
                'address_line' => 'address',
                'subtotal_amount' => 1000,
                'total_amount' => 1000,
            ])->id,
            'provider' => 'tochka',
            'payment_id' => 'pay-2',
            'payment_status' => 'awaiting_payment',
        ]);

        $this->postPlainTextWebhook('not-a-valid-jwt')->assertForbidden();

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'event_id' => null]);
    }

    public function test_webhook_duplicate_event_is_ignored(): void
    {
        $this->withoutMiddleware([VerifyCsrfToken::class, ValidateCsrfToken::class]);
        [$privatePem, $publicPem] = $this->generateRsaPemKeyPair();
        config()->set('services.tochka.webhook_public_key_pem', $publicPem);
        config()->set('services.tochka.webhook_jwk_public', null);

        $order = Order::query()->create([
            'number' => 'CB-TEST-3',
            'status' => 'awaiting_payment',
            'payment_status' => 'awaiting_payment',
            'customer_name' => 'Test',
            'email' => 'test3@example.com',
            'phone' => '+79990000002',
            'delivery_address_json' => ['value' => 'address'],
            'address_line' => 'address',
            'subtotal_amount' => 1000,
            'total_amount' => 1000,
        ]);

        $payment = Payment::query()->create([
            'order_id' => $order->id,
            'provider' => 'tochka',
            'payment_id' => 'pay-3',
            'payment_status' => 'awaiting_payment',
        ]);

        $jwt = JWT::encode([
            'webhookType' => 'acquiringInternetPayment',
            'operationId' => 'pay-3',
            'status' => 'APPROVED',
        ], $privatePem, 'RS256');

        $this->postPlainTextWebhook($jwt)->assertOk();
        $this->postPlainTextWebhook($jwt)
            ->assertOk()
            ->assertJson(['message' => 'duplicate ignored']);

        $this->assertNotNull($payment->fresh()->event_id);
    }
}
