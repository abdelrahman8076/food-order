<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private AnalyticsService $analytics)
    {
    }

    public function index()
    {
        $todayOrders = Order::whereDate('created_at', today())->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $recentOrders = Order::with('orderItems')->latest()->take(10)->get();
        $monthSummary = $this->analytics->monthToDateSummary();

        return view('admin.dashboard', compact('todayOrders', 'pendingOrders', 'recentOrders', 'monthSummary'));
    }
}
