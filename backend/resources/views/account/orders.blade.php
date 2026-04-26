@extends('layouts.app', ['title' => 'Мои заказы — CultBear'])

@section('content')
<section class="mx-auto max-w-5xl px-4 py-12">
    <h1 class="text-3xl font-black">Мои заказы</h1>

    @if($orders->isEmpty())
        <p class="mt-6 text-zinc-600">У вас пока нет заказов.</p>
    @else
        <div class="mt-8 space-y-3">
            @foreach($orders as $order)
                <a href="/account/orders/{{ $order->id }}" class="block rounded-xl border border-zinc-200 p-4 hover:border-black">
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
