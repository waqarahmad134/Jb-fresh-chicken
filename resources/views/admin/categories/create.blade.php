@extends('layouts.admin')

@section('title', 'Create Product Category - Admin Panel')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.product-categories.index') }}" class="text-sm text-gray-600 hover:text-primary dark:text-gray-400">← Back to Categories</a>
    </div>

    <h1 class="mb-6 text-3xl font-extrabold text-secondary">Create Product Category</h1>

    <div class="max-w-2xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <form action="{{ route('admin.product-categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium">Name <span class="text-red-600">*</span></label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="slug" class="block text-sm font-medium">Slug <small class="text-gray-500">(auto-generated if empty)</small></label>
                <input id="slug" name="slug" type="text" value="{{ old('slug') }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                @error('slug')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Category Image</label>
                
                <div class="space-y-4">
                    <!-- File Upload Option -->
                    <div>
                        <label for="featured_image" class="block text-sm font-medium mb-1">Upload Image</label>
                        <input type="file" id="featured_image" name="featured_image" accept="image/*" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700" onchange="previewFeaturedImage(this)">
                        @error('featured_image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-gray-500">Upload an image file (max 5MB)</p>
                    </div>

                    <!-- OR Divider -->
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="bg-white dark:bg-gray-800 px-2 text-gray-500">OR</span>
                        </div>
                    </div>

                    <!-- URL Input Option -->
                    <div>
                        <label for="image_url" class="block text-sm font-medium mb-1">Image URL</label>
                        <input type="text" id="image_url" name="image_url" value="{{ old('image_url') }}" placeholder="https://example.com/image.jpg" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700" onchange="previewImageUrl(this)">
                        @error('image_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-gray-500">Enter an image URL from the web</p>
                    </div>

                    <!-- Image Preview -->
                    <div id="image-preview-container" class="hidden">
                        <label class="block text-sm font-medium mb-2">Preview</label>
                        <div class="relative inline-block">
                            <img id="image-preview" src="" alt="Preview" class="max-w-full h-48 rounded border border-gray-300 object-cover">
                            <button type="button" onclick="clearImagePreview()" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <label class="flex items-center gap-2 border-t pt-4 dark:border-gray-700">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                <span class="text-sm">Active</span>
            </label>

            <div class="flex gap-3 border-t pt-4 dark:border-gray-700">
                <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-secondary">Create</button>
                <a href="{{ route('admin.product-categories.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        function previewFeaturedImage(input) {
            const preview = document.getElementById('image-preview');
            const container = document.getElementById('image-preview-container');
            const urlInput = document.getElementById('image_url');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.classList.remove('hidden');
                    // Clear URL input when file is selected
                    if (urlInput) urlInput.value = '';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewImageUrl(input) {
            const preview = document.getElementById('image-preview');
            const container = document.getElementById('image-preview-container');
            const fileInput = document.getElementById('featured_image');
            
            if (input.value && input.value.trim() !== '') {
                preview.src = input.value;
                container.classList.remove('hidden');
                // Clear file input when URL is entered
                if (fileInput) fileInput.value = '';
            } else {
                container.classList.add('hidden');
            }
        }

        function clearImagePreview() {
            const preview = document.getElementById('image-preview');
            const container = document.getElementById('image-preview-container');
            const fileInput = document.getElementById('featured_image');
            const urlInput = document.getElementById('image_url');
            
            preview.src = '';
            container.classList.add('hidden');
            if (fileInput) fileInput.value = '';
            if (urlInput) urlInput.value = '';
        }

        // Preview URL on page load if it exists
        document.addEventListener('DOMContentLoaded', function() {
            const urlInput = document.getElementById('image_url');
            if (urlInput && urlInput.value) {
                previewImageUrl(urlInput);
            }
        });
    </script>
@endsection


