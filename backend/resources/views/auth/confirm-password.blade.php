@extends('layouts.app', ['title' => 'Подтверждение пароля — CultBear'])

@section('content')
<div class="mx-auto max-w-md px-4 py-12">
    <h1 class="text-2xl font-semibold text-zinc-900">Подтвердите пароль</h1>
    <p class="mt-2 text-sm text-zinc-600">Для безопасности введите текущий пароль ещё раз.</p>

    <form method="POST" action="{{ route('password.confirm.store') }}" class="mt-8 space-y-4">
        @csrf
        <div>
            <label for="password" class="block text-sm font-medium text-zinc-700">Пароль</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="mt-1 w-full rounded border border-zinc-300 px-3 py-2 text-sm focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 @error('password') border-red-500 @enderror">
            @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="w-full rounded bg-black px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-800">Подтвердить</button>
    </form>
</div>
@endsection
