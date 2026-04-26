<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $cart = $this->resolveCart($request);
        $cart->load('items.variant.product');

        return response()->json($cart);
    }

    public function storeItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $variant = ProductVariant::query()->findOrFail($validated['product_variant_id']);
        $cart = $this->resolveCart($request);

        $item = CartItem::query()->firstOrNew([
            'cart_id' => $cart->id,
            'product_variant_id' => $variant->id,
        ]);
        $item->quantity = max(1, ($item->quantity ?? 0) + (int) $validated['quantity']);
        $item->unit_price = $variant->price;
        $item->save();

        return response()->json($item, 201);
    }

    public function updateItem(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $this->resolveCart($request);
        $item = $cart->items()->whereKey($id)->firstOrFail();

        if ($item->variant->stock_quantity < $validated['quantity']) {
            return response()->json(['message' => 'Недостаточно товара на складе.'], 422);
        }

        $item->update(['quantity' => $validated['quantity']]);

        return response()->json($item);
    }

    public function destroyItem(Request $request, int $id): JsonResponse
    {
        $cart = $this->resolveCart($request);
        $cart->items()->whereKey($id)->delete();

        return response()->json(status: 204);
    }

    private function resolveCart(Request $request): Cart
    {
        if ($request->user()) {
            return Cart::query()->firstOrCreate(['user_id' => $request->user()->id], ['session_id' => $request->session()->getId()]);
        }

        return Cart::query()->firstOrCreate(['session_id' => $request->session()->getId()], ['user_id' => null]);
    }
}
