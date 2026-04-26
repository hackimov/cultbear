@extends('layouts.app', ['title' => 'Контакты — CultBear'])

@section('content')
<section class="mx-auto max-w-4xl px-4 py-12">
    <h1 class="text-3xl font-black">Контакты</h1>
    <p class="mt-4 text-zinc-700">Email: {{ $legal['email'] ?? 'info@cultbear.local' }}</p>
    <p class="text-zinc-700">Телефон: {{ $legal['phone'] ?? '+7 (999) 000-00-00' }}</p>
    <p class="mt-2 text-zinc-700">Юридический адрес: {{ $legal['legal_address'] ?? 'не указан' }}</p>
</section>
@endsection
