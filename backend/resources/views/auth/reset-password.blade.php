@extends('layouts.app', ['title' => 'Новый пароль — CultBear'])

@section('content')
<section class="mx-auto w-full max-w-7xl px-4 py-12 md:py-16">
<div class="mx-auto w-full rounded-2xl border border-zinc-200 bg-white p-6 md:p-8" style="max-width: 28rem;">
    <h1 class="text-2xl font-semibold text-zinc-900">Новый пароль</h1>

    <p class="mt-2 text-sm text-zinc-600">Аккаунт: <strong>{{ old('email', $request->email) }}</strong></p>

    <form method="POST" action="{{ route('password.update') }}" class="mt-8 space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <input type="hidden" name="email" value="{{ old('email', $request->email) }}">
        @error('email')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        <div>
            <label for="password" class="block text-sm font-medium text-zinc-700">Новый пароль</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="mt-1 w-full rounded border border-zinc-300 px-3 py-2 text-sm focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 @error('password') border-red-500 @enderror">
            @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-zinc-700">Повтор пароля</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="mt-1 w-full rounded border border-zinc-300 px-3 py-2 text-sm focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900">
        </div>
        <button type="submit" class="w-full rounded bg-black px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-800">Сохранить пароль</button>
    </form>
</div>
</section>
@endsection
