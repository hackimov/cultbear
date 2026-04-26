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

    .checkout-item-head {
        display: grid;
        grid-template-columns: 72px minmax(0, 1fr);
        gap: 0.875rem;
        align-items: start;
    }

    .checkout-item-thumb {
        width: 72px;
        height: 72px;
        border-radius: 0.75rem;
        overflow: hidden;
        background: #f4f4f5;
        border: 1px solid #e4e4e7;
        flex-shrink: 0;
    }

    .checkout-item-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .checkout-item-meta {
        min-width: 0;
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

    .checkout-address-wrap {
        position: relative;
    }

    .checkout-address-suggestions {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 0.35rem);
        z-index: 30;
        border: 1px solid #d4d4d8;
        border-radius: 0.75rem;
        background: #fff;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
        max-height: 260px;
        overflow: auto;
    }

    .checkout-hidden {
        display: none;
    }

    .checkout-address-suggestion {
        width: 100%;
        text-align: left;
        padding: 0.625rem 0.75rem;
        border: 0;
        background: #fff;
        font-size: 0.875rem;
        line-height: 1.25;
    }

    .checkout-address-suggestion + .checkout-address-suggestion {
        border-top: 1px solid #f4f4f5;
    }

    .checkout-address-suggestion:hover {
        background: #fafafa;
    }

    .checkout-submit-wrap {
        margin-top: 1.25rem;
    }

    .checkout-submit-btn {
        width: 100%;
        border-radius: 0.875rem;
        background: #000;
        color: #fff;
        padding: 0.75rem 1.5rem;
        font-size: 0.95rem;
        font-weight: 600;
        line-height: 1.2;
    }

    .checkout-submit-btn:hover {
        background: #27272a;
    }

    .checkout-delete-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 36px;
        padding: 0 0.75rem;
        border: 1px solid #fecaca;
        border-radius: 0.75rem;
        background: #fef2f2;
        color: #b91c1c;
    }

    .checkout-delete-btn svg {
        width: 18px;
        height: 18px;
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
                            <div class="checkout-item-head">
                                @php
                                    $productMedia = $item->variant?->product?->media
                                        ?->sortByDesc(fn ($media) => (bool) $media->is_primary)
                                        ->first();
                                    $thumbPath = $productMedia?->webp_path ?: $productMedia?->preview_path ?: $productMedia?->path;
                                    $thumbUrl = null;
                                    if ($thumbPath) {
                                        $thumbUrl = filter_var($thumbPath, FILTER_VALIDATE_URL)
                                            ? $thumbPath
                                            : \Illuminate\Support\Facades\Storage::disk($productMedia?->disk ?: 's3')->url($thumbPath);
                                    }
                                @endphp

                                <div class="checkout-item-thumb">
                                    @if($thumbUrl)
                                        <img src="{{ $thumbUrl }}" alt="{{ $item->variant->product->name ?? 'Товар' }}">
                                    @endif
                                </div>

                                <div class="checkout-item-meta">
                                    <p class="text-xs text-zinc-500">{{ $item->variant->product->article ?? '—' }}</p>
                                    <h3 class="mt-1 text-lg font-semibold">{{ $item->variant->product->name ?? 'Товар' }}</h3>
                                    <p class="mt-1 text-sm text-zinc-600">
                                        {{ $item->variant->model ?? '-' }} / {{ $item->variant->size ?? '-' }} / {{ $item->variant->color ?? '-' }}
                                    </p>
                                </div>
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
                                <form method="POST" action="/cart/items/{{ $item->id }}" class="flex items-center gap-2" data-cart-item-form>
                                    @csrf
                                    @method('PATCH')
                                    <span class="text-sm font-semibold">Количество</span>
                                    <div class="checkout-qty-controls">
                                        <button type="button" class="checkout-qty-btn" data-qty-minus>−</button>
                                        <input type="number" min="1" name="quantity" value="{{ $item->quantity }}" class="checkout-qty-input" data-qty-input>
                                        <button type="button" class="checkout-qty-btn" data-qty-plus>+</button>
                                    </div>
                                </form>

                                <form method="POST" action="/cart/items/{{ $item->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="checkout-delete-btn" aria-label="Удалить" title="Удалить">
                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M4 7h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <path d="M9.5 3.5h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <path d="M7 7l.8 12.2A2 2 0 0 0 9.8 21h4.4a2 2 0 0 0 2-1.8L17 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        </svg>
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

                        <div class="checkout-address-wrap">
                            <label for="checkout_address_line" class="mb-1 block text-sm font-semibold">Адрес доставки</label>
                            <input
                                id="checkout_address_line"
                                name="address_line"
                                required
                                value="{{ old('address_line', $user?->address_line) }}"
                                placeholder="Улица, дом, квартира"
                                class="w-full rounded-xl border border-zinc-300 px-3 py-2.5 text-sm"
                                autocomplete="off"
                                data-address-input
                                data-city-target="#checkout_city"
                                data-postal-target="#checkout_postal_code"
                            >
                            <div class="checkout-address-suggestions checkout-hidden" data-address-suggestions></div>
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

                    <div class="checkout-submit-wrap">
                        <button type="submit" class="checkout-submit-btn">Перейти к оплате</button>
                    </div>
                </form>
            </aside>
        </div>
    @endif
</section>

<script>
    (() => {
        document.querySelectorAll('[data-cart-item-form]').forEach((form) => {
            const qtyInput = form.querySelector('[data-qty-input]');
            const minusButton = form.querySelector('[data-qty-minus]');
            const plusButton = form.querySelector('[data-qty-plus]');
            if (!qtyInput || !minusButton || !plusButton) {
                return;
            }

            const submitQuantity = () => {
                const current = Math.max(1, Number(qtyInput.value || 1));
                qtyInput.value = String(current);
                form.requestSubmit();
            };

            minusButton.addEventListener('click', () => {
                const current = Math.max(1, Number(qtyInput.value || 1) - 1);
                qtyInput.value = String(current);
                submitQuantity();
            });

            plusButton.addEventListener('click', () => {
                const current = Math.max(1, Number(qtyInput.value || 1) + 1);
                qtyInput.value = String(current);
                submitQuantity();
            });

            qtyInput.addEventListener('change', submitQuantity);
        });

        const addressInput = document.querySelector('[data-address-input]');
        const suggestionsWrap = document.querySelector('[data-address-suggestions]');
        const cityTarget = document.querySelector(addressInput?.dataset.cityTarget || '');
        const postalTarget = document.querySelector(addressInput?.dataset.postalTarget || '');

        if (!addressInput || !suggestionsWrap) {
            return;
        }

        let debounceTimer = null;
        let lastQuery = '';

        const showSuggestions = () => {
            suggestionsWrap.classList.remove('checkout-hidden');
        };

        const hideSuggestions = () => {
            suggestionsWrap.classList.add('checkout-hidden');
            suggestionsWrap.innerHTML = '';
        };

        const renderSuggestions = (items, emptyText = 'Подсказки не найдены') => {
            suggestionsWrap.innerHTML = '';

            if (!Array.isArray(items) || items.length === 0) {
                const emptyNode = document.createElement('div');
                emptyNode.className = 'checkout-address-suggestion';
                emptyNode.textContent = emptyText;
                suggestionsWrap.appendChild(emptyNode);
                showSuggestions();
                return;
            }

            items.forEach((item) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'checkout-address-suggestion';
                button.textContent = item.value || '';
                button.addEventListener('click', () => {
                    addressInput.value = item.value || '';
                    if (cityTarget) {
                        cityTarget.value = item.data?.city || '';
                    }
                    if (postalTarget) {
                        postalTarget.value = item.data?.postal_code || '';
                    }
                    hideSuggestions();
                });
                suggestionsWrap.appendChild(button);
            });

            showSuggestions();
        };

        const fetchSuggestions = async (query) => {
            renderSuggestions([], 'Поиск адреса...');
            try {
                const response = await fetch(`/checkout/address-suggestions?query=${encodeURIComponent(query)}`, {
                    headers: { 'Accept': 'application/json' },
                });
                if (!response.ok) {
                    renderSuggestions([], 'Не удалось загрузить подсказки');
                    return;
                }

                const payload = await response.json();
                renderSuggestions(payload?.suggestions || []);
            } catch (error) {
                renderSuggestions([], 'Не удалось загрузить подсказки');
            }
        };

        addressInput.addEventListener('input', () => {
            const query = addressInput.value.trim();
            if (query.length < 3) {
                lastQuery = '';
                hideSuggestions();
                return;
            }

            if (query === lastQuery) {
                return;
            }

            if (debounceTimer) {
                clearTimeout(debounceTimer);
            }

            debounceTimer = setTimeout(() => {
                lastQuery = query;
                fetchSuggestions(query);
            }, 350);
        });

        document.addEventListener('click', (event) => {
            if (event.target === addressInput || suggestionsWrap.contains(event.target)) {
                return;
            }
            hideSuggestions();
        });

        const checkoutForm = document.querySelector('form[action="/checkout"]');
        if (!checkoutForm) {
            return;
        }

        const checkoutDraftKey = 'cultbear_checkout_draft_v1';
        const draftFields = ['name', 'email', 'phone', 'address_line', 'city', 'postal_code'];

        const readDraft = () => {
            try {
                const payload = sessionStorage.getItem(checkoutDraftKey);
                return payload ? JSON.parse(payload) : {};
            } catch (error) {
                return {};
            }
        };

        const writeDraft = () => {
            const draft = {};
            draftFields.forEach((fieldName) => {
                const field = checkoutForm.querySelector(`[name="${fieldName}"]`);
                if (!field) {
                    return;
                }
                draft[fieldName] = field.value || '';
            });
            sessionStorage.setItem(checkoutDraftKey, JSON.stringify(draft));
        };

        const savedDraft = readDraft();
        draftFields.forEach((fieldName) => {
            const field = checkoutForm.querySelector(`[name="${fieldName}"]`);
            if (!field) {
                return;
            }

            if (!field.value && savedDraft[fieldName]) {
                field.value = savedDraft[fieldName];
            }

            field.addEventListener('input', writeDraft);
            field.addEventListener('change', writeDraft);
        });

        writeDraft();
    })();
</script>
@endsection
