@extends('layouts.app', ['title' => 'Корзина — CultBear'])

@section('content')
<style>
    .checkout-layout {
        display: grid;
        gap: 1.25rem;
    }

    .checkout-card {
        border: 1px solid #e4e4e7;
        border-radius: 1rem;
        background: #fff;
        padding: 1rem;
    }

    .checkout-item {
        border: 1px solid #e4e4e7;
        border-radius: 1rem;
        background: #fff;
        padding: 1rem;
    }

    .checkout-item + .checkout-item {
        margin-top: 0.75rem;
    }

    .checkout-item-grid {
        display: grid;
        gap: 0.875rem;
    }

    .checkout-qty-controls {
        display: inline-flex;
        align-items: center;
        border: 1px solid #d4d4d8;
        border-radius: 0.75rem;
        overflow: hidden;
    }

    .checkout-qty-btn {
        padding: 0.45rem 0.75rem;
        font-size: 1rem;
        line-height: 1;
    }

    .checkout-qty-input {
        width: 3rem;
        text-align: center;
        border-left: 1px solid #d4d4d8;
        border-right: 1px solid #d4d4d8;
        padding: 0.45rem 0.25rem;
        appearance: textfield;
        outline: none;
    }

    .checkout-qty-input::-webkit-outer-spin-button,
    .checkout-qty-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .checkout-form-grid {
        display: grid;
        gap: 0.875rem;
    }

    @media (min-width: 1024px) {
        .checkout-layout {
            grid-template-columns: minmax(0, 1fr) 380px;
            align-items: start;
        }

        .checkout-summary-sticky {
            position: sticky;
            top: 1.5rem;
        }
    }
</style>

<section class="mx-auto w-full max-w-7xl px-4 py-10 md:py-14">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-3xl font-black">Оформление заказа</h1>
        <a href="/" class="text-sm font-semibold text-zinc-600 underline">Продолжить покупки</a>
    </div>

    @if(session('status'))
        <p class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('status') }}</p>
    @endif
    @if(session('error'))
        <p class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ session('error') }}</p>
    @endif
    @if($errors->any())
        <p class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ $errors->first() }}</p>
    @endif

    @if($items->isEmpty())
        <div class="mt-6 rounded-2xl border border-zinc-200 bg-white p-6 md:p-8">
            <p class="text-zinc-600">В корзине пока ничего нет.</p>
            <a href="/" class="mt-4 inline-block rounded-xl bg-black px-5 py-2.5 text-sm font-semibold text-white">Перейти в каталог</a>
        </div>
    @else
        <div class="checkout-layout mt-6">
            <div>
                @foreach($items as $item)
                    <article class="checkout-item">
                        <div class="checkout-item-grid">
                            <div>
                                <p class="text-xs text-zinc-500">{{ $item->variant->product->article ?? '—' }}</p>
                                <h3 class="mt-1 text-lg font-semibold">{{ $item->variant->product->name ?? 'Товар' }}</h3>
                                <p class="mt-1 text-sm text-zinc-600">
                                    {{ $item->variant->model ?? '-' }} / {{ $item->variant->size ?? '-' }} / {{ $item->variant->color ?? '-' }}
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <p class="text-sm text-zinc-700">
                                    <span class="text-zinc-500">Цена:</span>
                                    <strong>{{ number_format((int)$item->unit_price, 0, '.', ' ') }} ₽</strong>
                                </p>
                                <p class="text-sm font-semibold">
                                    {{ number_format((int)$item->quantity * (int)$item->unit_price, 0, '.', ' ') }} ₽
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <form method="POST" action="/cart/items/{{ $item->id }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <span class="text-sm font-semibold">Количество</span>
                                    <div class="checkout-qty-controls">
                                        <button type="button" class="checkout-qty-btn" data-qty-minus>−</button>
                                        <input type="number" min="1" name="quantity" value="{{ $item->quantity }}" class="checkout-qty-input" data-qty-input>
                                        <button type="button" class="checkout-qty-btn" data-qty-plus>+</button>
                                    </div>
                                    <button type="submit" class="rounded-lg border border-zinc-300 px-3 py-1.5 text-xs font-semibold">Обновить</button>
                                </form>

                                <form method="POST" action="/cart/items/{{ $item->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700">
                                        Удалить
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <aside class="checkout-summary-sticky">
                <div class="checkout-card">
                    <p class="text-sm text-zinc-500">Итого к оплате</p>
                    <p class="mt-1 text-2xl font-black">{{ number_format((int) $total, 0, '.', ' ') }} ₽</p>
                </div>

                <form method="POST" action="/checkout" class="checkout-card mt-3">
                    @csrf
                    <h2 class="text-lg font-bold">Данные для доставки</h2>

                    <div class="checkout-form-grid mt-4">
                        <div>
                            <label for="checkout_name" class="mb-1 block text-sm font-semibold">Имя</label>
                            <input id="checkout_name" name="name" required value="{{ old('name', $user?->name) }}" class="w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-sm">
                        </div>

                        <div>
                            <label for="checkout_email" class="mb-1 block text-sm font-semibold">Email</label>
                            <input id="checkout_email" name="email" type="email" required value="{{ old('email', $user?->email) }}" class="w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-sm">
                        </div>

                        <div>
                            <label for="checkout_phone" class="mb-1 block text-sm font-semibold">Телефон</label>
                            <input id="checkout_phone" name="phone" required value="{{ old('phone', $user?->phone) }}" class="w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-sm">
                        </div>

                        <div>
                            <label for="checkout_address_line" class="mb-1 block text-sm font-semibold">Адрес доставки</label>
                            <input id="checkout_address_line" name="address_line" required value="{{ old('address_line', $user?->address_line) }}" placeholder="Улица, дом, квартира" class="w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-sm">
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label for="checkout_city" class="mb-1 block text-sm font-semibold">Город</label>
                                <input id="checkout_city" name="city" value="{{ old('city', $user?->city) }}" class="w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-sm">
                            </div>
                            <div>
                                <label for="checkout_postal_code" class="mb-1 block text-sm font-semibold">Индекс</label>
                                <input id="checkout_postal_code" name="postal_code" value="{{ old('postal_code', $user?->postal_code) }}" class="w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-sm">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="mt-5 w-full rounded-xl bg-black px-6 py-3 text-sm font-semibold text-white hover:bg-zinc-800">
                        Перейти к оплате
                    </button>
                </form>
            </aside>
        </div>
    @endif
</section>

<script>
    (() => {
        document.querySelectorAll('form').forEach((form) => {
            const qtyInput = form.querySelector('[data-qty-input]');
            const minusButton = form.querySelector('[data-qty-minus]');
            const plusButton = form.querySelector('[data-qty-plus]');
            if (!qtyInput || !minusButton || !plusButton) {
                return;
            }

            minusButton.addEventListener('click', () => {
                const current = Math.max(1, Number(qtyInput.value || 1) - 1);
                qtyInput.value = String(current);
            });

            plusButton.addEventListener('click', () => {
                const current = Math.max(1, Number(qtyInput.value || 1) + 1);
                qtyInput.value = String(current);
            });
        });
    })();
</script>
@endsection
