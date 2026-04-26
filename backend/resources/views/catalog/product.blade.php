@extends('layouts.app', ['title' => $product->name.' — CultBear'])

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10">
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

        <div class="grid gap-8 md:grid-cols-2">
            <div class="rounded-2xl bg-zinc-100 p-8">
                @if(! empty($imageUrls))
                    <div class="js-product-carousel space-y-3" data-images='@json($imageUrls)'>
                        <div class="relative">
                            <img
                                src="{{ $imageUrls[0] }}"
                                alt="{{ $product->name }}"
                                class="aspect-square w-full rounded-xl object-cover"
                                data-carousel-image
                            >

                            @if(count($imageUrls) > 1)
                                <button
                                    type="button"
                                    class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-white/95 px-3 py-2 text-lg font-semibold text-zinc-900 shadow"
                                    aria-label="Предыдущее фото"
                                    data-carousel-prev
                                >←</button>

                                <button
                                    type="button"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-white/95 px-3 py-2 text-lg font-semibold text-zinc-900 shadow"
                                    aria-label="Следующее фото"
                                    data-carousel-next
                                >→</button>
                            @endif
                        </div>

                        @if(count($imageUrls) > 1)
                            <div class="flex items-center justify-center gap-2" data-carousel-dots></div>
                        @endif
                    </div>
                    <script>
                        (() => {
                            const carousel = document.currentScript.previousElementSibling;
                            if (!carousel) return;

                            let images = [];
                            try {
                                images = JSON.parse(carousel.dataset.images || '[]');
                            } catch (e) {
                                images = [];
                            }

                            if (!Array.isArray(images) || images.length <= 1) return;

                            const imageNode = carousel.querySelector('[data-carousel-image]');
                            const prevButton = carousel.querySelector('[data-carousel-prev]');
                            const nextButton = carousel.querySelector('[data-carousel-next]');
                            const dotsWrap = carousel.querySelector('[data-carousel-dots]');
                            const stage = imageNode?.parentElement;

                            if (!imageNode || !prevButton || !nextButton || !dotsWrap || !stage) return;

                            let activeIndex = 0;
                            let touchStartX = null;
                            const dots = images.map((_, index) => {
                                const dot = document.createElement('button');
                                dot.type = 'button';
                                dot.className = 'h-2.5 w-2.5 rounded-full bg-zinc-300 transition';
                                dot.setAttribute('aria-label', `Показать фото ${index + 1}`);
                                dot.addEventListener('click', () => show(index));
                                dotsWrap.appendChild(dot);
                                return dot;
                            });

                            const show = (index) => {
                                activeIndex = (index + images.length) % images.length;
                                imageNode.src = images[activeIndex];
                                dots.forEach((dot, dotIndex) => {
                                    dot.className = dotIndex === activeIndex
                                        ? 'h-2.5 w-2.5 rounded-full bg-zinc-900 transition'
                                        : 'h-2.5 w-2.5 rounded-full bg-zinc-300 transition';
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
                        })();
                    </script>
                @else
                    <div class="aspect-square rounded-xl bg-zinc-200"></div>
                @endif
            </div>
            <div>
                <p class="text-xs text-zinc-500">{{ $product->article }}</p>
                <h1 class="mt-2 text-3xl font-black">{{ $product->name }}</h1>
                <p class="mt-4 text-zinc-700">{{ $product->description }}</p>

                <div class="mt-6 space-y-4">
                    @if(session('status'))
                        <p class="rounded border border-zinc-200 bg-zinc-50 p-3 text-sm text-zinc-800">{{ session('status') }}</p>
                    @endif
                    @if(session('error'))
                        <p class="rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">{{ session('error') }}</p>
                    @endif

                    @if($product->variants->isNotEmpty())
                        <form method="POST" action="/cart/items" class="space-y-4">
                            @csrf
                            <div>
                                <label for="product_variant_id" class="mb-1 block text-sm font-medium">Вариант</label>
                                <select id="product_variant_id" name="product_variant_id" class="w-full rounded border border-zinc-300 px-3 py-2" required>
                                    @foreach($product->variants as $variant)
                                        <option value="{{ $variant->id }}">
                                            {{ $variant->model }} / {{ $variant->size }} / {{ $variant->color }} — {{ number_format($variant->price, 0, '.', ' ') }} ₽
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="rounded bg-black px-6 py-3 text-sm font-semibold text-white">Добавить в корзину</button>
                        </form>
                    @else
                        <p class="rounded border border-zinc-200 bg-zinc-50 p-3 text-sm text-zinc-700">Нет доступных вариантов в наличии.</p>
                        <button type="button" class="cursor-not-allowed rounded bg-zinc-300 px-6 py-3 text-sm font-semibold text-white" disabled>Нет в наличии</button>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
