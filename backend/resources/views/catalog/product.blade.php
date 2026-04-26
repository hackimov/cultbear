@extends('layouts.app', ['title' => $product->name.' — CultBear'])

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-8 md:py-10">
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

        <div class="grid gap-6 lg:grid-cols-[96px_minmax(0,1fr)_380px]">
            <div class="hidden lg:block">
                @if(count($imageUrls) > 1)
                    <div class="js-carousel-thumbs flex flex-col gap-3" data-carousel-thumbs></div>
                @endif
            </div>

            <div class="js-product-carousel rounded-2xl border border-zinc-200 bg-zinc-50 p-4 sm:p-6" data-images='@json($imageUrls)'>
                @if(! empty($imageUrls))
                    <div class="relative overflow-hidden rounded-xl bg-white">
                        <img
                            src="{{ $imageUrls[0] }}"
                            alt="{{ $product->name }}"
                            class="aspect-square w-full object-cover"
                            data-carousel-image
                        >

                        @if(count($imageUrls) > 1)
                            <button
                                type="button"
                                class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full border border-zinc-200 bg-white/95 px-3 py-2 text-base font-semibold text-zinc-900 shadow-sm"
                                aria-label="Предыдущее фото"
                                data-carousel-prev
                            >←</button>

                            <button
                                type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full border border-zinc-200 bg-white/95 px-3 py-2 text-base font-semibold text-zinc-900 shadow-sm"
                                aria-label="Следующее фото"
                                data-carousel-next
                            >→</button>
                        @endif
                    </div>

                    @if(count($imageUrls) > 1)
                        <div class="mt-4 flex items-center justify-center gap-2 lg:hidden" data-carousel-dots></div>
                    @endif
                @else
                    <div class="aspect-square rounded-xl bg-zinc-200"></div>
                @endif
            </div>

            <div class="space-y-4">
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

                @if($product->variants->isNotEmpty())
                    <form method="POST" action="/cart/items" class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm space-y-4 js-buy-box">
                        @csrf
                        @php($firstVariant = $product->variants->first())

                        <div class="rounded-xl bg-zinc-900 p-4 text-white">
                            <div class="text-xs uppercase tracking-wide text-zinc-300">Цена</div>
                            <div class="mt-1 text-3xl font-black" data-variant-price>
                                {{ number_format($firstVariant->price, 0, '.', ' ') }} ₽
                            </div>
                        </div>

                        <div>
                            <label for="product_variant_id" class="mb-1.5 block text-sm font-semibold">Вариант</label>
                            <select id="product_variant_id" name="product_variant_id" class="w-full rounded-xl border border-zinc-300 px-3 py-3 text-sm" required>
                                @foreach($product->variants as $variant)
                                    <option value="{{ $variant->id }}" data-price="{{ $variant->price }}">
                                        {{ $variant->model }} / {{ $variant->size }} / {{ $variant->color }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center justify-between gap-3">
                            <span class="text-sm font-semibold">Количество</span>
                            <div class="inline-flex items-center rounded-xl border border-zinc-300">
                                <button type="button" class="px-3 py-2 text-lg leading-none" data-qty-minus aria-label="Уменьшить">−</button>
                                <input
                                    type="number"
                                    name="quantity"
                                    value="1"
                                    min="1"
                                    class="w-14 border-x border-zinc-300 py-2 text-center text-sm outline-none"
                                    data-qty-input
                                >
                                <button type="button" class="px-3 py-2 text-lg leading-none" data-qty-plus aria-label="Увеличить">+</button>
                            </div>
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-black px-6 py-3.5 text-sm font-bold text-white transition hover:bg-zinc-800">
                            Добавить в корзину
                        </button>
                        <a href="/cart" class="block w-full rounded-xl border border-zinc-300 px-6 py-3 text-center text-sm font-semibold text-zinc-900">
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
                    const thumbsWrap = document.querySelector('[data-carousel-thumbs]');
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
                                    'h-2.5 w-2.5 rounded-full bg-zinc-300 transition',
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
                                    'overflow-hidden rounded-xl border-2 border-transparent bg-white',
                                    () => show(index)
                                );
                                thumb.innerHTML = `<img src="${image}" alt="" class="h-20 w-20 object-cover">`;
                                thumbsWrap.appendChild(thumb);
                                thumbs.push(thumb);
                            });
                        }

                        const show = (index) => {
                            activeIndex = (index + images.length) % images.length;
                            imageNode.src = images[activeIndex];

                            dots.forEach((dot, dotIndex) => {
                                dot.className = dotIndex === activeIndex
                                    ? 'h-2.5 w-2.5 rounded-full bg-zinc-900 transition'
                                    : 'h-2.5 w-2.5 rounded-full bg-zinc-300 transition';
                            });

                            thumbs.forEach((thumb, thumbIndex) => {
                                thumb.className = thumbIndex === activeIndex
                                    ? 'overflow-hidden rounded-xl border-2 border-zinc-900 bg-white'
                                    : 'overflow-hidden rounded-xl border-2 border-transparent bg-white';
                            });
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
            const qtyInput = buyBox.querySelector('[data-qty-input]');
            const minusButton = buyBox.querySelector('[data-qty-minus]');
            const plusButton = buyBox.querySelector('[data-qty-plus]');

            const formatPrice = (value) => Number(value).toLocaleString('ru-RU') + ' ₽';
            const updatePrice = () => {
                if (!variantSelect || !priceNode) return;
                const selected = variantSelect.options[variantSelect.selectedIndex];
                const price = selected?.dataset.price ?? 0;
                priceNode.textContent = formatPrice(price);
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
