@extends('layouts.admin')

@section('title', 'Product Categories - Admin Panel')

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <style>
        .sortable-drag {
            background-color: rgb(243 244 246);
        }
        .dark .sortable-drag {
            background-color: rgb(55 65 81);
        }
    </style>
@endpush

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-3xl font-extrabold text-secondary">Product Categories</h1>
        <a href="{{ route('admin.product-categories.create') }}" class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-secondary">Add Category</a>
    </div>

    <div class="mb-6">
        <form action="{{ route('admin.product-categories.index') }}" method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search categories..." class="flex-1 rounded-md border border-gray-300 bg-light px-4 py-2 text-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
            <button type="submit" class="rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">Search</button>
            @if ($search)
                <a href="{{ route('admin.product-categories.index') }}" class="rounded-md bg-gray-500 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-600">Clear</a>
            @endif
        </form>
    </div>

    @if(!$search)
        <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 p-3 text-sm text-blue-800 dark:border-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
            <strong>💡 Tip:</strong> Drag and drop categories to reorder them. Changes are saved automatically.
        </div>
    @endif

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">{{ !$search ? 'Drag' : '' }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Image</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody id="categories-sortable" class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @forelse ($categories as $category)
                        <tr data-id="{{ $category->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ !$search ? 'cursor-move' : '' }}">
                            @if(!$search)
                                <td class="px-6 py-4 cursor-grab active:cursor-grabbing">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                    </svg>
                                </td>
                            @else
                                <td></td>
                            @endif
                            <td class="px-6 py-4">
                                @if($category->image_url)
                                    <img src="{{ str_starts_with($category->image_url, 'http') ? $category->image_url : asset('public'.$category->image_url) }}" alt="{{ $category->name }}" class="h-10 w-10 rounded object-cover" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'40\' height=\'40\'%3E%3Crect fill=\'%23e5e7eb\' width=\'40\' height=\'40\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' fill=\'%239ca3af\' font-size=\'10\'%3ENo Image%3C/text%3E%3C/svg%3E'">
                                @else
                                    <div class="flex h-10 w-10 items-center justify-center rounded bg-gray-100 text-xs text-gray-400 dark:bg-gray-700">No Image</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $category->name }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $category->slug }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.product-categories.edit', $category) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                    <form action="{{ route('admin.product-categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ !$search ? '6' : '5' }}" class="px-6 py-10 text-center text-sm text-gray-500">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($categories->hasPages())
        <div class="mt-6">{{ $categories->links() }}</div>
    @endif
@endsection

@push('scripts')
    @if(!$search)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sortable = document.getElementById('categories-sortable');
            if (!sortable) return;

            let sortableInstance = new Sortable(sortable, {
                animation: 150,
                ghostClass: 'opacity-50',
                dragClass: 'sortable-drag',
                onEnd: function(evt) {
                    const rows = Array.from(sortable.querySelectorAll('tr[data-id]'));
                    const categories = rows.map((row, index) => ({
                        id: parseInt(row.getAttribute('data-id')),
                        sort_order: index + 1
                    }));

                    // Show saving indicator
                    const originalHtml = evt.item.innerHTML;
                    evt.item.innerHTML = '<td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Saving...</td>';

                    fetch('{{ route("admin.product-categories.update-sort-order") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ categories: categories })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Restore original HTML
                            evt.item.innerHTML = originalHtml;
                            // Show success message briefly
                            const successMsg = document.createElement('div');
                            successMsg.className = 'fixed top-20 right-4 rounded-lg bg-green-500 px-4 py-2 text-white shadow-lg z-50';
                            successMsg.textContent = 'Sort order updated!';
                            document.body.appendChild(successMsg);
                            setTimeout(() => successMsg.remove(), 2000);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        evt.item.innerHTML = originalHtml;
                        // Show error message
                        const errorMsg = document.createElement('div');
                        errorMsg.className = 'fixed top-20 right-4 rounded-lg bg-red-500 px-4 py-2 text-white shadow-lg z-50';
                        errorMsg.textContent = 'Failed to update sort order';
                        document.body.appendChild(errorMsg);
                        setTimeout(() => errorMsg.remove(), 3000);
                        // Reload to restore original order
                        setTimeout(() => location.reload(), 1000);
                    });
                }
            });
        });
    </script>
    @endif
@endpush


