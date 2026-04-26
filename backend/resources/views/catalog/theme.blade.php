@extends('layouts.app', ['title' => $theme->name.' — CultBear'])

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10">
        <h1 class="text-3xl font-black">{{ $theme->name }}</h1>
        <p class="mt-2 text-zinc-600">{{ $theme->description }}</p>
        @if($theme->banner_src)
            <div class="mt-6 overflow-hidden rounded-xl border border-zinc-200">
                <img src="{{ $theme->banner_src }}" alt="{{ $theme->name }}" class="h-48 w-full object-cover md:h-64">
            </div>
        @endif

        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-{{ max(2, min(4, (int)$theme->layout_columns)) }}">
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
