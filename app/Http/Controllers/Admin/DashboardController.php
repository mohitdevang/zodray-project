<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $totalSales = Payment::where('status', 'completed')->sum('amount');
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $failedOrders = Order::where('status', 'failed')->count();

        $paidAmount = Payment::where('status', 'completed')->sum('amount');
        $unpaidAmount = Payment::where('status', 'pending')->sum('amount');

        $codPayments = Payment::where('payment_method', 'cod')
            ->where('status', 'completed')
            ->count();
        $onlinePayments = Payment::where('payment_method', 'online')
            ->where('status', 'completed')
            ->count();

        // Recent orders
        $recentOrders = Order::with(['user', 'items', 'payment'])
            ->latest()
            ->take(10)
            ->get();

        // Orders by status
        $ordersByStatus = Order::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Sales chart data (last 7 days)
        $salesByDate = Payment::where('status', 'completed')
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalSales',
            'pendingOrders',
            'completedOrders',
            'failedOrders',
            'paidAmount',
            'unpaidAmount',
            'codPayments',
            'onlinePayments',
            'recentOrders',
            'ordersByStatus',
            'salesByDate'
        ));
    }
}
