@extends('layouts.admin')

@section('title', 'Media Library - Admin Panel')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-3xl font-extrabold text-secondary">Media Library</h1>
        <button onclick="openUploadModal()" class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add New Media
        </button>
    </div>

    {{-- Filters and Search --}}
    <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
        <form method="GET" action="{{ route('admin.media.index') }}" class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search media..." class="w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
            </div>
            <div>
                <select name="type" class="rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    <option value="all" {{ request('type') === 'all' || !request('type') ? 'selected' : '' }}>All Media</option>
                    <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>Images</option>
                    <option value="file" {{ request('type') === 'file' ? 'selected' : '' }}>Files</option>
                </select>
            </div>
            <div>
                <select name="sort_by" class="rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Date</option>
                    <option value="original_name" {{ request('sort_by') === 'original_name' ? 'selected' : '' }}>Name</option>
                    <option value="size" {{ request('sort_by') === 'size' ? 'selected' : '' }}>Size</option>
                </select>
            </div>
            <div>
                <select name="sort_order" class="rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    <option value="desc" {{ request('sort_order') === 'desc' ? 'selected' : '' }}>Desc</option>
                    <option value="asc" {{ request('sort_order') === 'asc' ? 'selected' : '' }}>Asc</option>
                </select>
            </div>
            <button type="submit" class="rounded-md bg-gray-600 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700">Filter</button>
            @if(request()->hasAny(['search', 'type', 'sort_by', 'sort_order']))
                <a href="{{ route('admin.media.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">Clear</a>
            @endif
        </form>
    </div>

    {{-- Media Grid --}}
    @if($media->count() > 0)
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
            @foreach($media as $item)
                <div class="group relative overflow-hidden rounded-lg border border-gray-200 bg-white transition-shadow hover:shadow-lg dark:border-gray-700 dark:bg-gray-800">
                    @if($item['is_image'])
                        <img src="{{ str_starts_with($item['url'], 'http') ? $item['url'] : asset('public' . $item['url']) }}" alt="{{ $item['alt_text'] ?? $item['original_name'] }}" class="h-40 w-full object-cover">
                    @else
                        <div class="flex h-40 w-full items-center justify-center bg-gray-100 dark:bg-gray-700">
                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-opacity-0 transition-opacity group-hover:bg-opacity-50">
                        <div class="flex h-full items-center justify-center gap-2 opacity-0 transition-opacity group-hover:opacity-100">
                            <button onclick="event.stopPropagation(); copyImageUrl('{{ $item['url'] }}')" class="rounded bg-primary px-3 py-1 text-sm text-white hover:bg-secondary">Copy</button>
                            <button onclick="event.stopPropagation(); deleteMediaFromGrid('{{ $item['filename'] }}', '{{ $item['directory'] ?? 'media' }}')" class="rounded bg-red-600 px-3 py-1 text-sm text-white hover:bg-red-700">Delete</button>
                        </div>
                    </div>
                    <div class="p-2">
                        <p class="truncate text-xs font-medium text-gray-900 dark:text-gray-100">{{ $item['original_name'] }}</p>
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item['formatted_size'] }}</p>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $item['directory'] === 'media' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' : ($item['directory'] === 'blog' ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : ($item['directory'] === 'products' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300' : 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300')) }}">
                                {{ ucfirst($item['directory'] ?? 'media') }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $media->links() }}
        </div>
    @else
        <div class="rounded-lg border border-gray-200 bg-white p-12 text-center dark:border-gray-700 dark:bg-gray-800">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto h-16 w-16 text-gray-400">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-gray-100">No media found</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Upload your first file to get started</p>
            <button onclick="openUploadModal()" class="mt-4 inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-secondary">
                Upload Media
            </button>
        </div>
    @endif

    {{-- Upload Modal --}}
    <div id="uploadModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="w-full max-w-2xl rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Upload Media</h2>
                <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">File</label>
                        <input type="file" name="file" id="fileInput" required class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                        <p class="mt-1 text-xs text-gray-500">Maximum file size: 10MB</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alt Text</label>
                        <input type="text" name="alt_text" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Caption</label>
                        <textarea name="caption" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700"></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-secondary">Upload</button>
                        <button type="button" onclick="closeUploadModal()" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="w-full max-w-2xl rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Edit Media</h2>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="editModalContent" class="space-y-4">
                {{-- Content loaded via AJAX --}}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Toast notification function (if not already available)
        if (typeof showToast === 'undefined') {
            function showToast(message, type = 'success', duration = 3000) {
                const container = document.getElementById('toast-container');
                if (!container) {
                    const newContainer = document.createElement('div');
                    newContainer.id = 'toast-container';
                    newContainer.className = 'fixed bottom-4 right-4 z-50 flex flex-col gap-2 max-w-full sm:max-w-md px-4 sm:px-0';
                    newContainer.setAttribute('aria-live', 'polite');
                    newContainer.setAttribute('aria-atomic', 'true');
                    document.body.appendChild(newContainer);
                    return showToast(message, type, duration);
                }

                const toastId = `toast-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
                const toast = document.createElement('div');
                toast.id = toastId;
                toast.setAttribute('role', 'alert');
                toast.className = `
                    flex items-center gap-3 rounded-lg border px-4 py-3 shadow-lg
                    transition-all duration-300 ease-in-out
                    transform translate-x-full opacity-0
                    ${type === 'success' 
                        ? 'border-green-200 bg-green-50 text-green-800 dark:border-green-800 dark:bg-green-900/40 dark:text-green-300' 
                        : type === 'error'
                        ? 'border-red-200 bg-red-50 text-red-800 dark:border-red-800 dark:bg-red-900/40 dark:text-red-300'
                        : type === 'info'
                        ? 'border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-800 dark:bg-blue-900/40 dark:text-blue-300'
                        : 'border-gray-200 bg-gray-50 text-gray-800 dark:border-gray-800 dark:bg-gray-900/40 dark:text-gray-300'
                    }
                    w-full sm:min-w-[300px] sm:max-w-md
                `.replace(/\s+/g, ' ').trim();

                const icon = type === 'success' 
                    ? '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
                    : type === 'error'
                    ? '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
                    : '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';

                toast.innerHTML = `
                    ${icon}
                    <span class="flex-1 text-sm font-medium break-words">${message}</span>
                    <button type="button" onclick="removeToast('${toastId}')" class="flex-shrink-0 text-current opacity-60 hover:opacity-100 transition-opacity ml-2" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                `;

                container.appendChild(toast);

                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        toast.classList.remove('translate-x-full', 'opacity-0');
                        toast.classList.add('translate-x-0', 'opacity-100');
                    });
                });

                if (duration > 0) {
                    setTimeout(() => {
                        removeToast(toastId);
                    }, duration);
                }

                return toastId;
            }

            function removeToast(toastId) {
                const toast = document.getElementById(toastId);
                if (!toast) return;
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }
        }

        let selectedMediaCallback = null;

        function openUploadModal() {
            document.getElementById('uploadModal').classList.remove('hidden');
            document.getElementById('uploadModal').classList.add('flex');
        }

        function closeUploadModal() {
            document.getElementById('uploadModal').classList.add('hidden');
            document.getElementById('uploadModal').classList.remove('flex');
            document.getElementById('uploadForm').reset();
        }

        function openEditModal(filename, directory = 'media') {
            fetch(`/admin/media/${encodeURIComponent(filename)}?directory=${encodeURIComponent(directory)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const media = data.media;
                        const isImage = media.is_image;
                        
                        const imageUrl = media.url.startsWith('http') ? media.url : '{{ asset("public") }}' + media.url;
                        document.getElementById('editModalContent').innerHTML = `
                            <div class="mb-4">
                                ${isImage ? `<img src="${imageUrl}" alt="${media.alt_text || ''}" class="h-64 w-full rounded-lg object-cover">` : ''}
                            </div>
                            <form id="editForm">
                                @csrf
                                @method('PUT')
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Original Name</label>
                                        <input type="text" value="${media.original_name}" disabled class="mt-1 block w-full rounded-md border border-gray-300 bg-gray-100 px-3 py-2 dark:border-gray-600 dark:bg-gray-700">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alt Text</label>
                                        <input type="text" name="alt_text" id="editAltText" value="${(media.alt_text || '').replace(/"/g, '&quot;')}" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Caption</label>
                                        <textarea name="caption" id="editCaption" rows="2" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">${(media.caption || '').replace(/</g, '&lt;').replace(/>/g, '&gt;')}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                                        <textarea name="description" id="editDescription" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 bg-light px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-primary dark:border-gray-600 dark:bg-gray-700">${(media.description || '').replace(/</g, '&lt;').replace(/>/g, '&gt;')}</textarea>
                                    </div>
                                    <div class="flex gap-3">
                                        <button type="submit" class="flex-1 rounded-md bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-secondary">Update</button>
                                        <button type="button" onclick="deleteMediaFromGrid('${media.filename}', '${media.directory || 'media'}')" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Delete</button>
                                        <button type="button" onclick="closeEditModal()" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        `;
                        
                        document.getElementById('editForm').addEventListener('submit', function(e) {
                            e.preventDefault();
                            updateMedia(media.filename, media.directory || 'media');
                        });
                        
                        document.getElementById('editModal').classList.remove('hidden');
                        document.getElementById('editModal').classList.add('flex');
                    }
                });
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }

        function updateMedia(filename, directory = 'media') {
            const formData = {
                directory: directory,
                alt_text: document.getElementById('editAltText').value,
                caption: document.getElementById('editCaption').value,
                description: document.getElementById('editDescription').value,
            };

            fetch(`/admin/media/${encodeURIComponent(filename)}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (typeof showToast === 'function') {
                            showToast('Media updated successfully!', 'success');
                        } else {
                            alert('Media updated successfully!');
                        }
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Error updating media', 'error');
                        } else {
                            alert(data.message || 'Error updating media');
                        }
                    }
                });
        }

        const adminMediaBase = '{{ url("admin/media") }}';

        function deleteMediaFromGrid(filename, directory) {
            if (!confirm('Are you sure you want to delete this media? This action cannot be undone.')) {
                return;
            }

            // Send directory in FormData body
            const formData = new FormData();
            formData.append('directory', directory);

            fetch(`${adminMediaBase}/${encodeURIComponent(filename)}/delete`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
                .then(response => {
                    // Check if response is ok and is JSON
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`HTTP ${response.status}: ${text.substring(0, 100)}`);
                        });
                    }
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.includes("application/json")) {
                        return response.json();
                    } else {
                        return response.text().then(text => {
                            throw new Error('Expected JSON but got: ' + text.substring(0, 100));
                        });
                    }
                })
                .then(data => {
                    if (data.success) {
                        if (typeof showToast === 'function') {
                            showToast('Media deleted successfully!', 'success');
                        } else {
                            alert('Media deleted successfully!');
                        }
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Error deleting media', 'error');
                        } else {
                            alert(data.message || 'Error deleting media');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof showToast === 'function') {
                        showToast('Error deleting media: ' + error.message, 'error');
                    } else {
                        alert('Error deleting media: ' + error.message);
                    }
                });
        }

        function deleteMedia(filename, directory = 'media') {
            // Used by edit modal
            deleteMediaFromGrid(filename, directory);
        }

        function copyImageUrl(url) {
            // Convert relative URL to full URL if needed
            let fullUrl = url;
            if (!url.startsWith('http')) {
                // If it's a relative path like /media/filename.png, make it full URL
                if (url.startsWith('/')) {
                    fullUrl = window.location.origin + url;
                } else {
                    fullUrl = window.location.origin + '/' + url;
                }
            }
            
            // Create a temporary textarea element to copy the URL
            const textarea = document.createElement('textarea');
            textarea.value = fullUrl;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            textarea.setSelectionRange(0, 99999); // For mobile devices
            
            try {
                // Copy the text to clipboard
                document.execCommand('copy');
                
                // Show toast notification
                if (typeof showToast === 'function') {
                    showToast('Your link has been successfully copied', 'success');
                } else {
                    // Fallback if showToast is not available
                    alert('Image URL copied to clipboard: ' + fullUrl);
                }
            } catch (err) {
                // Fallback for browsers that don't support execCommand
                console.error('Failed to copy:', err);
                // Try modern clipboard API
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(fullUrl).then(() => {
                        if (typeof showToast === 'function') {
                            showToast('Your link has been successfully copied', 'success');
                        } else {
                            alert('Image URL copied to clipboard: ' + fullUrl);
                        }
                    }).catch(err => {
                        console.error('Failed to copy:', err);
                        alert('Failed to copy URL. Please copy manually: ' + fullUrl);
                    });
                } else {
                    alert('Please copy manually: ' + fullUrl);
                }
            } finally {
                // Remove the temporary textarea
                document.body.removeChild(textarea);
            }
        }

        // Upload form handler
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('{{ route("admin.media.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (typeof showToast === 'function') {
                            showToast('File uploaded successfully!', 'success');
                        } else {
                            alert('File uploaded successfully!');
                        }
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Error uploading file', 'error');
                        } else {
                            alert(data.message || 'Error uploading file');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (typeof showToast === 'function') {
                        showToast('Error uploading file', 'error');
                    } else {
                        alert('Error uploading file');
                    }
                });
        });

        // Close modals on outside click
        document.getElementById('uploadModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeUploadModal();
            }
        });

        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    </script>
@endpush

