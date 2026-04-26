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
                    <div
                        x-data="{ images: @js($imageUrls), activeIndex: 0 }"
                        class="space-y-3"
                    >
                        <div class="relative">
                            <img
                                :src="images[activeIndex]"
                                alt="{{ $product->name }}"
                                class="aspect-square w-full rounded-xl object-cover"
                            >

                            <button
                                type="button"
                                @click="activeIndex = (activeIndex - 1 + images.length) % images.length"
                                class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full bg-white/90 px-3 py-1 text-xl font-semibold text-zinc-800 shadow"
                                aria-label="Предыдущее фото"
                            >‹</button>

                            <button
                                type="button"
                                @click="activeIndex = (activeIndex + 1) % images.length"
                                class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full bg-white/90 px-3 py-1 text-xl font-semibold text-zinc-800 shadow"
                                aria-label="Следующее фото"
                            >›</button>
                        </div>

                        <div class="grid grid-cols-5 gap-2">
                            <template x-for="(image, index) in images" :key="image + index">
                                <button
                                    type="button"
                                    @click="activeIndex = index"
                                    class="overflow-hidden rounded-lg border-2 transition"
                                    :class="activeIndex === index ? 'border-black' : 'border-transparent'"
                                >
                                    <img :src="image" alt="" class="aspect-square w-full object-cover">
                                </button>
                            </template>
                        </div>
                    </div>
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
