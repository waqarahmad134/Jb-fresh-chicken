<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    private $mediaPath = 'public/media';

    /**
     * Get file metadata from filesystem
     */
    private function getFileMetadata($filename): array
    {
        $path = $this->mediaPath . '/' . $filename;
        $fullPath = storage_path('app/' . $path);
        
        if (!file_exists($fullPath)) {
            return null;
        }

        $mimeType = mime_content_type($fullPath);
        $size = filesize($fullPath);
        $url = Storage::url($path);
        
        // Check for metadata JSON file
        $metadataFile = $this->mediaPath . '/' . $filename . '.json';
        $metadata = [];
        if (Storage::exists($metadataFile)) {
            $metadata = json_decode(Storage::get($metadataFile), true) ?? [];
        }

        return [
            'filename' => $filename,
            'original_name' => $metadata['original_name'] ?? $filename,
            'url' => $url,
            'mime_type' => $mimeType,
            'size' => $size,
            'formatted_size' => $this->formatBytes($size),
            'alt_text' => $metadata['alt_text'] ?? '',
            'caption' => $metadata['caption'] ?? '',
            'description' => $metadata['description'] ?? '',
            'created_at' => filemtime($fullPath),
            'is_image' => str_starts_with($mimeType, 'image/'),
        ];
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Display a listing of media (grid view like WordPress)
     */
    public function index(Request $request)
    {
        $files = [];
        
        if (Storage::exists($this->mediaPath)) {
            $fileList = Storage::files($this->mediaPath);
            
            foreach ($fileList as $file) {
                // Skip JSON metadata files
                if (Str::endsWith($file, '.json')) {
                    continue;
                }
                
                $filename = basename($file);
                $metadata = $this->getFileMetadata($filename);
                
                if ($metadata) {
                    // Apply filters
                    if ($request->has('type') && $request->type !== 'all') {
                        if ($request->type === 'image' && !$metadata['is_image']) {
                            continue;
                        }
                        if ($request->type === 'file' && $metadata['is_image']) {
                            continue;
                        }
                    }
                    
                    // Apply search
                    if ($request->has('search') && $request->search) {
                        $search = strtolower($request->search);
                        $searchable = strtolower($metadata['original_name'] . ' ' . $metadata['alt_text'] . ' ' . $metadata['caption']);
                        if (!str_contains($searchable, $search)) {
                            continue;
                        }
                    }
                    
                    $files[] = $metadata;
                }
            }
            
            // Sort
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            usort($files, function($a, $b) use ($sortBy, $sortOrder) {
                $aVal = $a[$sortBy] ?? 0;
                $bVal = $b[$sortBy] ?? 0;
                
                if ($sortOrder === 'asc') {
                    return $aVal <=> $bVal;
                }
                return $bVal <=> $aVal;
            });
        }

        // Paginate manually
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedFiles = array_slice($files, $offset, $perPage);
        
        // Create paginator manually
        $media = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedFiles,
            count($files),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.media.index', compact('media'));
    }

    /**
     * Store uploaded file
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        
        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '-' . uniqid() . '.' . $extension;

        // Store file
        $path = $file->storeAs($this->mediaPath, $filename);
        
        // Store metadata in JSON file
        $metadata = [
            'original_name' => $originalName,
            'alt_text' => $request->alt_text ?? '',
            'caption' => $request->caption ?? '',
            'description' => $request->description ?? '',
            'uploaded_by' => auth()->id(),
            'uploaded_at' => now()->toIso8601String(),
        ];
        
        Storage::put($this->mediaPath . '/' . $filename . '.json', json_encode($metadata, JSON_PRETTY_PRINT));

        $fileData = $this->getFileMetadata($filename);

        return response()->json([
            'success' => true,
            'media' => $fileData,
            'message' => 'File uploaded successfully!'
        ]);
    }

    /**
     * Update media metadata (alt text, caption, description)
     */
    public function update(Request $request, string $filename): JsonResponse
    {
        $validated = $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $metadataFile = $this->mediaPath . '/' . $filename . '.json';
        
        // Get existing metadata or create new
        $metadata = [];
        if (Storage::exists($metadataFile)) {
            $metadata = json_decode(Storage::get($metadataFile), true) ?? [];
        }
        
        // Update metadata
        $metadata = array_merge($metadata, $validated);
        
        // Ensure we have original_name
        if (!isset($metadata['original_name'])) {
            $metadata['original_name'] = $filename;
        }
        
        Storage::put($metadataFile, json_encode($metadata, JSON_PRETTY_PRINT));

        $fileData = $this->getFileMetadata($filename);

        return response()->json([
            'success' => true,
            'media' => $fileData,
            'message' => 'Media updated successfully!'
        ]);
    }

    /**
     * Delete media
     */
    public function destroy(string $filename): JsonResponse
    {
        // Delete file
        $filePath = $this->mediaPath . '/' . $filename;
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        // Delete metadata JSON
        $metadataFile = $this->mediaPath . '/' . $filename . '.json';
        if (Storage::exists($metadataFile)) {
            Storage::delete($metadataFile);
        }

        return response()->json([
            'success' => true,
            'message' => 'Media deleted successfully!'
        ]);
    }

    /**
     * Get media by filename (for AJAX requests)
     */
    public function show(string $filename): JsonResponse
    {
        $fileData = $this->getFileMetadata($filename);
        
        if (!$fileData) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'media' => $fileData
        ]);
    }

    /**
     * Get all media (for editor integration)
     */
    public function getAll(): JsonResponse
    {
        $files = [];
        
        if (Storage::exists($this->mediaPath)) {
            $fileList = Storage::files($this->mediaPath);
            
            foreach ($fileList as $file) {
                if (Str::endsWith($file, '.json')) {
                    continue;
                }
                
                $filename = basename($file);
                $metadata = $this->getFileMetadata($filename);
                
                if ($metadata) {
                    $files[] = $metadata;
                }
            }
            
            // Sort by created_at desc
            usort($files, function($a, $b) {
                return ($b['created_at'] ?? 0) <=> ($a['created_at'] ?? 0);
            });
        }
        
        return response()->json([
            'success' => true,
            'media' => $files
        ]);
    }
}
