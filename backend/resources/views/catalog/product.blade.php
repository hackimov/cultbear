@extends('layouts.app', ['title' => $product->name.' — CultBear'])

@section('content')
    <section class="mx-auto max-w-7xl px-4 py-10">
        @php
            $primaryMedia = $product->media->firstWhere('is_primary', true) ?? $product->media->first();
            $mediaPath = $primaryMedia?->webp_path ?: $primaryMedia?->preview_path ?: $primaryMedia?->path;
            $mediaDisk = $primaryMedia?->disk ?: 's3';

            $imageUrl = null;
            if ($mediaPath) {
                $imageUrl = filter_var($mediaPath, FILTER_VALIDATE_URL)
                    ? $mediaPath
                    : \Illuminate\Support\Facades\Storage::disk($mediaDisk)->url($mediaPath);
            }
        @endphp

        <div class="grid gap-8 md:grid-cols-2">
            <div class="rounded-2xl bg-zinc-100 p-8">
                @if($imageUrl)
                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="aspect-square w-full rounded-xl object-cover">
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
