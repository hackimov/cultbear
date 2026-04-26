<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use App\Models\Theme;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request): JsonResponse|View
    {
        $products = Product::query()
            ->with(['variants', 'media'])
            ->where('is_active', true)
            ->when($request->string('search')->isNotEmpty(), function ($query) use ($request) {
                $search = $request->string('search')->toString();
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'ilike', "%{$search}%")
                        ->orWhere('article', 'ilike', "%{$search}%");
                });
            })
            ->orderByDesc('is_pinned')
            ->orderBy('sort_order')
            ->paginate(24);

        if ($request->expectsJson()) {
            return response()->json($products);
        }

        return view('catalog.index', [
            'products' => $products,
            'themes' => Theme::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'legal' => Setting::getValue('legal_details', []),
        ]);
    }

    public function theme(string $slug, Request $request): JsonResponse|View
    {
        $theme = Theme::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $products = $theme->products()
            ->with('variants')
            ->where('is_active', true)
            ->when($request->filled('size'), fn ($query) => $query->whereHas('variants', fn ($v) => $v->where('size', $request->string('size')->toString())))
            ->when($request->filled('model'), fn ($query) => $query->whereHas('variants', fn ($v) => $v->where('model', $request->string('model')->toString())))
            ->when($request->filled('in_stock'), fn ($query) => $query->whereHas('variants', fn ($v) => $v->where('stock_quantity', '>', 0)))
            ->orderByDesc('is_pinned')
            ->orderBy('sort_order')
            ->paginate(24);

        if ($request->expectsJson()) {
            return response()->json([
                'theme' => $theme,
                'products' => $products,
            ]);
        }

        return view('catalog.theme', [
            'theme' => $theme,
            'products' => $products,
            'themes' => Theme::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'legal' => Setting::getValue('legal_details', []),
        ]);
    }

    public function product(string $slug, Request $request): JsonResponse|View
    {
        $product = Product::query()
            ->with([
                'media',
                'variants' => fn ($query) => $query
                    ->where('is_active', true)
                    ->where('stock_quantity', '>', 0)
                    ->orderBy('id'),
            ])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        if ($request->expectsJson()) {
            return response()->json($product);
        }

        return view('catalog.product', [
            'product' => $product,
            'themes' => Theme::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'legal' => Setting::getValue('legal_details', []),
        ]);
    }
}
