<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'CultBear' }}</title>
    <meta name="description" content="{{ $description ?? 'Интернет-магазин футболок с патриотической символикой.' }}">
    <meta property="og:title" content="{{ $title ?? 'CultBear' }}">
    <meta property="og:description" content="{{ $description ?? 'Интернет-магазин футболок с патриотической символикой.' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="flex min-h-screen flex-col bg-white text-zinc-900" x-data="{mobileMenu:false}">
    <header class="border-b border-zinc-200">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4">
            <a href="/" class="inline-flex h-10 w-[172px] shrink-0 items-center" aria-label="CultBear">
                <img src="{{ url('/logo-header.svg') }}" alt="CultBear" width="172" height="40" class="block h-10 w-[172px]">
            </a>
            <nav class="hidden items-center gap-5 text-sm md:flex">
                <a href="/">Главная</a>
                <a href="/about">О нас</a>
                <a href="/delivery">Доставка</a>
                <a href="/contacts">Контакты</a>
                <a href="/cart">Корзина</a>
                <a href="/account/orders">Профиль</a>
            </nav>
            <button class="md:hidden" @click="mobileMenu = !mobileMenu" aria-label="Открыть меню">
                <span class="block h-0.5 w-6 bg-black mb-1 transition"></span>
                <span class="block h-0.5 w-6 bg-black mb-1 transition"></span>
                <span class="block h-0.5 w-6 bg-black transition"></span>
            </button>
        </div>
        <div x-show="mobileMenu" x-transition class="border-t border-zinc-200 md:hidden">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-4 text-sm">
                <a href="/">Главная</a>
                <a href="/about">О нас</a>
                <a href="/delivery">Доставка</a>
                <a href="/contacts">Контакты</a>
                <a href="/cart">Корзина</a>
                <a href="/account/orders">Профиль</a>
            </div>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="border-t border-zinc-200 bg-zinc-50">
        <div class="mx-auto max-w-7xl px-4 py-8 text-sm text-zinc-700">
            <p class="font-semibold">{{ $legal['company_name'] ?? 'CultBear' }}</p>
            <p>ИНН: {{ $legal['inn'] ?? '-' }} | ОГРН: {{ $legal['ogrn'] ?? '-' }}</p>
            <p>{{ $legal['legal_address'] ?? 'Адрес будет указан после заполнения настроек.' }}</p>
            <p>{{ $legal['email'] ?? 'info@cultbear.local' }} | {{ $legal['phone'] ?? '+7 (999) 000-00-00' }}</p>
            <div class="mt-2 flex gap-4">
                <a href="/privacy-policy">Политика конфиденциальности</a>
                <a href="/terms">Пользовательское соглашение</a>
            </div>
        </div>
    </footer>

    <div x-data="{ accepted: localStorage.getItem('cookieAccepted') === '1' }" x-show="!accepted" class="fixed bottom-4 left-4 right-4 z-50 rounded-lg border border-zinc-300 bg-white p-4 shadow-lg">
        <p class="text-sm">Мы используем cookie для корректной работы сайта.</p>
        <button class="mt-3 rounded bg-black px-4 py-2 text-sm text-white" @click="localStorage.setItem('cookieAccepted','1'); accepted = true">
            Принять
        </button>
    </div>
</body>
</html>
