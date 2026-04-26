@extends('layouts.app', ['title' => $product->name.' — CultBear'])

@section('content')
    <style>
        .product-page-wrap {
            max-width: 1120px;
        }

        .product-layout {
            display: grid;
            gap: 2rem;
            align-items: start;
        }

        .product-gallery-wrap {
            width: 100%;
            max-width: 760px;
            margin: 0 auto;
        }

        .product-gallery-image {
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            max-height: 640px;
        }

        .product-carousel-arrow {
            position: absolute;
            top: 50%;
            z-index: 10;
            width: 40px;
            height: 40px;
            transform: translateY(-50%);
            border-radius: 9999px;
            border: 1px solid #e4e4e7;
            background: rgba(255, 255, 255, 0.95);
            color: #18181b;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.08);
        }

        .product-carousel-arrow-left {
            left: 0.75rem;
        }

        .product-carousel-arrow-right {
            right: 0.75rem;
        }

        .product-carousel-thumbs {
            display: none;
            gap: 0.5rem;
            overflow-x: auto;
            padding: 0.25rem 0;
        }

        .product-carousel-thumb {
            width: 56px;
            height: 56px;
            flex: 0 0 auto;
            overflow: hidden;
            border: 2px solid transparent;
            border-radius: 0.625rem;
            background: #fff;
            cursor: pointer;
            transition: border-color 0.2s ease;
        }

        .product-carousel-thumb img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-carousel-thumb.is-active {
            border-color: #18181b;
        }

        .product-carousel-dots {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .product-carousel-dot {
            width: 10px;
            height: 10px;
            border-radius: 9999px;
            background: #d4d4d8;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .product-carousel-dot.is-active {
            background: #18181b;
        }

        .product-buy-box {
            padding: 1.375rem;
            border: 1px solid #e4e4e7;
            border-radius: 1rem;
            background: #fff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .product-price-card {
            padding: 1rem;
            border-radius: 0.875rem;
            background: linear-gradient(180deg, #111827 0%, #09090b 100%);
            color: #fff;
        }

        .product-model-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
            font-weight: 700;
            color: #18181b;
        }

        .product-variant-select {
            width: 100%;
            border: 1px solid #d4d4d8;
            border-radius: 0.875rem;
            padding: 0.75rem 0.875rem;
            font-size: 0.95rem;
            line-height: 1.2;
            background: #fff;
        }

        .product-attrs-box {
            border: 1px solid #e4e4e7;
            border-radius: 0.875rem;
            background: #fafafa;
            padding: 0.875rem;
        }

        .product-attrs-title {
            margin: 0;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #71717a;
        }

        .product-attrs-value {
            margin: 0.375rem 0 0;
            font-size: 0.9375rem;
            font-weight: 600;
            color: #18181b;
            line-height: 1.35;
        }

        .product-attrs-title + .product-attrs-title {
            margin-top: 0.875rem;
        }

        .product-qty-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-top: 0.125rem;
        }

        .product-qty-control {
            display: inline-flex;
            align-items: center;
            border: 1px solid #d4d4d8;
            border-radius: 0.875rem;
            overflow: hidden;
        }

        .product-qty-btn {
            padding: 0.5rem 0.875rem;
            font-size: 1.125rem;
            line-height: 1;
        }

        .product-qty-input {
            width: 3.5rem;
            border-left: 1px solid #d4d4d8;
            border-right: 1px solid #d4d4d8;
            padding: 0.5rem 0.25rem;
            text-align: center;
            font-size: 0.95rem;
            line-height: 1.2;
            outline: none;
        }

        .product-primary-btn,
        .product-secondary-btn {
            width: 100%;
            border-radius: 0.875rem;
            padding: 0.75rem 1.5rem;
            text-align: center;
            font-size: 0.95rem;
            font-weight: 700;
            line-height: 1.2;
            display: block;
        }

        .product-primary-btn {
            background: #000;
            color: #fff;
            transition: background-color 0.2s ease;
        }

        .product-primary-btn:hover {
            background: #27272a;
        }

        .product-secondary-btn {
            border: 1px solid #d4d4d8;
            color: #18181b;
            background: #fff;
        }

        @media (min-width: 640px) {
            .product-carousel-thumbs {
                display: flex;
            }
        }

        @media (min-width: 1024px) {
            .product-layout {
                grid-template-columns: minmax(0, 1fr) 380px;
            }
        }
    </style>

    <section class="product-page-wrap mx-auto px-4 py-8 md:py-10">
        @php
            $imageUrls = $product->media
                ->sortByDesc(fn ($media) => (bool) $media->is_primary)
                ->map(function ($media): ?string {
                    $mediaPath = $media->webp_path ?: $media->preview_path ?: $media->path;

                    if (! $mediaPath) {
                        return null;
                    }

                    if (filter_var($mediaPath, FILTER_VALIDATE_URL)) {
                        return $mediaPath;
                    }

                    return \Illuminate\Support\Facades\Storage::disk($media->disk ?: 's3')->url($mediaPath);
                })
                ->filter()
                ->values()
                ->all();
        @endphp

        <div class="product-layout">
            <div class="product-gallery-wrap">
                <div class="js-product-carousel rounded-2xl border border-zinc-200 bg-zinc-50 p-4 sm:p-6" data-images='@json($imageUrls)'>
                @if(! empty($imageUrls))
                    <div class="relative overflow-hidden rounded-xl bg-white shadow-sm">
                        <img
                            src="{{ $imageUrls[0] }}"
                            alt="{{ $product->name }}"
                            class="product-gallery-image"
                            data-carousel-image
                        >

                        @if(count($imageUrls) > 1)
                            <button
                                type="button"
                                class="product-carousel-arrow product-carousel-arrow-left"
                                aria-label="Предыдущее фото"
                                data-carousel-prev
                            >&larr;</button>

                            <button
                                type="button"
                                class="product-carousel-arrow product-carousel-arrow-right"
                                aria-label="Следующее фото"
                                data-carousel-next
                            >&rarr;</button>
                        @endif
                    </div>

                    @if(count($imageUrls) > 1)
                        <div class="mt-4 space-y-3">
                            <div class="product-carousel-thumbs" data-carousel-thumbs></div>
                            <div class="flex items-center justify-between gap-3">
                                <div class="product-carousel-dots" data-carousel-dots></div>
                                <div class="rounded-md bg-zinc-200/70 px-2.5 py-1 text-xs font-semibold text-zinc-600 tabular-nums" data-carousel-count></div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="aspect-square rounded-xl bg-zinc-200"></div>
                @endif
            </div>
            </div>

            <div class="space-y-4 lg:sticky lg:top-6 lg:self-start">
                <div>
                    <p class="text-xs text-zinc-500">Артикул: {{ $product->article ?: '—' }}</p>
                    <h1 class="mt-1 text-3xl font-black leading-tight">{{ $product->name }}</h1>
                    <p class="mt-3 text-sm leading-6 text-zinc-700">{{ $product->description }}</p>
                </div>

                @if(session('status'))
                    <p class="rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('status') }}</p>
                @endif
                @if(session('error'))
                    <p class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ session('error') }}</p>
                @endif

                @if($errors->any())
                    <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if($product->variants->isNotEmpty())
                    <form method="POST" action="/cart/items" class="product-buy-box js-buy-box">
                        @csrf
                        @php($selectedVariantId = old('product_variant_id'))
                        @php($selectedVariant = $product->variants->firstWhere('id', (int) $selectedVariantId) ?: $product->variants->first())
                        @php($availableSizes = $product->variants->pluck('size')->filter()->unique()->values())
                        @php($availableColors = $product->variants->pluck('color')->filter()->unique()->values())

                        <div class="product-price-card">
                            <div class="text-xs uppercase tracking-wide text-zinc-300">Цена</div>
                            <div class="mt-1 text-3xl font-black" data-variant-price>
                                {{ number_format($selectedVariant->price, 0, '.', ' ') }} ₽
                            </div>
                        </div>

                        <div>
                            <label for="product_variant_id" class="product-model-label" data-model-label>{{ $selectedVariant->model }}</label>
                            <select id="product_variant_id" name="product_variant_id" class="product-variant-select" required>
                                @foreach($product->variants as $variant)
                                    <option
                                        value="{{ $variant->id }}"
                                        data-price="{{ $variant->price }}"
                                        data-model="{{ $variant->model }}"
                                        @selected((int) old('product_variant_id', $selectedVariant->id) === $variant->id)
                                    >
                                        {{ $variant->size }} / {{ $variant->color }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="product-attrs-box">
                            <p class="product-attrs-title">Доступные размеры</p>
                            <p class="product-attrs-value">
                                {{ $availableSizes->isNotEmpty() ? $availableSizes->join(', ') : '—' }}
                            </p>
                            <p class="product-attrs-title">Доступные цвета</p>
                            <p class="product-attrs-value">
                                {{ $availableColors->isNotEmpty() ? $availableColors->join(', ') : '—' }}
                            </p>
                        </div>

                        <div class="product-qty-row">
                            <span class="text-sm font-semibold">Количество</span>
                            <div class="product-qty-control">
                                <button type="button" class="product-qty-btn" data-qty-minus aria-label="Уменьшить">−</button>
                                <input
                                    type="number"
                                    name="quantity"
                                    value="{{ old('quantity', 1) }}"
                                    min="1"
                                    class="product-qty-input"
                                    data-qty-input
                                >
                                <button type="button" class="product-qty-btn" data-qty-plus aria-label="Увеличить">+</button>
                            </div>
                        </div>

                        <button type="submit" class="product-primary-btn">
                            Добавить в корзину
                        </button>
                        <a href="/cart" class="product-secondary-btn">
                            Перейти в корзину
                        </a>
                    </form>
                @else
                    <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-5">
                        <p class="text-sm text-zinc-700">Нет доступных вариантов в наличии.</p>
                        <button type="button" class="mt-4 w-full cursor-not-allowed rounded-xl bg-zinc-300 px-6 py-3 text-sm font-semibold text-white" disabled>
                            Нет в наличии
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <script>
        (() => {
            const carousel = document.querySelector('.js-product-carousel');
            if (carousel) {
                let images = [];
                try {
                    images = JSON.parse(carousel.dataset.images || '[]');
                } catch (e) {
                    images = [];
                }

                if (Array.isArray(images) && images.length > 1) {
                    const imageNode = carousel.querySelector('[data-carousel-image]');
                    const prevButton = carousel.querySelector('[data-carousel-prev]');
                    const nextButton = carousel.querySelector('[data-carousel-next]');
                    const dotsWrap = carousel.querySelector('[data-carousel-dots]');
                    const thumbsWrap = carousel.querySelector('[data-carousel-thumbs]');
                    const countNode = carousel.querySelector('[data-carousel-count]');
                    const stage = imageNode?.parentElement;

                    if (imageNode && prevButton && nextButton && stage) {
                        let activeIndex = 0;
                        let touchStartX = null;
                        const dots = [];
                        const thumbs = [];

                        const buildIndicatorButton = (className, onClick) => {
                            const button = document.createElement('button');
                            button.type = 'button';
                            button.className = className;
                            button.addEventListener('click', onClick);
                            return button;
                        };

                        if (dotsWrap) {
                            images.forEach((_, index) => {
                                const dot = buildIndicatorButton(
                                    'product-carousel-dot',
                                    () => show(index)
                                );
                                dot.setAttribute('aria-label', `Показать фото ${index + 1}`);
                                dotsWrap.appendChild(dot);
                                dots.push(dot);
                            });
                        }

                        if (thumbsWrap) {
                            images.forEach((image, index) => {
                                const thumb = buildIndicatorButton(
                                    'product-carousel-thumb',
                                    () => show(index)
                                );
                                thumb.innerHTML = `<img src="${image}" alt="" class="h-full w-full object-cover">`;
                                thumbsWrap.appendChild(thumb);
                                thumbs.push(thumb);
                            });
                        }

                        const show = (index) => {
                            activeIndex = (index + images.length) % images.length;
                            imageNode.src = images[activeIndex];
                            imageNode.alt = `${@json($product->name)} (${activeIndex + 1}/${images.length})`;

                            dots.forEach((dot, dotIndex) => {
                                dot.classList.toggle('is-active', dotIndex === activeIndex);
                            });

                            thumbs.forEach((thumb, thumbIndex) => {
                                thumb.classList.toggle('is-active', thumbIndex === activeIndex);
                            });

                            if (countNode) {
                                countNode.textContent = `${activeIndex + 1} / ${images.length}`;
                            }
                        };

                        prevButton.addEventListener('click', () => show(activeIndex - 1));
                        nextButton.addEventListener('click', () => show(activeIndex + 1));

                        stage.addEventListener('touchstart', (event) => {
                            touchStartX = event.changedTouches[0]?.clientX ?? null;
                        }, { passive: true });

                        stage.addEventListener('touchend', (event) => {
                            if (touchStartX === null) return;
                            const touchEndX = event.changedTouches[0]?.clientX ?? touchStartX;
                            const delta = touchEndX - touchStartX;
                            if (Math.abs(delta) >= 35) {
                                show(activeIndex + (delta < 0 ? 1 : -1));
                            }
                            touchStartX = null;
                        }, { passive: true });

                        show(0);
                    }
                }
            }

            const buyBox = document.querySelector('.js-buy-box');
            if (!buyBox) return;

            const variantSelect = buyBox.querySelector('#product_variant_id');
            const priceNode = buyBox.querySelector('[data-variant-price]');
            const modelLabelNode = buyBox.querySelector('[data-model-label]');
            const qtyInput = buyBox.querySelector('[data-qty-input]');
            const minusButton = buyBox.querySelector('[data-qty-minus]');
            const plusButton = buyBox.querySelector('[data-qty-plus]');

            const formatPrice = (value) => Number(value).toLocaleString('ru-RU') + ' ₽';
            const updatePrice = () => {
                if (!variantSelect || !priceNode) return;
                const selected = variantSelect.options[variantSelect.selectedIndex];
                const price = selected?.dataset.price ?? 0;
                priceNode.textContent = formatPrice(price);
                if (modelLabelNode && selected?.dataset.model) {
                    modelLabelNode.textContent = selected.dataset.model;
                }
            };

            variantSelect?.addEventListener('change', updatePrice);
            updatePrice();

            minusButton?.addEventListener('click', () => {
                const current = Math.max(1, Number(qtyInput?.value || 1) - 1);
                if (qtyInput) qtyInput.value = String(current);
            });

            plusButton?.addEventListener('click', () => {
                const current = Math.max(1, Number(qtyInput?.value || 1) + 1);
                if (qtyInput) qtyInput.value = String(current);
            });
        })();
    </script>
@endsection
