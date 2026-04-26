<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse | View
    {
        $orders = Order::query()
            ->where('user_id', auth()->id())
            ->latest('id')
            ->paginate(20);

        if ($request->expectsJson()) {
            return response()->json($orders);
        }

        return view('account.orders', [
            'orders' => $orders,
        ]);
    }

    public function show(Request $request, int $id): JsonResponse | View
    {
        $order = Order::query()
            ->with('items', 'payments')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        if ($request->expectsJson()) {
            return response()->json($order);
        }

        return view('account.order-show', [
            'order' => $order,
        ]);
    }
}
