<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdated;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request): View
    {
        $query = Order::with('user');

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('shipping_name', 'like', "%{$search}%")
                    ->orWhere('shipping_email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->get('payment_status'));
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $orders = $query->latest()->paginate(20);

        $statuses = OrderStatus::cases();
        $paymentStatuses = PaymentStatus::cases();

        return view('admin.orders.index', compact('orders', 'statuses', 'paymentStatuses'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): View
    {
        $order->load(['user', 'items.product']);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order): View
    {
        $order->load(['user', 'items']);
        $statuses = OrderStatus::cases();
        $paymentStatuses = PaymentStatus::cases();

        return view('admin.orders.edit', compact('order', 'statuses', 'paymentStatuses'));
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', OrderStatus::values())],
            'payment_status' => ['required', 'string', 'in:'.implode(',', PaymentStatus::values())],
            'tracking_number' => ['nullable', 'string', 'max:255'],
            'shipping_carrier' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $oldStatus = $order->status;

        // Add timestamps for shipped/delivered
        if ($validated['status'] === OrderStatus::SHIPPED->value && ! $order->shipped_at) {
            $validated['shipped_at'] = now();
        }

        if ($validated['status'] === OrderStatus::DELIVERED->value && ! $order->delivered_at) {
            $validated['delivered_at'] = now();
        }

        $order->update($validated);

        // Send email if status changed
        if ($oldStatus->value !== $validated['status']) {
            Mail::to($order->shipping_email)->send(new OrderStatusUpdated($order));
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order updated successfully.');
    }

    /**
     * Update order status (quick action).
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', OrderStatus::values())],
        ]);

        $oldStatus = $order->status;

        $data = ['status' => $validated['status']];

        // Add timestamps for shipped/delivered
        if ($validated['status'] === OrderStatus::SHIPPED->value && ! $order->shipped_at) {
            $data['shipped_at'] = now();
        }

        if ($validated['status'] === OrderStatus::DELIVERED->value && ! $order->delivered_at) {
            $data['delivered_at'] = now();
        }

        $order->update($data);

        // Send email if status changed
        if ($oldStatus->value !== $validated['status']) {
            Mail::to($order->shipping_email)->send(new OrderStatusUpdated($order));
        }

        return redirect()
            ->back()
            ->with('success', 'Order status updated to '.OrderStatus::from($validated['status'])->label());
    }

    /**
     * Remove the specified order.
     */
    public function destroy(Order $order): RedirectResponse
    {
        // Only allow deletion of pending/cancelled orders
        if (! in_array($order->status, [OrderStatus::PENDING, OrderStatus::CANCELLED])) {
            return redirect()
                ->back()
                ->with('error', 'Only pending or cancelled orders can be deleted.');
        }

        $orderNumber = $order->order_number;
        $order->delete();

        return redirect()
            ->route('admin.orders.index')
            ->with('success', "Order #{$orderNumber} deleted successfully.");
    }
}
