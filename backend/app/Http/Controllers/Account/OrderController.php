<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::query()
            ->where('user_id', auth()->id())
            ->latest('id')
            ->paginate(20);

        return response()->json($orders);
    }

    public function show(int $id): JsonResponse
    {
        $order = Order::query()
            ->with('items', 'payments')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return response()->json($order);
    }
}
