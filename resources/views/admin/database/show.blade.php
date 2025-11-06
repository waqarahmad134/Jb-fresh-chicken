@extends('layouts.admin')

@section('title', 'Table: ' . $table . ' - Database - Admin Panel')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.database.index') }}" class="text-sm text-gray-600 hover:text-primary dark:text-gray-400">← Back to Database</a>
    </div>

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-3xl font-extrabold text-secondary">Table: <code class="text-2xl">{{ $table }}</code></h1>
        <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
            <span><strong>Rows:</strong> {{ number_format($rowCount) }}</span>
            <span><strong>Size:</strong> {{ $size }}</span>
        </div>
    </div>

    {{-- Table Structure --}}
    <div class="mb-6 rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Structure</h2>
        </div>
        <div class="overflow-x-auto p-6">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Column</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Nullable</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Default</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Key</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">Extra</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @foreach($structure as $column)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                <code class="rounded bg-gray-100 px-2 py-1 text-xs dark:bg-gray-700">{{ $column->COLUMN_NAME }}</code>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $column->DATA_TYPE }}</td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                <span class="rounded-full px-2 py-1 text-xs {{ $column->IS_NULLABLE === 'YES' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                    {{ $column->IS_NULLABLE }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ $column->COLUMN_DEFAULT ?? 'NULL' }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                @if($column->COLUMN_KEY)
                                    <span class="rounded-full px-2 py-1 text-xs bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        {{ $column->COLUMN_KEY }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $column->EXTRA }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Table Data --}}
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Data</h2>
        </div>
        <div class="overflow-x-auto p-6">
            @if($rows->count() > 0)
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            @foreach($columns as $column)
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                    {{ $column }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                        @foreach($rows as $row)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                @foreach($columns as $column)
                                    <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                        @if(is_string($row->$column) && strlen($row->$column) > 100)
                                            <span title="{{ $row->$column }}">{{ \Illuminate\Support\Str::limit($row->$column, 100) }}</span>
                                        @else
                                            {{ $row->$column ?? 'NULL' }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $rows->links() }}
                </div>
            @else
                <p class="text-center text-sm text-gray-500 py-8">No data found in this table</p>
            @endif
        </div>
    </div>
@endsection

