@extends('layouts.app', ['title' => $theme->name.' — CultBear'])

@section('content')
    <style>
        .theme-cards-grid {
            margin-top: 1.5rem;
        }

        .theme-product-card {
            display: block;
            overflow: hidden;
            border: 1px solid #e4e4e7;
            border-radius: 0.75rem;
            background: #fff;
            padding: 0.75rem;
        }

        .theme-product-card:hover {
            border-color: #18181b;
        }

        .theme-product-media {
            overflow: hidden;
            border-radius: 0.625rem;
            background: #f4f4f5;
            margin-bottom: 0.75rem;
        }

        .theme-product-article {
            margin: 0;
            font-size: 0.75rem;
            line-height: 1.2;
            color: #71717a;
        }

        .theme-product-title {
            margin: 0.25rem 0 0;
            font-size: 1rem;
            line-height: 1.3;
            font-weight: 600;
            color: #18181b;
        }

        .theme-product-price {
            margin-top: 0.625rem;
            font-size: 0.9375rem;
            line-height: 1.25;
            font-weight: 700;
            color: #18181b;
        }
    </style>

    <section class="mx-auto max-w-7xl px-4 py-10">
        <h1 class="text-3xl font-black">{{ $theme->name }}</h1>
        <p class="mt-2 text-zinc-600">{{ $theme->description }}</p>
        @if($theme->banner_src)
            <div class="mt-6 overflow-hidden rounded-xl border border-zinc-200">
                <img src="{{ $theme->banner_src }}" alt="{{ $theme->name }}" class="h-48 w-full object-cover md:h-64">
            </div>
        @endif

        <div class="theme-cards-grid grid gap-4 sm:grid-cols-2 lg:grid-cols-{{ max(2, min(4, (int)$theme->layout_columns)) }}">
            @foreach($products as $product)
                @php
                    $productMedia = $product->media
                        ->sortByDesc(fn ($media) => (bool) $media->is_primary)
                        ->first();
                    $productThumbPath = $productMedia?->webp_path ?: $productMedia?->preview_path ?: $productMedia?->path;
                    $productThumbUrl = null;
                    if ($productThumbPath) {
                        $productThumbUrl = filter_var($productThumbPath, FILTER_VALIDATE_URL)
                            ? $productThumbPath
                            : \Illuminate\Support\Facades\Storage::disk($productMedia?->disk ?: 's3')->url($productThumbPath);
                    }
                @endphp

                <a href="/products/{{ $product->slug }}" class="theme-product-card">
                    <div class="theme-product-media">
                        @if($productThumbUrl)
                            <img src="{{ $productThumbUrl }}" alt="{{ $product->name }}" class="aspect-square w-full object-cover">
                        @else
                            <div class="aspect-square w-full bg-zinc-200"></div>
                        @endif
                    </div>
                    <p class="theme-product-article">{{ $product->article }}</p>
                    <h3 class="theme-product-title">{{ $product->name }}</h3>
                    <p class="theme-product-price">{{ number_format($product->base_price, 0, '.', ' ') }} ₽</p>
                </a>
            @endforeach
        </div>
    </section>
@endsection
