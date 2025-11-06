@extends('layouts.admin')

@section('title', 'Manage Pages - Admin Panel')

@section('content')

    <h1 class="mb-6 text-3xl font-extrabold text-secondary">Manage Pages</h1>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
        {{-- Page List Sidebar --}}
        <div class="md:col-span-1">
            <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="mb-3 font-bold">Select a Page</h2>
                <nav class="flex flex-col space-y-1">
                    @forelse ($pages as $page)
                        <a 
                            href="{{ route('admin.settings.pages', ['page' => $page->slug]) }}"
                            class="rounded-md p-2 text-left text-sm font-medium transition-colors {{ $selectedPage && $selectedPage->slug === $page->slug ? 'bg-primary text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                        >
                            {{ $page->title }}
                        </a>
                    @empty
                        <p class="text-sm text-gray-500">No pages found.</p>
                    @endforelse
                </nav>
            </div>
        </div>

        {{-- Page Editor --}}
        <div class="md:col-span-3">
            @if ($selectedPage)
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <form action="{{ route('admin.settings.pages.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="slug" value="{{ $selectedPage->slug }}">

                        <h2 class="mb-2 text-2xl font-bold">Editing: {{ $selectedPage->title }}</h2>
                        <p class="mb-4 text-sm text-gray-500">
                            Last Updated: {{ $selectedPage->updated_at->format('F j, Y g:i A') }}
                        </p>

                        <div>
                            <label for="pageContent" class="mb-1 block text-sm font-medium">
                                Page Content (HTML is supported) - Setting Pages Content
                            </label>
                            <textarea
                                id="pageContent"
                                name="content"
                                rows="15"
                                class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 font-mono text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700"
                                required
                            >{{ old('content', $selectedPage->content) }}</textarea>
                            @error('content')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="mt-6">
                            <button type="submit" class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-secondary">
                                Save Content
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="rounded-lg border border-gray-200 bg-white p-6 text-center shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-gray-500">Select a page from the sidebar to edit its content.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

