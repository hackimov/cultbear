@extends('layouts.app', ['title' => 'Пользовательское соглашение — CultBear'])

@section('content')
<section class="mx-auto max-w-4xl px-4 py-12">
    <h1 class="text-3xl font-black">Пользовательское соглашение</h1>
    <p class="mt-4 text-zinc-700">Оформляя заказ, пользователь подтверждает достоверность данных и согласие с условиями публичной оферты, доставки, возврата и обработки персональных данных.</p>
    <h2 class="mt-6 text-xl font-semibold">Обязанности пользователя</h2>
    <ul class="mt-2 list-disc pl-6 text-zinc-700">
        <li>предоставлять корректные контактные данные и адрес доставки;</li>
        <li>своевременно принимать и оплачивать заказ;</li>
        <li>не использовать сайт в противоправных целях.</li>
    </ul>
    <h2 class="mt-6 text-xl font-semibold">Обязанности продавца</h2>
    <ul class="mt-2 list-disc pl-6 text-zinc-700">
        <li>передать товар надлежащего качества;</li>
        <li>обновлять статусы заказа и уведомлять об изменениях;</li>
        <li>соблюдать требования законодательства РФ.</li>
    </ul>
    <p class="mt-4 text-zinc-700">Реквизиты продавца: {{ $legal['company_name'] ?? 'CultBear' }}.</p>
</section>
@endsection
