@extends('layouts.admin')

@section('title', 'Add Home Banner - Admin Panel')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-secondary">Add Banner</h1>
            <p class="text-sm text-gray-500">Create a slide that appears on the homepage hero carousel.</p>
        </div>
        <a href="{{ route('admin.banners.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Back to Banners</a>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="title" class="block text-sm font-medium">Title <span class="text-red-600">*</span></label>
                    <input id="title" name="title" type="text" value="{{ old('title') }}" required class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="subtitle" class="block text-sm font-medium">Subtitle</label>
                    <input id="subtitle" name="subtitle" type="text" value="{{ old('subtitle') }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    @error('subtitle')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="button_label" class="block text-sm font-medium">Button Label</label>
                    <input id="button_label" name="button_label" type="text" value="{{ old('button_label') }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    @error('button_label')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="button_url" class="block text-sm font-medium">Button URL</label>
                    <input id="button_url" name="button_url" type="url" value="{{ old('button_url') }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    @error('button_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Banner Image</label>
                <div class="space-y-4">
                    <div>
                        <label for="featured_image" class="block text-sm font-medium mb-1">Upload Image</label>
                        <input type="file" id="featured_image" name="featured_image" accept="image/*" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700" onchange="previewFeaturedImage(this)">
                        @error('featured_image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-gray-500">Upload a file (max 5MB).</p>
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
                        <input type="text" id="image_url" name="image_url" value="{{ old('image_url') }}" placeholder="https://example.com/image.jpg" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700" onchange="previewImageUrl(this)">
                        @error('image_url')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-gray-500">Or paste a complete image URL.</p>
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
                    <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', 0) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    @error('sort_order')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-gray-500">Lower numbers display first.</p>
                </div>

                <div class="flex items-end">
                    <label class="flex items-center gap-2 text-sm font-semibold">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                        Active
                    </label>
                </div>
            </div>

            <div class="flex gap-3 border-t pt-4 dark:border-gray-700">
                <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white transition hover:bg-secondary">Save Banner</button>
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

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    container.classList.remove('hidden');
                    if (urlInput) {
                        urlInput.value = '';
                    }
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
                if (fileInput) {
                    fileInput.value = '';
                }
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
    </script>
@endpush
