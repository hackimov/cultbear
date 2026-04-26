@extends('layouts.app', ['title' => 'CultBear — Главная'])

@section('content')
    <style>
        .home-cards-grid {
            margin-top: 1.5rem;
        }

        .home-card {
            display: block;
            overflow: hidden;
            border: 1px solid #e4e4e7;
            border-radius: 0.75rem;
            background: #fff;
            padding: 0.75rem;
        }

        .home-card:hover {
            border-color: #18181b;
        }

        .home-card-media {
            overflow: hidden;
            border-radius: 0.625rem;
            background: #f4f4f5;
            margin-bottom: 0.75rem;
        }

        .home-card-title {
            margin: 0;
            font-size: 1rem;
            line-height: 1.3;
            font-weight: 600;
            color: #18181b;
        }

        .home-card-description {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            line-height: 1.45;
            color: #52525b;
        }

        .home-card-article {
            margin: 0;
            font-size: 0.75rem;
            line-height: 1.2;
            color: #71717a;
        }

        .home-card-price {
            margin-top: 0.625rem;
            font-size: 0.9375rem;
            line-height: 1.25;
            font-weight: 700;
            color: #18181b;
        }
    </style>

    <section class="hero-observe relative overflow-hidden bg-zinc-100">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-16 md:grid-cols-2 md:py-24">
            <div>
                <img src="{{ url('/logo-without-text.svg') }}" alt="CultBear" width="350" height="345" class="mb-4 block">
                <h1 class="text-4xl font-black leading-tight md:text-5xl">Футболки с символикой России</h1>
                <p class="mt-4 max-w-xl text-zinc-700">Современный каталог с акцентом на черные модели, качественную печать и удобный заказ онлайн.</p>
                <a href="{{ $themes->first() ? '/themes/'.$themes->first()->slug : '#' }}" class="mt-8 inline-block rounded bg-black px-6 py-3 text-sm font-semibold text-white">
                    Перейти в каталог
                </a>
            </div>
            <div class="flex h-72 items-center justify-center md:h-96">
                <img src="{{ url('/t-shirt.png') }}" alt="Футболка CultBear" class="max-h-full w-auto object-contain">
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-12">
        <h2 class="text-2xl font-bold">Популярные тематики</h2>
        <div class="home-cards-grid grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($themes as $theme)
                <a href="/themes/{{ $theme->slug }}" class="home-card">
                    <div class="home-card-media">
                        @if($theme->banner_src)
                            <img src="{{ $theme->banner_src }}" alt="{{ $theme->name }}" class="aspect-[16/10] w-full object-cover">
                        @else
                            <div class="aspect-[16/10] w-full bg-zinc-200"></div>
                        @endif
                    </div>
                    <h3 class="home-card-title">{{ $theme->name }}</h3>
                    <p class="home-card-description">{{ $theme->description ?? 'Тематическая подборка товаров.' }}</p>
                </a>
            @empty
                <p class="text-zinc-600">Тематики пока не добавлены.</p>
            @endforelse
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-14">
        <h2 class="text-2xl font-bold">Хиты продаж и новинки</h2>
        <div class="home-cards-grid grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
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

                <a href="/products/{{ $product->slug }}" class="home-card">
                    <div class="home-card-media">
                        @if($productThumbUrl)
                            <img src="{{ $productThumbUrl }}" alt="{{ $product->name }}" class="aspect-square w-full object-cover">
                        @else
                            <div class="aspect-square w-full bg-zinc-200"></div>
                        @endif
                    </div>
                    <p class="home-card-article">{{ $product->article }}</p>
                    <h3 class="home-card-title mt-1">{{ $product->name }}</h3>
                    <p class="home-card-price">{{ number_format($product->base_price, 0, '.', ' ') }} ₽</p>
                </a>
            @endforeach
        </div>
    </section>
@endsection
