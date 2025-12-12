@extends('layouts.admin')

@section('title', 'Order Details - Admin Panel')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-600 hover:text-primary dark:text-gray-400">
            ← Back to Orders
        </a>
    </div>

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-extrabold text-secondary">Order #{{ $order->order_number }}</h1>
        <div class="flex gap-2">
            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold {{ $order->status === 'delivered' ? 'bg-green-100 text-green-800' : ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                {{ ucfirst($order->status) }}
            </span>
            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                Payment: {{ ucfirst($order->payment_status) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Order Items --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-bold">Order Items</h2>
                <div class="space-y-4">
                    @foreach ($order->items as $item)
                        <div class="flex items-start gap-4 border-b pb-4 last:border-b-0 dark:border-gray-700">
                            @if ($item->product_image)
                                <img src="{{ str_starts_with($item->product_image, 'http') ? $item->product_image : asset('public'.$item->product_image) }}" alt="{{ $item->product_name }}" class="h-16 w-16 rounded object-cover">
                            @else
                                <div class="flex h-16 w-16 items-center justify-center rounded bg-gray-100 dark:bg-gray-700">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h3 class="font-semibold">{{ $item->product_name }}</h3>
                                @if ($item->product_sku)
                                    <p class="text-xs text-gray-500">SKU: {{ $item->product_sku }}</p>
                                @endif
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Qty: {{ $item->quantity }} × PKR {{ number_format($item->price, 2) }}</p>
                            </div>
                            <div class="text-right font-semibold">
                                PKR {{ number_format($item->total, 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 space-y-2 border-t pt-4 dark:border-gray-700">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                        <span class="font-semibold">PKR {{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Tax:</span>
                        <span class="font-semibold">PKR {{ number_format($order->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Shipping:</span>
                        <span class="font-semibold">PKR {{ number_format($order->shipping, 2) }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-2 text-lg font-bold dark:border-gray-700">
                        <span>Total:</span>
                        <span class="text-primary">PKR {{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Details Sidebar --}}
        <div class="space-y-6">
            {{-- Customer Info --}}
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-bold">Customer Information</h2>
                <div class="space-y-2 text-sm">
                    <div>
                        <span class="font-semibold">Name:</span>
                        <p>{{ $order->shipping_name }}</p>
                    </div>
                    <div>
                        <span class="font-semibold">Email:</span>
                        <p>{{ $order->shipping_email }}</p>
                    </div>
                    @if ($order->shipping_phone)
                        <div>
                            <span class="font-semibold">Phone:</span>
                            <p>{{ $order->shipping_phone }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Shipping Address --}}
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-bold">Shipping Address</h2>
                <div class="text-sm text-gray-700 dark:text-gray-300">
                    <p>{{ $order->shipping_address }}</p>
                    <p>{{ $order->shipping_city }}{{ $order->shipping_state ? ', ' . $order->shipping_state : '' }} {{ $order->shipping_zip }}</p>
                    <p>{{ $order->shipping_country }}</p>
                </div>
            </div>

            {{-- Payment Info --}}
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-bold">Payment Information</h2>
                <div class="space-y-2 text-sm">
                    <div>
                        <span class="font-semibold">Method:</span>
                        <p class="capitalize">{{ $order->payment_method }}</p>
                    </div>
                    <div>
                        <span class="font-semibold">Status:</span>
                        <p class="capitalize">{{ $order->payment_status }}</p>
                    </div>
                </div>
            </div>

            {{-- Order Dates --}}
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-bold">Order Timeline</h2>
                <div class="space-y-2 text-sm">
                    <div>
                        <span class="font-semibold">Placed:</span>
                        <p>{{ $order->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                    <div>
                        <span class="font-semibold">Last Updated:</span>
                        <p>{{ $order->updated_at->format('M d, Y g:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

