@extends('layouts.admin')

@section('title', 'Robots.txt Management - Admin Panel')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-3xl font-extrabold text-secondary">Robots.txt Management</h1>
        <a href="{{ url('/robots.txt') }}" target="_blank" class="text-sm text-primary hover:underline">
            View Current File →
        </a>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Edit your robots.txt file. This file tells search engines which pages they can and cannot access on your site.
            </p>
        </div>

        <form action="{{ route('admin.settings.robots.update') }}" method="POST" class="p-6">
            @csrf

            <div class="mb-4">
                <label for="content" class="mb-2 block text-sm font-medium">robots.txt Content</label>
                <textarea 
                    id="content" 
                    name="content" 
                    rows="15" 
                    class="block w-full rounded-md border border-gray-300 bg-light px-3 py-2 font-mono text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700"
                    placeholder="User-agent: *&#10;Disallow:"
                >{{ old('content', $content) }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-gray-500">
                    <strong>Common directives:</strong><br>
                    <code>User-agent: *</code> - Applies to all search engines<br>
                    <code>Disallow: /</code> - Blocks all pages<br>
                    <code>Disallow: /admin</code> - Blocks admin pages<br>
                    <code>Allow: /</code> - Allows all pages<br>
                    <code>Sitemap: https://yoursite.com/sitemap.xml</code> - Location of sitemap
                </p>
            </div>

            <div class="flex items-center justify-between border-t pt-4 dark:border-gray-700">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    File location: <code class="rounded bg-gray-100 px-2 py-1 dark:bg-gray-700">{{ public_path('robots.txt') }}</code>
                </div>
                <div class="flex gap-3">
                    <a 
                        href="{{ route('admin.settings.general') }}" 
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-secondary"
                    >
                        Save robots.txt
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/40">
        <h3 class="mb-2 text-sm font-semibold text-blue-900 dark:text-blue-300">📚 About robots.txt</h3>
        <ul class="space-y-1 text-xs text-blue-800 dark:text-blue-400">
            <li>• robots.txt is a file that tells search engines which pages to crawl and index</li>
            <li>• It's publicly accessible at <code class="rounded bg-blue-100 px-1 dark:bg-blue-800">{{ url('/robots.txt') }}</code></li>
            <li>• Changes take effect immediately after saving</li>
            <li>• Make sure to test your robots.txt with Google Search Console</li>
        </ul>
    </div>
@endsection

