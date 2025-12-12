@extends('layouts.admin')

@section('title', 'Create Product - Admin Panel')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 hover:text-primary dark:text-gray-400">
            ← Back to Products
        </a>
    </div>

    <h1 class="mb-6 text-3xl font-extrabold text-secondary">Create New Product</h1>

    <div class="max-w-3xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium">Product Name <span class="text-red-600">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium">Slug <small class="text-gray-500">(auto-generated if empty)</small></label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug') }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    @error('slug')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="sku" class="block text-sm font-medium">SKU</label>
                    <input type="text" id="sku" name="sku" value="{{ old('sku') }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    @error('sku')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium">Category <span class="text-red-600">*</span></label>
                    <select id="category_id" name="category_id" required class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                        <option value="">Select a category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium">Price (PKR) <span class="text-red-600">*</span></label>
                    <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" required class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    @error('price')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="sale_price" class="block text-sm font-medium">Sale Price (PKR)</label>
                    <input type="number" id="sale_price" name="sale_price" value="{{ old('sale_price') }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    @error('sale_price')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="stock_quantity" class="block text-sm font-medium">Stock Quantity</label>
                    <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    @error('stock_quantity')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-medium">Description</label>
                    <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">{{ old('description') }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-gray-500">Use the rich text editor. Click Code View (< / >) to add HTML directly.</p>
                </div>

                <div class="sm:col-span-2">
                    <label for="images" class="block text-sm font-medium">Product Images <small class="text-gray-500">(optional, multiple allowed)</small></label>
                    <input type="file" id="images" name="images[]" multiple accept="image/*" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700" onchange="previewImages(this)">
                    @error('images')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('images.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-gray-500">You can select multiple images. The first image will be set as primary.</p>
                    <div id="image-preview" class="mt-3 flex flex-wrap gap-3">
                        <!-- Preview images will appear here -->
                    </div>
                </div>
            </div>

            <fieldset class="space-y-3 border-t pt-4 dark:border-gray-700">
                <legend class="text-sm font-medium">Product Options</legend>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                    <span class="text-sm">Active (visible on storefront)</span>
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                    <span class="text-sm">Featured Product</span>
                </label>
            </fieldset>

            <div class="flex gap-3 border-t pt-4 dark:border-gray-700">
                <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-secondary">
                    Create Product
                </button>
                <a href="{{ route('admin.products.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
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
        function previewImages(input) {
            const preview = document.getElementById('image-preview');
            preview.innerHTML = '';
            
            if (input.files && input.files.length > 0) {
                Array.from(input.files).forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.className = 'relative';
                            div.innerHTML = `
                                <img src="${e.target.result}" alt="Preview ${index + 1}" class="h-24 w-24 rounded border object-cover">
                                <span class="absolute -top-2 -right-2 bg-primary text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">${index + 1}</span>
                            `;
                            preview.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        }
    </script>
@endpush

