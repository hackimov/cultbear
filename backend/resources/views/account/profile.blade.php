@extends('layouts.app', ['title' => 'Профиль — CultBear'])

@section('content')
<section class="mx-auto w-full max-w-3xl px-4 py-10 md:py-14">
    <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm md:p-7">
        <h1 class="text-2xl font-black md:text-3xl">Профиль</h1>
        <p class="mt-2 text-sm text-zinc-600">Измените имя и адрес доставки, чтобы оформление заказа занимало меньше времени.</p>

        @if(session('status'))
            <p class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('status') }}</p>
        @endif

        @if($errors->any())
            <p class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="/account/profile" class="mt-6 space-y-4">
            @csrf
            @method('PUT')

            <div class="space-y-1.5">
                <label for="name" class="block text-sm font-semibold">Имя</label>
                <input id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-sm">
            </div>

            <div class="space-y-1.5">
                <label for="phone" class="block text-sm font-semibold">Телефон</label>
                <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+7 (___) ___-__-__" class="w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-sm">
            </div>

            <div class="space-y-1.5">
                <label for="address_line" class="block text-sm font-semibold">Адрес доставки</label>
                <input id="address_line" name="address_line" value="{{ old('address_line', $user->address_line) }}" placeholder="Улица, дом, квартира" class="w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-sm">
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div class="space-y-1.5">
                    <label for="city" class="block text-sm font-semibold">Город</label>
                    <input id="city" name="city" value="{{ old('city', $user->city) }}" class="w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-sm">
                </div>

                <div class="space-y-1.5">
                    <label for="postal_code" class="block text-sm font-semibold">Индекс</label>
                    <input id="postal_code" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" class="w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-sm">
                </div>
            </div>

            <button type="submit" class="w-full rounded-xl bg-black px-6 py-3 text-sm font-semibold text-white hover:bg-zinc-800 sm:w-auto">
                Сохранить изменения
            </button>
        </form>
    </div>
</section>
@endsection
