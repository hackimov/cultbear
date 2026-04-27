@extends('layouts.app', ['title' => 'CultBear — Каталог'])

@section('content')
    <style>
        .catalog-toolbar {
            display: grid;
            gap: 1rem;
            margin-top: 1.25rem;
        }

        .catalog-search-input {
            width: 100%;
            border: 1px solid #d4d4d8;
            border-radius: 0.75rem;
            padding: 0.75rem 0.95rem;
            font-size: 0.9375rem;
            line-height: 1.3;
        }

        .catalog-search-row {
            display: flex;
            gap: 0.625rem;
        }

        .catalog-search-button {
            border: 1px solid #18181b;
            border-radius: 0.75rem;
            background: #18181b;
            color: #fff;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            line-height: 1.2;
            font-weight: 600;
            white-space: nowrap;
        }

        .catalog-search-input:focus {
            outline: none;
            border-color: #18181b;
            box-shadow: 0 0 0 1px #18181b;
        }

        .catalog-chip-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.625rem;
        }

        .catalog-chip {
            display: inline-flex;
            align-items: center;
            border: 1px solid #d4d4d8;
            border-radius: 999px;
            background: #fff;
            color: #18181b;
            padding: 0.5rem 0.9rem;
            font-size: 0.875rem;
            line-height: 1.2;
            transition: border-color .15s ease, background-color .15s ease, color .15s ease;
        }

        .catalog-chip:hover {
            border-color: #18181b;
        }

        .catalog-chip.is-active {
            border-color: #18181b;
            background: #18181b;
            color: #fff;
        }

        .catalog-cards-grid {
            margin-top: 1.5rem;
        }

        .catalog-product-card {
            display: block;
            overflow: hidden;
            border: 1px solid #e4e4e7;
            border-radius: 0.75rem;
            background: #fff;
            padding: 0.75rem;
        }

        .catalog-product-card:hover {
            border-color: #18181b;
        }

        .catalog-product-media {
            overflow: hidden;
            border-radius: 0.625rem;
            background: #f4f4f5;
            margin-bottom: 0.75rem;
        }

        .catalog-product-article {
            margin: 0;
            font-size: 0.75rem;
            line-height: 1.2;
            color: #71717a;
        }

        .catalog-product-title {
            margin: 0.25rem 0 0;
            font-size: 1rem;
            line-height: 1.3;
            font-weight: 600;
            color: #18181b;
        }

        .catalog-product-price {
            margin-top: 0.625rem;
            font-size: 0.9375rem;
            line-height: 1.25;
            font-weight: 700;
            color: #18181b;
        }

        .catalog-pagination-wrap {
            margin-top: 1.5rem;
        }
    </style>

    <section class="mx-auto max-w-7xl px-4 py-10">
        <h1 class="text-3xl font-black">Каталог</h1>
        <p class="mt-2 text-zinc-600">Ищите по названию или артикулу и фильтруйте товары по тематикам.</p>

        <form method="GET" action="/catalog" class="catalog-toolbar">
            @if($selectedThemeSlug !== '')
                <input type="hidden" name="theme" value="{{ $selectedThemeSlug }}">
            @endif
            <div class="catalog-search-row">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Поиск по названию или артикулу"
                    class="catalog-search-input"
                >
                <button type="submit" class="catalog-search-button">Найти</button>
            </div>

            <div class="catalog-chip-list">
                <a href="/catalog{{ request('search') ? '?search='.urlencode((string) request('search')) : '' }}" class="catalog-chip {{ $selectedThemeSlug === '' ? 'is-active' : '' }}">
                    Все тематики
                </a>
                @foreach($themes as $theme)
                    <a
                        href="/catalog?{{ http_build_query(array_filter([
                            'search' => (string) request('search'),
                            'theme' => $theme->slug,
                        ])) }}"
                        class="catalog-chip {{ $selectedThemeSlug === $theme->slug ? 'is-active' : '' }}"
                    >
                        {{ $theme->name }}
                    </a>
                @endforeach
            </div>
        </form>

        <div class="catalog-cards-grid grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @forelse($products as $product)
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

                <a href="/products/{{ $product->slug }}" class="catalog-product-card">
                    <div class="catalog-product-media">
                        @if($productThumbUrl)
                            <img src="{{ $productThumbUrl }}" alt="{{ $product->name }}" class="aspect-square w-full object-cover">
                        @else
                            <div class="aspect-square w-full bg-zinc-200"></div>
                        @endif
                    </div>
                    <p class="catalog-product-article">{{ $product->article }}</p>
                    <h3 class="catalog-product-title">{{ $product->name }}</h3>
                    <p class="catalog-product-price">{{ number_format($product->base_price, 0, '.', ' ') }} ₽</p>
                </a>
            @empty
                <p class="text-zinc-600">По вашему запросу товары не найдены.</p>
            @endforelse
        </div>

        <div class="catalog-pagination-wrap">
            {{ $products->links() }}
        </div>
    </section>
@endsection
