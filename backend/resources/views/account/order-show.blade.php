@extends('layouts.app', ['title' => ($order->number ?? 'Заказ').' — CultBear'])

@section('content')
<section class="mx-auto w-full max-w-5xl px-4 py-12 md:py-16">
    <a href="/account/orders" class="text-sm underline">Назад к списку заказов</a>

    <h1 class="mt-4 text-3xl font-black">Заказ {{ $order->number }}</h1>

    <div class="mt-6 grid gap-4 md:grid-cols-2">
        <div class="rounded-2xl border border-zinc-200 bg-white p-5">
            <h2 class="font-semibold">Статусы</h2>
            <p class="mt-2 text-sm">Заказ: <strong>{{ $order->status }}</strong></p>
            <p class="text-sm">Оплата: <strong>{{ $order->payment_status }}</strong></p>
            <p class="text-sm">Оплачен: <strong>{{ $order->paid_at?->format('d.m.Y H:i') ?? '—' }}</strong></p>
        </div>
        <div class="rounded-2xl border border-zinc-200 bg-white p-5">
            <h2 class="font-semibold">Доставка</h2>
            <p class="mt-2 text-sm">{{ $order->address_line ?: '—' }}</p>
            <p class="text-sm">{{ $order->city ?: '' }} {{ $order->postal_code ?: '' }}</p>
            <p class="mt-2 text-sm">{{ $order->customer_name }}</p>
            <p class="text-sm">{{ $order->email }} | {{ $order->phone }}</p>
        </div>
    </div>

    <div class="mt-6 rounded-2xl border border-zinc-200 bg-white p-5">
        <h2 class="font-semibold">Состав заказа</h2>
        <div class="mt-3 space-y-2">
            @foreach($order->items as $item)
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-zinc-100 pb-2">
                    <p class="text-sm">{{ $item->product_name }} ({{ $item->sku_variant }})</p>
                    <p class="text-sm">{{ $item->quantity }} × {{ number_format((int)$item->unit_price, 0, '.', ' ') }} ₽ = <strong>{{ number_format((int)$item->line_total, 0, '.', ' ') }} ₽</strong></p>
                </div>
            @endforeach
        </div>
        <p class="mt-4 text-right text-lg font-semibold">Итого: {{ number_format((int)$order->total_amount, 0, '.', ' ') }} ₽</p>
    </div>
</section>
@endsection
