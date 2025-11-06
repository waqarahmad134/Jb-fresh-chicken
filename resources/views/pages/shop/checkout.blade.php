@extends('layouts.app')

@section('title', 'Checkout - ' . ($siteSettings['site_name'] ?? config('app.name')))

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cardRadio = document.getElementById('card');
        const codRadio = document.getElementById('cod');
        const cardDetails = document.getElementById('card-details');
        
        function toggleCardDetails() {
            if (cardRadio.checked) {
                cardDetails.classList.remove('hidden');
            } else {
                cardDetails.classList.add('hidden');
            }
        }
        
        cardRadio?.addEventListener('change', toggleCardDetails);
        codRadio?.addEventListener('change', toggleCardDetails);
        
        toggleCardDetails();
    });
</script>
@endpush

@section('content')
    <div class="mx-auto max-w-4xl">
        <h1 class="mb-10 text-center text-4xl font-extrabold text-secondary">Checkout</h1>
        
        <div class="grid grid-cols-1 gap-12 md:grid-cols-2">
            {{-- Order Summary --}}
            <div class="rounded-lg bg-amber-50 p-6 dark:bg-gray-800">
                <h2 class="mb-4 border-b pb-2 text-2xl font-bold dark:border-gray-600">Order Summary</h2>
                <div class="space-y-3">
                    @foreach ($cartItems as $item)
                        <div class="flex justify-between">
                            <span>{{ $item->product->name }} x {{ $item->quantity }}</span>
                            <span class="font-medium">PKR {{ number_format($item->price * $item->quantity, 2) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 flex justify-between border-t pt-4 text-xl font-bold dark:border-gray-600">
                    <span>Total</span>
                    <span>PKR {{ number_format($cartTotal, 2) }}</span>
                </div>
            </div>

            {{-- Checkout Form --}}
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-md dark:border-gray-700 dark:bg-gray-800">
                <form action="{{ route('shop.checkout.store') }}" method="POST">
                    @csrf
                    
                    <h2 class="mb-4 text-2xl font-bold">Shipping & Payment</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Full Name</label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                value="{{ $user->name }}" 
                                required
                                class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 @error('name') border-red-500 @enderror"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ $user->email }}" 
                                required
                                class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 @error('email') border-red-500 @enderror"
                            >
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                            <input 
                                type="tel" 
                                id="phone" 
                                name="phone" 
                                placeholder="0303-9345647"
                                class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 @error('phone') border-red-500 @enderror"
                            >
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                            <input 
                                type="text" 
                                id="address" 
                                name="address" 
                                required
                                class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 @error('address') border-red-500 @enderror"
                            >
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 dark:text-gray-300">City</label>
                            <input 
                                type="text" 
                                id="city" 
                                name="city" 
                                required
                                class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 @error('city') border-red-500 @enderror"
                            >
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="state" class="block text-sm font-medium text-gray-700 dark:text-gray-300">State/Province</label>
                                <input 
                                    type="text" 
                                    id="state" 
                                    name="state" 
                                    class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 @error('state') border-red-500 @enderror"
                                >
                                @error('state')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="zip" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Postal Code</label>
                                <input 
                                    type="text" 
                                    id="zip" 
                                    name="zip" 
                                    required
                                    class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 @error('zip') border-red-500 @enderror"
                                >
                                @error('zip')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Country</label>
                            <input 
                                type="text" 
                                id="country" 
                                name="country" 
                                value="Pakistan"
                                required
                                class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 @error('country') border-red-500 @enderror"
                            >
                            @error('country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Payment Method --}}
                        <div class="pt-4">
                            <h3 class="text-lg font-medium">Payment Method</h3>
                            <div class="mt-2 space-y-2">
                                <div class="flex cursor-pointer items-center rounded-md border border-gray-300 p-3 dark:border-gray-600">
                                    <input 
                                        type="radio" 
                                        name="payment_method" 
                                        id="card" 
                                        value="card" 
                                        checked
                                        class="h-4 w-4 text-primary focus:ring-secondary"
                                    >
                                    <label for="card" class="ml-3 block cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Credit / Debit Card
                                    </label>
                                </div>
                                <div class="flex cursor-pointer items-center rounded-md border border-gray-300 p-3 dark:border-gray-600">
                                    <input 
                                        type="radio" 
                                        name="payment_method" 
                                        id="cod" 
                                        value="cod"
                                        class="h-4 w-4 text-primary focus:ring-secondary"
                                    >
                                    <label for="cod" class="ml-3 block cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Cash on Delivery (COD)
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Card Details --}}
                        <div id="card-details" class="space-y-4 pt-2">
                            <div>
                                <label for="card-number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Card Number</label>
                                <input 
                                    type="text" 
                                    id="card-number" 
                                    name="card_number"
                                    placeholder="•••• •••• •••• ••••"
                                    class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700"
                                >
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="expiry" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expiry</label>
                                    <input 
                                        type="text" 
                                        id="expiry" 
                                        name="expiry"
                                        placeholder="MM / YY"
                                        class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700"
                                    >
                                </div>
                                <div>
                                    <label for="cvc" class="block text-sm font-medium text-gray-700 dark:text-gray-300">CVC</label>
                                    <input 
                                        type="text" 
                                        id="cvc" 
                                        name="cvc"
                                        placeholder="•••"
                                        class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <button 
                        type="submit" 
                        class="mt-8 w-full rounded-md bg-primary px-4 py-3 text-lg font-bold text-white shadow-md transition-colors hover:bg-secondary"
                    >
                        Place Order
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
