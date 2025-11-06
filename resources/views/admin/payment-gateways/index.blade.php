@extends('layouts.admin')

@section('title', 'Payment Gateways - Admin Panel')

@section('content')
<div class="space-y-6">
    <div class="mb-6">
        <h1 class="text-3xl font-extrabold text-secondary">Payment Gateways</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Manage payment gateway settings and API keys</p>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 border border-green-400 text-green-700 px-4 py-3">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-100 border border-red-400 text-red-700 px-4 py-3">
            {{ session('error') }}
        </div>
    @endif

    <div class="space-y-6">
        @foreach($gateways as $key => $gateway)
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $gateway['name'] }}</h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Status: <span class="font-medium {{ $gateway['enabled'] == '1' ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}">{{ $gateway['enabled'] == '1' ? 'Enabled' : 'Disabled' }}</span>
                            </p>
                        </div>
                        <form action="{{ route('admin.payment-gateways.update', $key) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="enabled" value="{{ $gateway['enabled'] == '1' ? '0' : '1' }}">
                            <button type="submit" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 {{ $gateway['enabled'] == '1' ? 'bg-primary' : 'bg-gray-200 dark:bg-gray-700' }}">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $gateway['enabled'] == '1' ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </form>
                    </div>
                </div>

                <form action="{{ route('admin.payment-gateways.update', $key) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="enabled" value="{{ $gateway['enabled'] }}">

                    <div class="space-y-4">
                        @if($gateway['enabled'] == '0')
                            <div class="rounded-md bg-yellow-50 p-4 dark:bg-yellow-900/20">
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">This payment gateway is currently disabled. Enable it above to configure settings.</p>
                            </div>
                        @endif
                        @if($key === 'stripe')
                            <div>
                                <label for="stripe_public_key" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Public Key</label>
                                <input type="text" id="stripe_public_key" name="public_key" value="{{ old('public_key', $gateway['public_key']) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" {{ $gateway['enabled'] == '0' ? 'disabled' : '' }}>
                                @error('public_key')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="stripe_secret_key" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Secret Key</label>
                                <input type="password" id="stripe_secret_key" name="secret_key" value="{{ old('secret_key', $gateway['secret_key']) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" {{ $gateway['enabled'] == '0' ? 'disabled' : '' }}>
                                @error('secret_key')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="test_mode" value="1" {{ old('test_mode', $gateway['test_mode']) == '1' ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-700" {{ $gateway['enabled'] == '0' ? 'disabled' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Test Mode</span>
                                </label>
                            </div>

                        @elseif($key === 'paypal')
                            <div>
                                <label for="paypal_client_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Client ID</label>
                                <input type="text" id="paypal_client_id" name="client_id" value="{{ old('client_id', $gateway['client_id']) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" {{ $gateway['enabled'] == '0' ? 'disabled' : '' }}>
                                @error('client_id')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="paypal_secret" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Secret</label>
                                <input type="password" id="paypal_secret" name="secret" value="{{ old('secret', $gateway['secret']) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" {{ $gateway['enabled'] == '0' ? 'disabled' : '' }}>
                                @error('secret')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="paypal_mode" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mode</label>
                                <select id="paypal_mode" name="mode" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" {{ $gateway['enabled'] == '0' ? 'disabled' : '' }}>
                                    <option value="sandbox" {{ old('mode', $gateway['mode']) == 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                                    <option value="live" {{ old('mode', $gateway['mode']) == 'live' ? 'selected' : '' }}>Live</option>
                                </select>
                                @error('mode')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                        @elseif($key === 'razorpay')
                            <div>
                                <label for="razorpay_key_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Key ID</label>
                                <input type="text" id="razorpay_key_id" name="key_id" value="{{ old('key_id', $gateway['key_id']) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" {{ $gateway['enabled'] == '0' ? 'disabled' : '' }}>
                                @error('key_id')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="razorpay_key_secret" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Key Secret</label>
                                <input type="password" id="razorpay_key_secret" name="key_secret" value="{{ old('key_secret', $gateway['key_secret']) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" {{ $gateway['enabled'] == '0' ? 'disabled' : '' }}>
                                @error('key_secret')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                        @elseif($key === 'bank_transfer')
                            <div>
                                <label for="bank_account_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Account Name</label>
                                <input type="text" id="bank_account_name" name="account_name" value="{{ old('account_name', $gateway['account_name']) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" {{ $gateway['enabled'] == '0' ? 'disabled' : '' }}>
                                @error('account_name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="bank_account_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Account Number</label>
                                <input type="text" id="bank_account_number" name="account_number" value="{{ old('account_number', $gateway['account_number']) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" {{ $gateway['enabled'] == '0' ? 'disabled' : '' }}>
                                @error('account_number')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bank Name</label>
                                <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $gateway['bank_name']) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" {{ $gateway['enabled'] == '0' ? 'disabled' : '' }}>
                                @error('bank_name')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="bank_iban" class="block text-sm font-medium text-gray-700 dark:text-gray-300">IBAN</label>
                                <input type="text" id="bank_iban" name="iban" value="{{ old('iban', $gateway['iban']) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" {{ $gateway['enabled'] == '0' ? 'disabled' : '' }}>
                                @error('iban')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                        @elseif($key === 'cod')
                            <div>
                                <label for="cod_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                <textarea id="cod_description" name="description" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" {{ $gateway['enabled'] == '0' ? 'disabled' : '' }}>{{ old('description', $gateway['description']) }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This description will be shown to customers during checkout</p>
                            </div>
                        @endif

                        <div class="flex justify-end pt-4">
                            <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2" {{ $gateway['enabled'] == '0' ? 'disabled' : '' }}>
                                Update Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endforeach
    </div>
@endsection

