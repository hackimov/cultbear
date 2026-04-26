@extends('layouts.app', ['title' => 'Мои заказы — CultBear'])

@section('content')
<section class="mx-auto w-full max-w-5xl px-4 py-12 md:py-16">
    <h1 class="text-3xl font-black">Мои заказы</h1>

    @if($orders->isEmpty())
        <div class="mt-6 rounded-2xl border border-zinc-200 bg-white p-6 md:p-8">
            <p class="text-zinc-600">У вас пока нет заказов.</p>
            <a href="/" class="mt-4 inline-block rounded bg-black px-5 py-2.5 text-sm font-semibold text-white">Перейти в каталог</a>
        </div>
    @else
        <div class="mt-8 space-y-3">
            @foreach($orders as $order)
                <a href="/account/orders/{{ $order->id }}" class="block rounded-2xl border border-zinc-200 bg-white p-5 hover:border-black">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <p class="font-semibold">{{ $order->number }}</p>
                        <p class="text-sm text-zinc-600">{{ $order->created_at?->format('d.m.Y H:i') }}</p>
                    </div>
                    <p class="mt-2 text-sm">Статус: <strong>{{ $order->status }}</strong> | Оплата: <strong>{{ $order->payment_status }}</strong></p>
                    <p class="mt-1 text-sm">Сумма: <strong>{{ number_format((int)$order->total_amount, 0, '.', ' ') }} ₽</strong></p>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif
</section>
@endsection
