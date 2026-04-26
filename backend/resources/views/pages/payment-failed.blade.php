@extends('layouts.app', ['title' => 'Оплата не прошла — CultBear'])

@section('content')
<section class="mx-auto max-w-4xl px-4 py-12">
    <h1 class="text-3xl font-black text-red-900">Оплата не завершена</h1>
    <p class="mt-4 text-zinc-700">
        Платёж не был проведён или был отменён. Деньги с карты не списались. Вы можете оформить заказ заново из корзины или выбрать другой способ оплаты.
    </p>
    <div class="mt-8 flex flex-wrap gap-4">
        <a href="{{ url('/cart') }}" class="inline-flex rounded-lg bg-black px-5 py-2.5 text-sm font-medium text-white hover:bg-zinc-800">
            Вернуться в корзину
        </a>
        <a href="{{ url('/contacts') }}" class="inline-flex rounded-lg border border-zinc-300 px-5 py-2.5 text-sm font-medium text-zinc-900 hover:bg-zinc-50">
            Связаться с нами
        </a>
    </div>
</section>
@endsection
