@extends('layouts.app', ['title' => $product->name.' — CultBear'])

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10">
        <div class="grid gap-8 md:grid-cols-2">
            <div class="rounded-2xl bg-zinc-100 p-8">
                <div class="aspect-square rounded-xl bg-zinc-200"></div>
            </div>
            <div>
                <p class="text-xs text-zinc-500">{{ $product->article }}</p>
                <h1 class="mt-2 text-3xl font-black">{{ $product->name }}</h1>
                <p class="mt-4 text-zinc-700">{{ $product->description }}</p>

                <div class="mt-6 space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Вариант</label>
                        <select class="w-full rounded border border-zinc-300 px-3 py-2">
                            @foreach($product->variants as $variant)
                                <option>
                                    {{ $variant->model }} / {{ $variant->size }} / {{ $variant->color }} — {{ number_format($variant->price, 0, '.', ' ') }} ₽ ({{ $variant->stock_quantity }} шт.)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button class="rounded bg-black px-6 py-3 text-sm font-semibold text-white">Добавить в корзину</button>
                </div>
            </div>
        </div>
    </section>
@endsection
