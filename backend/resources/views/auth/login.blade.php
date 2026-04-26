@extends('layouts.app', ['title' => 'Вход — CultBear'])

@section('content')
<section class="mx-auto w-full max-w-7xl px-4 py-12 md:py-16">
<div class="w-full rounded-2xl border border-zinc-200 bg-white p-6 md:p-8" style="max-width: 28rem;">
    <h1 class="text-2xl font-semibold text-zinc-900">Вход</h1>
    <p class="mt-2 text-sm text-zinc-600">Нет аккаунта? <a href="{{ route('register') }}" class="underline">Регистрация</a></p>

    @if (session('status'))
        <p class="mt-4 rounded border border-zinc-200 bg-zinc-50 p-3 text-sm text-zinc-800">{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-4">
        @csrf
        <div>
            <label for="email" class="block text-sm font-medium text-zinc-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="mt-1 w-full rounded border border-zinc-300 px-3 py-2 text-sm focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 @error('email') border-red-500 @enderror">
            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-zinc-700">Пароль</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="mt-1 w-full rounded border border-zinc-300 px-3 py-2 text-sm focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 @error('password') border-red-500 @enderror">
            @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <label class="flex items-center gap-2 text-sm text-zinc-700">
            <input type="checkbox" name="remember" class="rounded border-zinc-300">
            Запомнить меня
        </label>
        <button type="submit" class="w-full rounded bg-black px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-800">Войти</button>
    </form>
    <p class="mt-6 text-center text-sm text-zinc-600">
        <a href="{{ route('password.request') }}" class="underline">Забыли пароль?</a>
    </p>
</div>
</section>
@endsection
