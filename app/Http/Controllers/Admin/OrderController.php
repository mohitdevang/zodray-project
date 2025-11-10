<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items', 'payment']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('payment_status')) {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('status', $request->payment_status);
            });
        }

        if ($request->filled('order_number')) {
            $query->where('order_number', 'like', '%' . $request->order_number . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function export(Request $request)
    {
        $query = Order::with(['user', 'payment']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('payment_status')) {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('status', $request->payment_status);
            });
        }
        if ($request->filled('order_number')) {
            $query->where('order_number', 'like', '%' . $request->order_number . '%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="orders_export_'.now()->format('Ymd_His').'.csv"',
        ];

        $columns = [
            'Order ID', 'Order Number', 'User', 'Email', 'Status', 'Payment Method', 'Payment Status', 'Total Amount', 'Created At'
        ];

        $callback = function() use ($orders, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->id,
                    $order->order_number,
                    optional($order->user)->name,
                    optional($order->user)->email,
                    $order->status,
                    $order->payment_method,
                    optional($order->payment)->status,
                    $order->total_amount,
                    $order->created_at->toDateTimeString(),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items', 'payment'])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled,failed'
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return redirect()->route('admin.orders.show', $order->id)
            ->with('success', 'Order status updated successfully');
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,completed,failed,refunded'
        ]);

        $order = Order::with('payment')->findOrFail($id);
        $payment = $order->payment;

        $payment->status = $request->payment_status;
        if ($request->payment_status === 'completed' && !$payment->paid_at) {
            $payment->paid_at = now();
        }
        $payment->save();

        // Update order status based on payment
        if ($request->payment_status === 'completed') {
            $order->status = 'completed';
            $order->save();
        }

        return redirect()->route('admin.orders.show', $order->id)
            ->with('success', 'Payment status updated successfully');
    }
}
