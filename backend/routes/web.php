<?php

use App\Http\Controllers\Account\OrderController as AccountOrderController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\AddressSuggestionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SiteMapController;
use App\Http\Controllers\Webhook\TochkaWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index']);
Route::get('/catalog', [CatalogController::class, 'catalog']);
Route::get('/themes/{slug}', [CatalogController::class, 'theme']);
Route::get('/products/{slug}', [CatalogController::class, 'product']);

Route::get('/cart', [CartController::class, 'show']);
Route::post('/cart/items', [CartController::class, 'storeItem']);
Route::patch('/cart/items/{id}', [CartController::class, 'updateItem']);
Route::delete('/cart/items/{id}', [CartController::class, 'destroyItem']);

Route::post('/checkout', [CheckoutController::class, 'store']);
Route::get('/checkout/address-suggestions', AddressSuggestionController::class);
Route::post('/webhooks/tochka/payment', TochkaWebhookController::class);
Route::get('/about', [PageController::class, 'about']);
Route::get('/delivery', [PageController::class, 'delivery']);
Route::get('/contacts', [PageController::class, 'contacts']);
Route::get('/privacy-policy', [PageController::class, 'privacy']);
Route::get('/personal-data-policy', [PageController::class, 'personalDataPolicy']);
Route::get('/terms', [PageController::class, 'terms']);
Route::get('/payment/success', [PageController::class, 'paymentSuccess']);
Route::get('/payment/failed', [PageController::class, 'paymentFailed']);
Route::get('/sitemap.xml', SiteMapController::class);
Route::get('/robots.txt', fn () => response("User-agent: *\nAllow: /\nSitemap: ".url('/sitemap.xml'), 200, ['Content-Type' => 'text/plain']));
Route::get('/logo.svg', fn () => response()->file(resource_path('images/logo.svg'), [
    'Content-Type' => 'image/svg+xml',
]));
Route::get('/logo-header.svg', fn () => response()->file(resource_path('images/logo_header.svg'), [
    'Content-Type' => 'image/svg+xml',
]));
Route::get('/logo-without-text.svg', fn () => response()->file(resource_path('images/logo_without_text.svg'), [
    'Content-Type' => 'image/svg+xml',
]));
Route::get('/t-shirt.png', fn () => response()->file(resource_path('images/t-shirt.png'), [
    'Content-Type' => 'image/png',
]));

Route::middleware('auth')->prefix('/account')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::get('/orders', [AccountOrderController::class, 'index']);
    Route::get('/orders/{id}', [AccountOrderController::class, 'show']);
});
