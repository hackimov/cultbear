@extends('layouts.app', ['title' => 'Оплата прошла — CultBear'])

@section('content')
<section class="mx-auto max-w-4xl px-4 py-12">
    <h1 class="text-3xl font-black text-emerald-800">Оплата успешно принята</h1>
    <p class="mt-4 text-zinc-700">
        Спасибо за заказ. Мы получили оплату и начнём обработку. Статус заказа и детали вы можете посмотреть в личном кабинете.
    </p>
    <div class="mt-8 flex flex-wrap gap-4">
        <a href="{{ url('/account/orders') }}" class="inline-flex rounded-lg bg-black px-5 py-2.5 text-sm font-medium text-white hover:bg-zinc-800">
            Мои заказы
        </a>
        <a href="{{ url('/') }}" class="inline-flex rounded-lg border border-zinc-300 px-5 py-2.5 text-sm font-medium text-zinc-900 hover:bg-zinc-50">
            На главную
        </a>
    </div>
</section>
@endsection
