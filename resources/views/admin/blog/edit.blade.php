@extends('layouts.admin')

@section('title', 'Edit Blog Post - Admin Panel')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.blog.index') }}" class="text-sm text-gray-600 hover:text-primary dark:text-gray-400">
            ← Back to Blog Posts
        </a>
    </div>

    <h1 class="mb-6 text-3xl font-extrabold text-secondary">Edit Blog Post: {{ $blog->title }}</h1>

    <div class="max-w-4xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <form action="{{ route('admin.blog.update', $blog) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="title" class="block text-sm font-medium">Title <span class="text-red-600">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title', $blog->title) }}" required class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium">Slug <small class="text-gray-500">(auto-generated if empty)</small></label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug', $blog->slug) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    @error('slug')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="blog_category_id" class="block text-sm font-medium">Category <span class="text-red-600">*</span></label>
                    <select id="blog_category_id" name="blog_category_id" required class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                        <option value="">Select a category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('blog_category_id', $blog->blog_category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('blog_category_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium mb-2">Featured Image</label>
                    
                    <!-- Show existing image if available -->
                    @if($blog->image_url)
                        <div class="mb-4" id="current-image-container">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium">Current Image</label>
                                <label class="flex items-center gap-2 cursor-pointer text-red-600 hover:text-red-700">
                                    <input type="checkbox" name="remove_image" value="1" id="remove_image" class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500" onchange="handleRemoveImage(this)">
                                    <span class="text-sm font-medium">Remove Image</span>
                                </label>
                            </div>
                            <div class="relative inline-block">
                                <img src="{{ str_starts_with($blog->image_url, 'http') ? $blog->image_url : asset('public' . $blog->image_url) }}" alt="{{ $blog->title }}" class="max-w-full h-48 rounded border border-gray-300 object-cover" id="current-image">
                                <div id="remove-overlay" class="hidden absolute inset-0 bg-red-500 bg-opacity-50 rounded flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">Will be removed</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <div class="space-y-4">
                        <!-- File Upload Option -->
                        <div>
                            <label for="featured_image" class="block text-sm font-medium mb-1">Upload New Image</label>
                            <input type="file" id="featured_image" name="featured_image" accept="image/*" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700" onchange="previewFeaturedImage(this)">
                            @error('featured_image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            <p class="mt-1 text-xs text-gray-500">Upload an image file (max 5MB). This will replace the current image.</p>
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
                            <input type="text" id="image_url" name="image_url" value="{{ old('image_url', $blog->image_url) }}" placeholder="https://example.com/image.jpg" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700" onchange="previewImageUrl(this)">
                            @error('image_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            <p class="mt-1 text-xs text-gray-500">Enter an image URL from the web. Leave empty to remove image.</p>
                        </div>

                        <!-- Image Preview -->
                        <div id="image-preview-container" class="hidden">
                            <label class="block text-sm font-medium mb-2">New Image Preview</label>
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

                <div class="sm:col-span-2">
                    <label for="excerpt" class="block text-sm font-medium">Excerpt</label>
                    <textarea id="excerpt" name="excerpt" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">{{ old('excerpt', $blog->excerpt) }}</textarea>
                    @error('excerpt')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-gray-500">Short summary of the post (max 500 characters)</p>
                </div>

                <div class="sm:col-span-2">
                    <label for="content" class="block text-sm font-medium">Content <span class="text-red-600">*</span></label>
                    <textarea id="content" name="content" rows="12" required class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">{{ old('content', $blog->content) }}</textarea>
                    @error('content')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-gray-500">Use the rich text editor. Click Code View (< / >) to add HTML directly.</p>
                </div>
            </div>

            <fieldset class="space-y-3 border-t pt-4 dark:border-gray-700">
                <legend class="text-sm font-medium">Post Options</legend>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_published" value="1" {{ old('is_published', $blog->is_published) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                    <span class="text-sm">Published (visible on blog)</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $blog->is_featured) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                    <span class="text-sm">Featured Post</span>
                </label>
            </fieldset>

            <div class="flex gap-3 border-t pt-4 dark:border-gray-700">
                <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-secondary">
                    Update Post
                </button>
                <a href="{{ route('admin.blog.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection

@push('head')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
    <script>
        {!! file_get_contents(resource_path('js/admin-editor.js')) !!}
    </script>
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
                    // Hide current image when new one is selected
                    if (currentImage) {
                        currentImage.closest('.mb-4').style.display = 'none';
                    }
                    // Clear URL input when file is selected
                    if (urlInput) urlInput.value = '';
                    // Uncheck remove checkbox if new file is uploaded
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
                // Hide current image when new URL is entered
                if (currentImage) {
                    currentImage.closest('.mb-4').style.display = 'none';
                }
                // Clear file input when URL is entered
                if (fileInput) fileInput.value = '';
                // Uncheck remove checkbox if new URL is entered
                if (removeCheckbox) {
                    removeCheckbox.checked = false;
                    if (removeOverlay) removeOverlay.classList.add('hidden');
                }
            } else {
                container.classList.add('hidden');
                // Show current image again if URL is cleared
                if (currentImage) {
                    currentImage.closest('.mb-4').style.display = 'block';
                }
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
            // Show current image again
            if (currentImage) {
                currentImage.closest('.mb-4').style.display = 'block';
                handleRemoveImage(removeCheckbox);
            }
        }

        function handleRemoveImage(checkbox) {
            const overlay = document.getElementById('remove-overlay');
            const currentImageContainer = document.getElementById('current-image-container');
            const fileInput = document.getElementById('featured_image');
            const urlInput = document.getElementById('image_url');
            const previewContainer = document.getElementById('image-preview-container');
            
            if (checkbox.checked) {
                if (overlay) overlay.classList.remove('hidden');
                // Clear file and URL inputs when removing
                if (fileInput) fileInput.value = '';
                if (urlInput) urlInput.value = '';
                if (previewContainer) previewContainer.classList.add('hidden');
            } else {
                if (overlay) overlay.classList.add('hidden');
            }
        }

        // Preview URL on page load if it exists
        document.addEventListener('DOMContentLoaded', function() {
            const urlInput = document.getElementById('image_url');
            if (urlInput && urlInput.value && urlInput.value.trim() !== '') {
                // Don't auto-preview on edit, just show current image
            }
        });
    </script>
@endpush

