@extends('layouts.app', ['title' => 'Регистрация — CultBear'])

@section('content')
<section class="mx-auto w-full max-w-7xl px-4 py-12 md:py-16">
<div class="mx-auto w-full rounded-2xl border border-zinc-200 bg-white p-6 md:p-8" style="max-width: 28rem;">
    <h1 class="text-2xl font-semibold text-zinc-900">Регистрация</h1>
    <p class="mt-2 text-sm text-zinc-600">Уже есть аккаунт? <a href="{{ route('login') }}" class="underline">Вход</a></p>

    <form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-4">
        @csrf
        <div>
            <label for="name" class="block text-sm font-medium text-zinc-700">Имя</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                class="mt-1 w-full rounded border border-zinc-300 px-3 py-2 text-sm focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 @error('name') border-red-500 @enderror">
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="email" class="block text-sm font-medium text-zinc-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                class="mt-1 w-full rounded border border-zinc-300 px-3 py-2 text-sm focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 @error('email') border-red-500 @enderror">
            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-zinc-700">Пароль</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="mt-1 w-full rounded border border-zinc-300 px-3 py-2 text-sm focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 @error('password') border-red-500 @enderror">
            @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-zinc-700">Повтор пароля</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="mt-1 w-full rounded border border-zinc-300 px-3 py-2 text-sm focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
        </div>
        <label class="flex items-start gap-2 text-sm text-zinc-700">
            <input
                type="checkbox"
                name="personal_data_consent"
                value="1"
                required
                @checked(old('personal_data_consent'))
                class="mt-0.5 h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900"
            >
            <span>
                Я даю согласие на обработку персональных данных и принимаю
                <a href="/privacy-policy" class="underline">Политику конфиденциальности</a> и
                <a href="/personal-data-policy" class="underline">Политику обработки персональных данных</a>.
            </span>
        </label>
        @error('personal_data_consent')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        <button type="submit" class="w-full rounded bg-black px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-800">Зарегистрироваться</button>
    </form>
</div>
</section>
@endsection
