@extends('layouts.admin')

@section('title', 'Clear Database - Admin Panel')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.database.index') }}" class="text-sm text-gray-600 hover:text-primary dark:text-gray-400">← Back to Database</a>
    </div>

    <div class="mx-auto max-w-2xl">
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-6 dark:border-red-800 dark:bg-red-900/20">
            <div class="flex items-start gap-4">
                <svg class="h-6 w-6 flex-shrink-0 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <h2 class="text-lg font-semibold text-red-800 dark:text-red-200">Warning: Clear Database</h2>
                    <p class="mt-2 text-sm text-red-700 dark:text-red-300">
                        This action will <strong>permanently delete all data</strong> from all tables in the database. 
                        This action cannot be undone!
                    </p>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-100">Database Information</h3>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Tables:</span>
                    <span class="text-sm text-gray-900 dark:text-gray-100">{{ count($tables) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Rows:</span>
                    <span class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($totalRows) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Database:</span>
                    <span class="text-sm text-gray-900 dark:text-gray-100">
                        <code class="rounded bg-gray-100 px-2 py-1 text-xs dark:bg-gray-700">{{ config('database.connections.mysql.database') }}</code>
                    </span>
                </div>
            </div>

            <div class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-700">
                <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">Tables that will be cleared:</h4>
                <div class="max-h-64 space-y-2 overflow-y-auto">
                    @foreach($tables as $table)
                        <div class="flex items-center gap-2 text-sm">
                            <code class="rounded bg-gray-100 px-2 py-1 text-xs dark:bg-gray-700">{{ $table }}</code>
                        </div>
                    @endforeach
                </div>
            </div>

            <form method="POST" action="{{ route('admin.database.clear.store') }}" class="mt-8">
                @csrf
                
                <div class="mb-4">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="confirm" value="1" required class="h-5 w-5 rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            I understand this will delete all data and I want to proceed
                        </span>
                    </label>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-red-700">
                        Clear All Database Tables
                    </button>
                    <a href="{{ route('admin.database.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

