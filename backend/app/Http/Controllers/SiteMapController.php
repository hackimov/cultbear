<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Theme;
use Illuminate\Http\Response;

class SiteMapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = collect([
            url('/'),
            url('/about'),
            url('/delivery'),
            url('/contacts'),
            url('/privacy-policy'),
            url('/terms'),
        ])->merge(
            Theme::query()->where('is_active', true)->pluck('slug')->map(fn ($slug) => url("/themes/{$slug}"))
        )->merge(
            Product::query()->where('is_active', true)->pluck('slug')->map(fn ($slug) => url("/products/{$slug}"))
        );

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
