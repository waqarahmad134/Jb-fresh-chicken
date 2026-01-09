@extends('layouts.admin')

@section('title', 'Home Banners - Admin Panel')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-secondary">Manage Home Banners</h1>
            <p class="text-sm text-gray-500">Control the hero carousel that appears on the homepage.</p>
        </div>
        <a href="{{ route('admin.banners.create') }}" class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white transition hover:bg-secondary">
            Add New Banner
        </a>
    </div>

    <div class="mt-6 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">Preview</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">Title</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">Subtitle</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">Button</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">Order</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                @forelse($banners as $banner)
                    <tr>
                        <td class="px-4 py-3">
                            @php
                                $bannerImage = $banner->image_url ? (str_starts_with($banner->image_url, 'http') ? $banner->image_url : asset('public'.$banner->image_url)) : $placeholderImage;
                            @endphp
                            <img src="{{ $bannerImage }}" alt="{{ $banner->title }}" class="h-20 w-32 rounded border border-gray-200 object-cover" loading="lazy">
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $banner->title }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-300">{{ $banner->subtitle ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-300">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $banner->button_label ?? 'Shop Now' }}</p>
                            <span class="block text-xs text-primary">{{ $banner->button_url ?? url('/') }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-200">{{ $banner->sort_order }}</td>
                        <td class="px-4 py-3 text-sm font-semibold">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $banner->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/60 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/60 dark:text-red-300' }}">
                                {{ $banner->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-sm font-semibold text-gray-500 dark:text-gray-300">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.banners.edit', $banner) }}" class="rounded-md border border-gray-300 px-3 py-1 text-xs font-semibold text-gray-700 transition hover:border-primary hover:text-primary">Edit</a>
                                <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('Delete this banner?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md border border-red-300 px-3 py-1 text-xs font-semibold text-red-600 transition hover:bg-red-600 hover:text-white">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                            No banners yet. <a href="{{ route('admin.banners.create') }}" class="font-semibold text-primary hover:text-secondary">Create one now</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
