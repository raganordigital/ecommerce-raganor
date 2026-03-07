<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display user's orders
     */
    public function index(Request $request): View
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('items')
            ->latest()
            ->paginate(10);

        return view('public.orders.index', compact('orders'));
    }

    /**
     * Display specific order
     */
    public function show(Order $order): View
    {
        // Ensure user owns this order
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items');

        return view('public.orders.show', compact('order'));
    }
}
