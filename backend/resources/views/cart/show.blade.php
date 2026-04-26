@extends('layouts.app', ['title' => 'Корзина — CultBear'])

@section('content')
<section class="mx-auto w-full max-w-7xl px-4 py-12 md:py-16">
    <h1 class="text-3xl font-black">Корзина</h1>

    @if($items->isEmpty())
        <div class="mt-6 rounded-2xl border border-zinc-200 bg-white p-6 md:p-8">
            <p class="text-zinc-600">В корзине пока ничего нет.</p>
            <a href="/" class="mt-4 inline-block rounded bg-black px-5 py-2.5 text-sm font-semibold text-white">Перейти в каталог</a>
        </div>
    @else
        <div class="mt-8 space-y-4">
            @foreach($items as $item)
                <article class="rounded-2xl border border-zinc-200 bg-white p-5">
                    <p class="text-xs text-zinc-500">{{ $item->variant->product->article ?? '—' }}</p>
                    <h3 class="mt-1 font-semibold">{{ $item->variant->product->name ?? 'Товар' }}</h3>
                    <p class="mt-1 text-sm text-zinc-600">
                        Вариант: {{ $item->variant->model ?? '-' }} / {{ $item->variant->size ?? '-' }} / {{ $item->variant->color ?? '-' }}
                    </p>
                    <p class="mt-2 text-sm">Количество: <strong>{{ $item->quantity }}</strong></p>
                    <p class="text-sm">Цена за штуку: <strong>{{ number_format((int)$item->unit_price, 0, '.', ' ') }} ₽</strong></p>
                    <p class="mt-1 text-sm font-semibold">Сумма: {{ number_format((int)$item->quantity * (int)$item->unit_price, 0, '.', ' ') }} ₽</p>
                </article>
            @endforeach
        </div>

        <div class="mt-8 rounded-2xl border border-zinc-200 bg-white p-5">
            <p class="text-lg font-semibold">Итого: {{ number_format((int)$total, 0, '.', ' ') }} ₽</p>
            <p class="mt-2 text-sm text-zinc-600">Для оформления заказа перейдите к форме на странице товара (кнопка добавления в корзину и checkout API уже подключены в бэкенде).</p>
        </div>
    @endif
</section>
@endsection
