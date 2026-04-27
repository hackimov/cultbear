@extends('layouts.app', ['title' => 'CultBear — Главная'])

@section('content')
    <style>
        .home-hero-section {
            padding: 3rem 1.25rem 3.5rem;
        }

        .home-hero-grid {
            max-width: 1180px;
            margin: 0 auto;
            display: grid;
            gap: 2.25rem;
            align-items: center;
        }

        .home-hero-logo {
            width: 280px;
            max-width: 100%;
            height: auto;
            margin-bottom: 0.875rem;
        }

        .home-hero-title {
            margin: 0;
            max-width: 640px;
        }

        .home-hero-description {
            margin-top: 1rem;
            max-width: 620px;
        }

        .home-hero-cta {
            margin-top: 1.75rem;
        }

        .home-hero-side {
            display: flex;
            justify-content: center;
        }

        .home-hero-main-theme {
            width: 100%;
            max-width: 520px;
        }

        .home-hero-fallback-image {
            max-width: 520px;
            max-height: 420px;
            width: 100%;
            object-fit: contain;
        }

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

        @media (min-width: 768px) {
            .home-hero-section {
                padding: 3.75rem 2rem 4rem;
            }
        }

        @media (min-width: 1024px) {
            .home-hero-grid {
                grid-template-columns: minmax(0, 1fr) minmax(0, 520px);
                gap: 2.75rem;
            }
        }
    </style>

    <section class="hero-observe home-hero-section relative overflow-hidden bg-zinc-100">
        <div class="home-hero-grid">
            <div>
                <img src="{{ url('/logo-without-text.svg') }}" alt="CultBear" width="350" height="345" class="home-hero-logo block">
                <h1 class="home-hero-title text-4xl font-black leading-tight md:text-5xl">Cultbear - патриотизм начинается с тебя</h1>
                <p class="home-hero-description text-zinc-700">Современный каталог с акцентом на черные модели, качественную печать и удобный заказ онлайн.</p>
                <a href="/catalog" class="home-hero-cta inline-block rounded bg-black px-6 py-3 text-sm font-semibold text-white">
                    Перейти в каталог
                </a>
            </div>
            <div class="home-hero-side">
                @if($homeTheme)
                    <a href="/themes/{{ $homeTheme->slug }}" class="home-card home-hero-main-theme">
                        <div class="home-card-media">
                            @if($homeTheme->banner_src)
                                <img src="{{ $homeTheme->banner_src }}" alt="{{ $homeTheme->name }}" class="aspect-[16/10] w-full object-cover">
                            @else
                                <div class="aspect-[16/10] w-full bg-zinc-200"></div>
                            @endif
                        </div>
                        <h3 class="home-card-title">{{ $homeTheme->name }}</h3>
                        <p class="home-card-description">{{ $homeTheme->description ?? 'Главная тематика CultBear.' }}</p>
                    </a>
                @else
                    <img src="{{ url('/t-shirt.png') }}" alt="Футболка CultBear" class="home-hero-fallback-image">
                @endif
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
