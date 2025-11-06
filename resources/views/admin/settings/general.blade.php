@extends('layouts.admin')

@section('title', 'General Settings - Admin Panel')

@section('content')
    <h1 class="mb-6 text-3xl font-extrabold text-secondary">General Settings</h1>

    <div class="max-w-3xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <form action="{{ route('admin.settings.general.update') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="site_name" class="block text-sm font-medium">Site Name</label>
                <input type="text" id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700" required>
                @error('site_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="logo_url" class="block text-sm font-medium">Logo URL</label>
                <input type="text" id="logo_url" name="logo_url" value="{{ old('logo_url', $settings['logo_url']) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                @error('logo_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-gray-500">Leave empty to use default SVG logo.</p>
            </div>

            <div>
                <label for="meta_description" class="block text-sm font-medium">Meta Description</label>
                <textarea id="meta_description" name="meta_description" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">{{ old('meta_description', $settings['meta_description']) }}</textarea>
                @error('meta_description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="stripe_key" class="block text-sm font-medium">Stripe Key</label>
                <input type="password" id="stripe_key" name="stripe_key" value="{{ old('stripe_key', $settings['stripe_key']) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                @error('stripe_key')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <fieldset class="space-y-3 border-t pt-4 dark:border-gray-700">
                <legend class="text-sm font-medium">Feature Toggles</legend>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="blog_enabled" value="1" {{ old('blog_enabled', $settings['blog_enabled']) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                    <span class="text-sm">Enable Blog Section</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="newsletter_enabled" value="1" {{ old('newsletter_enabled', $settings['newsletter_enabled']) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                    <span class="text-sm">Enable Newsletter Sections</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="maintenance_mode" value="1" {{ old('maintenance_mode', $settings['maintenance_mode']) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                    <span class="text-sm">Enable Maintenance Mode</span>
                </label>
            </fieldset>

            <div class="border-t pt-4 dark:border-gray-700">
                <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-secondary">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
@endsection
