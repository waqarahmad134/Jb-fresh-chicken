<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaController extends Controller
{
    private $mediaPath = 'media';
    
    /**
     * Get all media directories to scan
     */
    private function getMediaDirectories(): array
    {
        return [
            'media' => public_path('media'),
            'blog' => public_path('images/blog'),
            'products' => public_path('images/products'),
            'categories' => public_path('images/categories'),
        ];
    }

    /**
     * Get file metadata from filesystem
     */
    private function getFileMetadata($filename, $directory = 'media'): ?array
    {
        // Determine the path based on directory type
        $pathMap = [
            'media' => 'media',
            'blog' => 'images/blog',
            'products' => 'images/products',
            'categories' => 'images/categories',
        ];
        
        $relativePath = $pathMap[$directory] ?? 'media';
        $filePath = public_path($relativePath . '/' . $filename);
        
        // Check file existence
        if (!File::exists($filePath)) {
            return null;
        }

        $mimeType = mime_content_type($filePath);
        $size = filesize($filePath);
        // Use same pattern as products: store relative path, use asset('public' . $url) in views
        $url = '/' . $relativePath . '/' . $filename;
        
        // Check for metadata JSON file (only for media directory)
        $metadata = [];
        if ($directory === 'media') {
            $metadataFile = public_path($relativePath . '/' . $filename . '.json');
            if (File::exists($metadataFile)) {
                $metadata = json_decode(File::get($metadataFile), true) ?? [];
            }
        }

        return [
            'filename' => $filename,
            'directory' => $directory, // Track which directory this file is from
            'original_name' => $metadata['original_name'] ?? $filename,
            'url' => $url,
            'mime_type' => $mimeType,
            'size' => $size,
            'formatted_size' => $this->formatBytes($size),
            'alt_text' => $metadata['alt_text'] ?? '',
            'caption' => $metadata['caption'] ?? '',
            'description' => $metadata['description'] ?? '',
            'created_at' => filemtime($filePath),
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
        $directories = $this->getMediaDirectories();
        
        // Scan all media directories
        foreach ($directories as $dirKey => $dirPath) {
            if (!File::exists($dirPath)) {
                continue;
            }
            
            $fileList = File::files($dirPath);
            
            foreach ($fileList as $file) {
                // Skip JSON metadata files
                if (Str::endsWith($file->getFilename(), '.json')) {
                    continue;
                }
                
                $filename = $file->getFilename();
                $metadata = $this->getFileMetadata($filename, $dirKey);
                
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

        // Store file directly in public/media (like products)
        $mediaPath = public_path($this->mediaPath);
        
        // Ensure directory exists
        if (!File::exists($mediaPath)) {
            File::makeDirectory($mediaPath, 0755, true);
        }
        
        // Move file to public/media
        $file->move($mediaPath, $filename);
        
        // Store metadata in JSON file
        $metadata = [
            'original_name' => $originalName,
            'alt_text' => $request->alt_text ?? '',
            'caption' => $request->caption ?? '',
            'description' => $request->description ?? '',
            'uploaded_by' => Auth::id(),
            'uploaded_at' => now()->toIso8601String(),
        ];
        
        File::put($mediaPath . '/' . $filename . '.json', json_encode($metadata, JSON_PRETTY_PRINT));

        $fileData = $this->getFileMetadata($filename, 'media');
        
        if (!$fileData) {
            return response()->json([
                'success' => false,
                'message' => 'File uploaded but metadata could not be retrieved'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'media' => $fileData,
            'message' => 'File uploaded successfully!'
        ]);
    }

    /**
     * Update media metadata (alt text, caption, description)
     * Note: Only works for media directory files (not blog/products/categories)
     */
    public function update(Request $request, string $filename): JsonResponse
    {
        $directory = $request->input('directory', 'media');
        
        // Only allow metadata updates for media directory files
        if ($directory !== 'media') {
            return response()->json([
                'success' => false,
                'message' => 'Metadata can only be updated for files in the media directory'
            ], 403);
        }
        
        $validated = $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $metadataFile = public_path($this->mediaPath . '/' . $filename . '.json');
        
        // Get existing metadata or create new
        $metadata = [];
        if (File::exists($metadataFile)) {
            $metadata = json_decode(File::get($metadataFile), true) ?? [];
        }
        
        // Update metadata
        $metadata = array_merge($metadata, $validated);
        
        // Ensure we have original_name
        if (!isset($metadata['original_name'])) {
            $metadata['original_name'] = $filename;
        }
        
        File::put($metadataFile, json_encode($metadata, JSON_PRETTY_PRINT));

        $fileData = $this->getFileMetadata($filename, $directory);
        
        if (!$fileData) {
            return response()->json([
                'success' => false,
                'message' => 'Media metadata could not be retrieved'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'media' => $fileData,
            'message' => 'Media updated successfully!'
        ]);
    }

    /**
     * Delete media
     */
    public function destroy(Request $request, string $filename): JsonResponse
    {
        // Get directory from request (which directory the file is in)
        $directory = $request->input('directory', 'media');
        
        // Determine the path based on directory type
        $pathMap = [
            'media' => 'media',
            'blog' => 'images/blog',
            'products' => 'images/products',
            'categories' => 'images/categories',
        ];
        
        $relativePath = $pathMap[$directory] ?? 'media';
        
        // Delete file
        $filePath = public_path($relativePath . '/' . $filename);
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        // Delete metadata JSON (only for media directory)
        if ($directory === 'media') {
            $metadataFile = public_path($relativePath . '/' . $filename . '.json');
            if (File::exists($metadataFile)) {
                File::delete($metadataFile);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Media deleted successfully!'
        ]);
    }

    /**
     * Get media by filename (for AJAX requests)
     */
    public function show(Request $request, string $filename): JsonResponse
    {
        $directory = $request->input('directory', 'media');
        $fileData = $this->getFileMetadata($filename, $directory);
        
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
        $directories = $this->getMediaDirectories();
        
        // Scan all media directories
        foreach ($directories as $dirKey => $dirPath) {
            if (!File::exists($dirPath)) {
                continue;
            }
            
            $fileList = File::files($dirPath);
            
            foreach ($fileList as $file) {
                if (Str::endsWith($file->getFilename(), '.json')) {
                    continue;
                }
                
                $filename = $file->getFilename();
                $metadata = $this->getFileMetadata($filename, $dirKey);
                
                if ($metadata) {
                    $files[] = $metadata;
                }
            }
        }
        
        // Sort by created_at desc
        usort($files, function($a, $b) {
            return ($b['created_at'] ?? 0) <=> ($a['created_at'] ?? 0);
        });
        
        return response()->json([
            'success' => true,
            'media' => $files
        ]);
    }
}
