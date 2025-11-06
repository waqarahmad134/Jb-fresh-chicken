<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentGatewayController extends Controller
{
    /**
     * Display payment gateways management page
     */
    public function index(): View
    {
        // Get all payment gateway settings
        $gateways = [
            'stripe' => [
                'name' => 'Stripe',
                'enabled' => Setting::where('key', 'payment_stripe_enabled')->first()?->value ?? '0',
                'public_key' => Setting::where('key', 'payment_stripe_public_key')->first()?->value ?? '',
                'secret_key' => Setting::where('key', 'payment_stripe_secret_key')->first()?->value ?? '',
                'test_mode' => Setting::where('key', 'payment_stripe_test_mode')->first()?->value ?? '1',
            ],
            'paypal' => [
                'name' => 'PayPal',
                'enabled' => Setting::where('key', 'payment_paypal_enabled')->first()?->value ?? '0',
                'client_id' => Setting::where('key', 'payment_paypal_client_id')->first()?->value ?? '',
                'secret' => Setting::where('key', 'payment_paypal_secret')->first()?->value ?? '',
                'mode' => Setting::where('key', 'payment_paypal_mode')->first()?->value ?? 'sandbox',
            ],
            'razorpay' => [
                'name' => 'Razorpay',
                'enabled' => Setting::where('key', 'payment_razorpay_enabled')->first()?->value ?? '0',
                'key_id' => Setting::where('key', 'payment_razorpay_key_id')->first()?->value ?? '',
                'key_secret' => Setting::where('key', 'payment_razorpay_key_secret')->first()?->value ?? '',
            ],
            'bank_transfer' => [
                'name' => 'Bank Transfer',
                'enabled' => Setting::where('key', 'payment_bank_transfer_enabled')->first()?->value ?? '0',
                'account_name' => Setting::where('key', 'payment_bank_account_name')->first()?->value ?? '',
                'account_number' => Setting::where('key', 'payment_bank_account_number')->first()?->value ?? '',
                'bank_name' => Setting::where('key', 'payment_bank_name')->first()?->value ?? '',
                'iban' => Setting::where('key', 'payment_bank_iban')->first()?->value ?? '',
            ],
            'cod' => [
                'name' => 'Cash on Delivery (COD)',
                'enabled' => Setting::where('key', 'payment_cod_enabled')->first()?->value ?? '1',
                'description' => Setting::where('key', 'payment_cod_description')->first()?->value ?? 'Pay when you receive your order',
            ],
        ];

        return view('admin.payment-gateways.index', compact('gateways'));
    }

    /**
     * Update payment gateway settings
     */
    public function update(Request $request, string $gateway)
    {
        $validated = $request->validate([
            'enabled' => 'nullable|boolean',
            'public_key' => 'nullable|string|max:255',
            'secret_key' => 'nullable|string|max:255',
            'client_id' => 'nullable|string|max:255',
            'secret' => 'nullable|string|max:255',
            'key_id' => 'nullable|string|max:255',
            'key_secret' => 'nullable|string|max:255',
            'test_mode' => 'nullable|boolean',
            'mode' => 'nullable|string|in:sandbox,live',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $settings = [];

        // Common enabled setting
        if ($request->has('enabled')) {
            $settings["payment_{$gateway}_enabled"] = $request->boolean('enabled') ? '1' : '0';
        }

        // Gateway-specific settings
        switch ($gateway) {
            case 'stripe':
                if ($request->has('public_key')) {
                    $settings['payment_stripe_public_key'] = $request->public_key;
                }
                if ($request->has('secret_key')) {
                    $settings['payment_stripe_secret_key'] = $request->secret_key;
                }
                if ($request->has('test_mode')) {
                    $settings['payment_stripe_test_mode'] = $request->boolean('test_mode') ? '1' : '0';
                }
                break;

            case 'paypal':
                if ($request->has('client_id')) {
                    $settings['payment_paypal_client_id'] = $request->client_id;
                }
                if ($request->has('secret')) {
                    $settings['payment_paypal_secret'] = $request->secret;
                }
                if ($request->has('mode')) {
                    $settings['payment_paypal_mode'] = $request->mode;
                }
                break;

            case 'razorpay':
                if ($request->has('key_id')) {
                    $settings['payment_razorpay_key_id'] = $request->key_id;
                }
                if ($request->has('key_secret')) {
                    $settings['payment_razorpay_key_secret'] = $request->key_secret;
                }
                break;

            case 'bank_transfer':
                if ($request->has('account_name')) {
                    $settings['payment_bank_account_name'] = $request->account_name;
                }
                if ($request->has('account_number')) {
                    $settings['payment_bank_account_number'] = $request->account_number;
                }
                if ($request->has('bank_name')) {
                    $settings['payment_bank_name'] = $request->bank_name;
                }
                if ($request->has('iban')) {
                    $settings['payment_bank_iban'] = $request->iban;
                }
                break;

            case 'cod':
                if ($request->has('description')) {
                    $settings['payment_cod_description'] = $request->description;
                }
                break;
        }

        // Update settings
        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'type' => 'string',
                    'group' => 'payment',
                    'description' => "Payment gateway setting for {$gateway}"
                ]
            );
        }

        return redirect()->route('admin.payment-gateways.index')
            ->with('success', ucfirst($gateway) . ' payment gateway settings updated successfully!');
    }
}
