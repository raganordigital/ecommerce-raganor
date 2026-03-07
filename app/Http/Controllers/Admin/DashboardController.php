<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        // Get the authenticated user with type hint for IDE
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Verify user is admin
        if (! $user || ! $user->hasRole('admin')) {
            abort(403, 'Unauthorized access.');
        }

        // Get basic stats
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalCustomers = User::role('customer')->count();

        $revenueToday = Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total');

        // Get recent orders
        $recentOrders = Order::with('user')
            ->latest()
            ->limit(5)
            ->get();

        // Get low stock products
        $lowStockProducts = Product::where('manage_stock', true)
            ->where('stock_quantity', '<', 10)
            ->where('stock_quantity', '>', 0)
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalOrders',
            'totalProducts',
            'totalCustomers',
            'revenueToday',
            'recentOrders',
            'lowStockProducts'
        ));
    }
}
