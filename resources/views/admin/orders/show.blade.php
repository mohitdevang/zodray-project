<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - {{ $order->order_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-2xl font-bold text-gray-900">Order Details</h1>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ route('admin.dashboard') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Dashboard
                            </a>
                            <a href="{{ route('admin.orders.index') }}" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Orders
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-700 mr-4">{{ Auth::user()->name }}</span>
                        <a href="{{ route('admin.orders.index') }}" class="text-gray-600 hover:text-gray-900">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </a>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('admin.orders.index') }}" class="text-indigo-600 hover:text-indigo-900">
                    ← Back to Orders
                </a>
            </div>

            <!-- Order Info -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                Order #{{ $order->order_number }}
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                Placed on {{ $order->created_at->format('F d, Y g:i A') }}
                            </p>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($order->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Update Status Forms -->
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Update Status</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" onsubmit="return confirm('Are you sure you want to update the order status?')">
                            @csrf
                            @method('PUT')
                            <div class="flex">
                                <select name="status" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="failed" {{ $order->status === 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                                <button type="submit" class="ml-2 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                    Update
                                </button>
                            </div>
                        </form>

                        @if($order->payment)
                        <form method="POST" action="{{ route('admin.orders.updatePaymentStatus', $order->id) }}" onsubmit="return confirm('Are you sure you want to update the payment status?')">
                            @csrf
                            @method('PUT')
                            <div class="flex">
                                <select name="payment_status" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="pending" {{ $order->payment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="completed" {{ $order->payment->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="failed" {{ $order->payment->status === 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="refunded" {{ $order->payment->status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                                <button type="submit" class="ml-2 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                    Update
                                </button>
                            </div>
                        </form>
                        @endif
                    </div>
                </div>

                <!-- Order Details -->
                <div class="px-4 py-5 sm:p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Customer</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->user->name }} ({{ $order->user->email }})</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ strtoupper($order->payment_method ?? 'N/A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Payment Status</dt>
                            <dd class="mt-1 text-sm">
                                @if($order->payment)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->payment->status === 'completed' ? 'bg-green-100 text-green-800' : ($order->payment->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($order->payment->status) }}
                                    </span>
                                @else
                                    N/A
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Shipping Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->shipping_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Shipping Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->shipping_email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Shipping Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->shipping_phone }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Shipping Address</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->shipping_address }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Order Items</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->product_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($item->product_price, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->quantity }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Order Totals -->
                    <div class="mt-8 border-t border-gray-200">
                        <dl class="space-y-2 pt-4">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-600">Subtotal</dt>
                                <dd class="text-sm text-gray-900">${{ number_format($order->subtotal, 2) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-600">Tax (10%)</dt>
                                <dd class="text-sm text-gray-900">${{ number_format($order->tax, 2) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-600">Shipping</dt>
                                <dd class="text-sm text-gray-900">${{ number_format($order->shipping_charge, 2) }}</dd>
                            </div>
                            <div class="flex justify-between border-t border-gray-200 pt-2">
                                <dt class="text-base font-semibold text-gray-900">Total</dt>
                                <dd class="text-base font-semibold text-gray-900">${{ number_format($order->total_amount, 2) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            @if($order->payment)
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Payment Details</h3>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Transaction ID</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->payment->transaction_id ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Amount</dt>
                            <dd class="mt-1 text-sm text-gray-900">${{ number_format($order->payment->amount, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ strtoupper($order->payment->payment_method) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status</dt>
                            <dd class="mt-1 text-sm">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->payment->status === 'completed' ? 'bg-green-100 text-green-800' : ($order->payment->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($order->payment->status) }}
                                </span>
                            </dd>
                        </div>
                        @if($order->payment->paid_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Paid At</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $order->payment->paid_at->format('F d, Y g:i A') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
            @endif
        </main>
    </div>
</body>
</html>
