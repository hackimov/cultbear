<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class CartMergeAfterLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cart_is_merged_into_user_cart_after_login(): void
    {
        $user = User::factory()->create();
        $sessionId = session()->getId();

        $theme = Theme::query()->create(['name' => 'Theme', 'slug' => 'theme']);
        $product = Product::query()->create([
            'theme_id' => $theme->id,
            'name' => 'Product',
            'slug' => 'product',
            'article' => 'ART-MERGE',
            'base_price' => 1000,
        ]);
        $variant = ProductVariant::query()->create([
            'product_id' => $product->id,
            'model' => 'short',
            'size' => 'M',
            'color' => 'black',
            'sku_variant' => 'SKU-MERGE',
            'price' => 1000,
            'stock_quantity' => 10,
        ]);

        $guestCart = Cart::query()->create(['session_id' => $sessionId]);
        CartItem::query()->create([
            'cart_id' => $guestCart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
            'unit_price' => 1000,
        ]);

        Event::dispatch(new Login('web', $user, false));

        $this->assertDatabaseMissing('carts', ['id' => $guestCart->id]);
        $this->assertDatabaseHas('carts', ['user_id' => $user->id]);
        $this->assertDatabaseHas('cart_items', ['product_variant_id' => $variant->id, 'quantity' => 1]);
    }
}
