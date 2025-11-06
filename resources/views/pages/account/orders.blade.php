@extends('layouts.app')

@section('title', 'Order History - ' . ($siteSettings['site_name'] ?? config('app.name')))

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('[data-order-toggle]').forEach(button => {
            button.addEventListener('click', function() {
                const orderId = this.getAttribute('data-order-toggle');
                const details = document.getElementById(`order-details-${orderId}`);
                const isExpanded = details.classList.contains('max-h-96');
                
                if (isExpanded) {
                    details.classList.remove('max-h-96', 'opacity-100', 'mt-4', 'pt-4');
                    details.classList.add('max-h-0', 'opacity-0');
                    this.textContent = 'View Details';
                } else {
                    details.classList.remove('max-h-0', 'opacity-0');
                    details.classList.add('max-h-96', 'opacity-100', 'mt-4', 'pt-4');
                    this.textContent = 'Hide Details';
                }
            });
        });
    });
</script>
@endpush

@section('content')
    <div class="mx-auto max-w-4xl rounded-lg border border-gray-200 bg-white p-8 shadow-lg dark:border-gray-700 dark:bg-gray-800">
        <h1 class="mb-6 border-b pb-4 text-3xl font-extrabold text-secondary dark:border-gray-600">My Orders</h1>
        
        @if ($orders->count() > 0)
            <div class="space-y-4">
                @foreach ($orders as $order)
                    <div class="rounded-md border p-4 transition-all duration-300 dark:border-gray-700">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <p class="text-lg font-bold">Order #{{ $order->order_number }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Date: {{ $order->created_at->format('Y-m-d') }}
                                </p>
                            </div>
                            <div class="text-gray-600 dark:text-gray-300">
                                Total: <span class="font-bold text-dark dark:text-light">PKR {{ number_format($order->total, 2) }}</span>
                            </div>
                            <div>
                                <span class="rounded-full px-3 py-1 text-sm font-semibold 
                                    @if($order->status === 'delivered') bg-green-100 text-green-800
                                    @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            <button 
                                data-order-toggle="{{ $order->id }}"
                                class="font-semibold text-primary hover:underline focus:outline-none"
                            >
                                View Details
                            </button>
                        </div>
                        
                        <div 
                            id="order-details-{{ $order->id }}"
                            class="max-h-0 overflow-hidden opacity-0 transition-all duration-500 ease-in-out"
                        >
                            <div class="border-t dark:border-gray-600">
                                <h4 class="mb-2 text-md font-bold text-gray-800 dark:text-gray-200">Order Items:</h4>
                                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                    @foreach ($order->items as $item)
                                        <li class="flex justify-between">
                                            <span>{{ $item->product->name ?? 'Product' }} &times; {{ $item->quantity }}</span>
                                            <span class="font-medium">PKR {{ number_format($item->price * $item->quantity, 2) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600 dark:text-gray-400">You have not placed any orders yet.</p>
        @endif

        <div class="mt-8 text-center">
            <a 
                href="{{ route('account.profile') }}" 
                class="font-semibold text-primary hover:underline"
            >
                &larr; Back to Profile
            </a>
        </div>
    </div>
@endsection

