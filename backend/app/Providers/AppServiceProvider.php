<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use App\Models\Theme;
use App\Models\User;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ProductVariantPolicy;
use App\Policies\SettingPolicy;
use App\Policies\ThemePolicy;
use App\Policies\UserPolicy;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view): void {
            $view->with('legal', Setting::getValue('legal_details', []));
        });

        Gate::policy(Theme::class, ThemePolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(ProductVariant::class, ProductVariantPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Setting::class, SettingPolicy::class);

        Event::listen(Login::class, function (Login $event): void {
            $sessionId = session()->getId();
            $userCart = Cart::query()->firstOrCreate(
                ['user_id' => $event->user->id],
                ['session_id' => $sessionId]
            );

            $guestCart = Cart::query()
                ->where('session_id', $sessionId)
                ->whereNull('user_id')
                ->with('items')
                ->first();

            if (! $guestCart) {
                return;
            }

            foreach ($guestCart->items as $item) {
                $existing = $userCart->items()->where('product_variant_id', $item->product_variant_id)->first();
                if ($existing) {
                    $existing->increment('quantity', $item->quantity);
                } else {
                    $item->update(['cart_id' => $userCart->id]);
                }
            }

            $guestCart->delete();
        });
    }
}
