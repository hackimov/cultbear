@extends('layouts.app', ['title' => 'Контакты — CultBear'])

@section('content')
<section class="mx-auto w-full max-w-5xl px-4 py-12 md:py-16">
    <div class="rounded-2xl border border-zinc-200 bg-white p-6 md:p-8">
        <h1 class="text-3xl font-black">Контакты</h1>
        <dl class="mt-6 grid gap-4 text-zinc-700 sm:grid-cols-2">
            <div>
                <dt class="text-sm text-zinc-500">Email</dt>
                <dd class="mt-1 font-medium">{{ $legal['email'] ?? 'info@cultbear.local' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-zinc-500">Телефон</dt>
                <dd class="mt-1 font-medium">{{ $legal['phone'] ?? '+7 (999) 000-00-00' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm text-zinc-500">Юридический адрес</dt>
                <dd class="mt-1 font-medium">{{ $legal['legal_address'] ?? 'Не указан' }}</dd>
            </div>
        </dl>
    </div>
</section>
@endsection
