<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get basic stats
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalCustomers = User::role('customer')->count();
        
        $revenueToday = Order::whereDate('created_at', today())
            ->where('payment_status', 'paid')
            ->sum('total');
            
        $recentOrders = Order::with('user')
            ->latest()
            ->limit(5)
            ->get();
            
        return view('admin.dashboard.index', compact(
            'totalOrders',
            'totalProducts',
            'totalCustomers',
            'revenueToday',
            'recentOrders'
        ));
    }
}