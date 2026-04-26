@extends('layouts.app', ['title' => 'Политика конфиденциальности — CultBear'])

@section('content')
<section class="mx-auto max-w-4xl px-4 py-12">
    <h1 class="text-3xl font-black">Политика конфиденциальности</h1>
    <p class="mt-4 text-zinc-700">Оператор персональных данных: {{ $legal['company_name'] ?? 'CultBear' }}, ИНН {{ $legal['inn'] ?? '-' }}.</p>
    <p class="mt-2 text-zinc-700">Мы обрабатываем персональные данные в соответствии с 152-ФЗ исключительно для оформления заказа, доставки, оплаты и клиентской поддержки.</p>
    <h2 class="mt-6 text-xl font-semibold">Какие данные собираем</h2>
    <ul class="mt-2 list-disc pl-6 text-zinc-700">
        <li>ФИО, телефон, email, адрес доставки;</li>
        <li>данные по заказу и статусам оплаты;</li>
        <li>технические cookies для авторизации и корзины.</li>
    </ul>
    <h2 class="mt-6 text-xl font-semibold">Права пользователя</h2>
    <p class="mt-2 text-zinc-700">Пользователь вправе запросить уточнение, изменение или удаление своих данных, написав на {{ $legal['email'] ?? 'info@cultbear.local' }}.</p>
</section>
@endsection
