@extends('layouts.app', ['title' => 'Восстановление пароля — CultBear'])

@section('content')
<section class="mx-auto w-full max-w-7xl px-4 py-12 md:py-16">
<div class="w-full rounded-2xl border border-zinc-200 bg-white p-6 md:p-8" style="max-width: 28rem;">
    <h1 class="text-2xl font-semibold text-zinc-900">Сброс пароля</h1>
    <p class="mt-2 text-sm text-zinc-600">Укажите email — отправим ссылку для нового пароля.</p>

    @if (session('status'))
        <p class="mt-4 rounded border border-zinc-200 bg-zinc-50 p-3 text-sm text-zinc-800">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-zinc-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="mt-1 w-full rounded border border-zinc-300 px-3 py-2 text-sm focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 @error('email') border-red-500 @enderror">
            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="w-full rounded bg-black px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-800">Отправить ссылку</button>
    </form>
    <p class="mt-6 text-center text-sm">
        <a href="{{ route('login') }}" class="text-zinc-600 underline">Назад ко входу</a>
    </p>
</div>
</section>
@endsection
