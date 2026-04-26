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
                <a href="/themes/{{ $theme->slug }}" class="rounded-xl border border-zinc-200 p-5 hover:border-black">
                    <h3 class="font-semibold">{{ $theme->name }}</h3>
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
                <a href="/products/{{ $product->slug }}" class="rounded-xl border border-zinc-200 p-4">
                    <p class="text-xs text-zinc-500">{{ $product->article }}</p>
                    <h3 class="mt-1 font-semibold">{{ $product->name }}</h3>
                    <p class="mt-3 text-sm font-bold">{{ number_format($product->base_price, 0, '.', ' ') }} ₽</p>
                </a>
            @endforeach
        </div>
    </section>
@endsection
