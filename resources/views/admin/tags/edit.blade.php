@extends('layouts.admin')

@section('title', 'Edit Tag - Admin Panel')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.tags.index') }}" class="text-sm text-gray-600 hover:text-primary dark:text-gray-400">← Back to Tags</a>
    </div>

    <h1 class="mb-6 text-3xl font-extrabold text-secondary">Edit Tag: {{ $tag->name }}</h1>

    <div class="max-w-2xl rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <form action="{{ route('admin.tags.update', $tag) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-medium">Name <span class="text-red-600">*</span></label>
                <input id="name" name="name" type="text" value="{{ old('name', $tag->name) }}" required class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="slug" class="block text-sm font-medium">Slug <small class="text-gray-500">(auto-generated if empty)</small></label>
                <input id="slug" name="slug" type="text" value="{{ old('slug', $tag->slug) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                @error('slug')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3 border-t pt-4 dark:border-gray-700">
                <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-secondary">Update</button>
                <a href="{{ route('admin.tags.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">Cancel</a>
            </div>
        </form>
    </div>
@endsection


