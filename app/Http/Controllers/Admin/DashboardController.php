<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'totalProducts' => Product::count(),
            'totalOrders' => Order::count(),
            'totalCustomers' => User::where('role', 'user')->count(),
            'totalRevenue' => Order::where('status', '!=', 'cancelled')->sum('total'),
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'lowStockProducts' => Product::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
            'outOfStockProducts' => Product::where('stock', 0)->count(),
        ];

        $recentOrders = Order::with('user')
            ->latest()
            ->take(5)
            ->get();

        $lowStockProducts = Product::with('category')
            ->where('stock', '<=', 10)
            ->orderBy('stock')
            ->take(10)
            ->get();

        $ordersByStatus = Order::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
            'ordersByStatus' => $ordersByStatus,
        ]);
    }
}
