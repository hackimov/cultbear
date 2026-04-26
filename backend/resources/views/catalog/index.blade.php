@extends('layouts.app', ['title' => 'CultBear — Главная'])

@section('content')
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
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($themes as $theme)
                <a href="/themes/{{ $theme->slug }}" class="overflow-hidden rounded-xl border border-zinc-200 bg-white p-3 hover:border-black">
                    <div class="overflow-hidden rounded-lg bg-zinc-100">
                        @if($theme->banner_src)
                            <img src="{{ $theme->banner_src }}" alt="{{ $theme->name }}" class="aspect-[16/10] w-full object-cover">
                        @else
                            <div class="aspect-[16/10] w-full bg-zinc-200"></div>
                        @endif
                    </div>
                    <h3 class="mt-3 font-semibold">{{ $theme->name }}</h3>
                    <p class="mt-2 text-sm text-zinc-600">{{ $theme->description ?? 'Тематическая подборка товаров.' }}</p>
                </a>
            @empty
                <p class="text-zinc-600">Тематики пока не добавлены.</p>
            @endforelse
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 pb-14">
        <h2 class="text-2xl font-bold">Хиты продаж и новинки</h2>
        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
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

                <a href="/products/{{ $product->slug }}" class="overflow-hidden rounded-xl border border-zinc-200 bg-white p-3 hover:border-black">
                    <div class="overflow-hidden rounded-lg bg-zinc-100">
                        @if($productThumbUrl)
                            <img src="{{ $productThumbUrl }}" alt="{{ $product->name }}" class="aspect-square w-full object-cover">
                        @else
                            <div class="aspect-square w-full bg-zinc-200"></div>
                        @endif
                    </div>
                    <p class="mt-3 text-xs text-zinc-500">{{ $product->article }}</p>
                    <h3 class="mt-1 font-semibold">{{ $product->name }}</h3>
                    <p class="mt-3 text-sm font-bold">{{ number_format($product->base_price, 0, '.', ' ') }} ₽</p>
                </a>
            @endforeach
        </div>
    </section>
@endsection
