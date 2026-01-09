@extends('layouts.admin')

@section('title', 'Edit Banner - Admin Panel')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-secondary">Edit Banner</h1>
            <p class="text-sm text-gray-500">Update the content that appears in the hero carousel.</p>
        </div>
        <a href="{{ route('admin.banners.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Back to Banners</a>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="title" class="block text-sm font-medium">Title <span class="text-red-600">*</span></label>
                    <input id="title" name="title" type="text" value="{{ old('title', $banner->title) }}" required class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="subtitle" class="block text-sm font-medium">Subtitle</label>
                    <input id="subtitle" name="subtitle" type="text" value="{{ old('subtitle', $banner->subtitle) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    @error('subtitle')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="button_label" class="block text-sm font-medium">Button Label</label>
                    <input id="button_label" name="button_label" type="text" value="{{ old('button_label', $banner->button_label) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    @error('button_label')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="button_url" class="block text-sm font-medium">Button URL</label>
                    <input id="button_url" name="button_url" type="url" value="{{ old('button_url', $banner->button_url) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    @error('button_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Banner Image</label>
                @if($banner->image_url)
                    <div class="mb-4" id="current-image-container">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium">Current Image</label>
                            <label class="flex items-center gap-2 cursor-pointer text-red-600 hover:text-red-700">
                                <input type="checkbox" name="remove_image" value="1" id="remove_image" class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500" onchange="handleRemoveImage(this)">
                                <span class="text-sm font-medium">Remove Image</span>
                            </label>
                        </div>
                        <div class="relative inline-block">
                            <img src="{{ str_starts_with($banner->image_url, 'http') ? $banner->image_url : asset('public' . $banner->image_url) }}" alt="{{ $banner->title }}" class="max-w-full h-48 rounded border border-gray-300 object-cover" id="current-image">
                            <div id="remove-overlay" class="hidden absolute inset-0 rounded bg-red-500 bg-opacity-50 flex items-center justify-center">
                                <span class="text-white font-bold text-lg">Will be removed</span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="space-y-4">
                    <div>
                        <label for="featured_image" class="block text-sm font-medium mb-1">Upload New Image</label>
                        <input type="file" id="featured_image" name="featured_image" accept="image/*" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700" onchange="previewFeaturedImage(this)">
                        @error('featured_image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-gray-500">Upload a new image to replace the current one.</p>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="bg-white dark:bg-gray-800 px-2 text-gray-500">OR</span>
                        </div>
                    </div>

                    <div>
                        <label for="image_url" class="block text-sm font-medium mb-1">Image URL</label>
                        <input type="text" id="image_url" name="image_url" value="{{ old('image_url', $banner->image_url) }}" placeholder="https://example.com/image.jpg" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700" onchange="previewImageUrl(this)">
                        @error('image_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-gray-500">Leave empty to keep the current image.</p>
                    </div>

                    <div id="image-preview-container" class="hidden">
                        <label class="block text-sm font-medium mb-2">Preview</label>
                        <div class="relative inline-block">
                            <img id="image-preview" src="" alt="Preview" class="max-w-full h-48 rounded border border-gray-300 object-cover">
                            <button type="button" onclick="clearImagePreview()" class="absolute top-2 right-2 rounded-full bg-red-500 p-1 text-white hover:bg-red-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="sort_order" class="block text-sm font-medium">Sort Order</label>
                    <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $banner->sort_order) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    @error('sort_order')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-end">
                    <label class="flex items-center gap-2 text-sm font-semibold">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $banner->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                        Active
                    </label>
                </div>
            </div>

            <div class="flex gap-3 border-t pt-4 dark:border-gray-700">
                <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white transition hover:bg-secondary">Save Changes</button>
                <a href="{{ route('admin.banners.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function previewFeaturedImage(input) {
            const preview = document.getElementById('image-preview');
            const container = document.getElementById('image-preview-container');
            const urlInput = document.getElementById('image_url');
            const currentImage = document.getElementById('current-image');
            const removeCheckbox = document.getElementById('remove_image');
            const removeOverlay = document.getElementById('remove-overlay');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.classList.remove('hidden');
                    if (currentImage) currentImage.closest('.mb-4').style.display = 'none';
                    if (urlInput) urlInput.value = '';
                    if (removeCheckbox) {
                        removeCheckbox.checked = false;
                        if (removeOverlay) removeOverlay.classList.add('hidden');
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewImageUrl(input) {
            const preview = document.getElementById('image-preview');
            const container = document.getElementById('image-preview-container');
            const fileInput = document.getElementById('featured_image');
            const currentImage = document.getElementById('current-image');
            const removeCheckbox = document.getElementById('remove_image');
            const removeOverlay = document.getElementById('remove-overlay');

            if (input.value && input.value.trim() !== '') {
                preview.src = input.value;
                container.classList.remove('hidden');
                if (currentImage) currentImage.closest('.mb-4').style.display = 'none';
                if (fileInput) fileInput.value = '';
                if (removeCheckbox) {
                    removeCheckbox.checked = false;
                    if (removeOverlay) removeOverlay.classList.add('hidden');
                }
            } else {
                container.classList.add('hidden');
                if (currentImage) currentImage.closest('.mb-4').style.display = 'block';
            }
        }

        function clearImagePreview() {
            const preview = document.getElementById('image-preview');
            const container = document.getElementById('image-preview-container');
            const fileInput = document.getElementById('featured_image');
            const urlInput = document.getElementById('image_url');
            const currentImage = document.getElementById('current-image');
            const removeCheckbox = document.getElementById('remove_image');

            preview.src = '';
            container.classList.add('hidden');
            if (fileInput) fileInput.value = '';
            if (urlInput) urlInput.value = '';
            if (removeCheckbox) removeCheckbox.checked = false;
            if (currentImage) {
                currentImage.closest('.mb-4').style.display = 'block';
                handleRemoveImage(removeCheckbox);
            }
        }

        function handleRemoveImage(checkbox) {
            const overlay = document.getElementById('remove-overlay');
            const fileInput = document.getElementById('featured_image');
            const urlInput = document.getElementById('image_url');
            const previewContainer = document.getElementById('image-preview-container');

            if (!checkbox) return;

            if (checkbox.checked) {
                if (overlay) overlay.classList.remove('hidden');
                if (fileInput) fileInput.value = '';
                if (urlInput) urlInput.value = '';
                if (previewContainer) previewContainer.classList.add('hidden');
            } else {
                if (overlay) overlay.classList.add('hidden');
            }
        }
    </script>
@endpush
