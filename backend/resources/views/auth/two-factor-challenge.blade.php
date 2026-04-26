@extends('layouts.app', ['title' => 'Двухфакторная аутентификация — CultBear'])

@section('content')
<div class="mx-auto max-w-md px-4 py-12">
    <h1 class="text-2xl font-semibold text-zinc-900">Подтверждение входа</h1>
    <p class="mt-2 text-sm text-zinc-600">Введите код из приложения или один резервный код.</p>

    <form method="POST" action="{{ route('two-factor.login.store') }}" class="mt-8 space-y-4">
        @csrf
        <div>
            <label for="code" class="block text-sm font-medium text-zinc-700">Код аутентификатора</label>
            <input id="code" type="text" name="code" inputmode="numeric" autofocus autocomplete="one-time-code"
                class="mt-1 w-full rounded border border-zinc-300 px-3 py-2 text-sm focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 @error('code') border-red-500 @enderror">
            @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="recovery_code" class="block text-sm font-medium text-zinc-700">Резервный код</label>
            <input id="recovery_code" type="text" name="recovery_code" autocomplete="one-time-code"
                class="mt-1 w-full rounded border border-zinc-300 px-3 py-2 text-sm focus:border-zinc-900 focus:outline-none focus:ring-1 focus:ring-zinc-900 @error('recovery_code') border-red-500 @enderror">
            @error('recovery_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="w-full rounded bg-black px-4 py-2.5 text-sm font-medium text-white hover:bg-zinc-800">Продолжить</button>
    </form>
</div>
@endsection
