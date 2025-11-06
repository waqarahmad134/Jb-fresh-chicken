<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class BackupController extends Controller
{
    /**
     * Display backup management page
     */
    public function index()
    {
        $backups = $this->getBackups();
        
        return view('admin.backup.index', compact('backups'));
    }

    /**
     * Create files backup (storage, media, etc.)
     */
    public function createFilesBackup()
    {
        try {
            // Check if ZipArchive is available
            if (!extension_loaded('zip') || !class_exists('\ZipArchive')) {
                return response()->json([
                    'success' => false,
                    'message' => 'ZipArchive extension is not enabled. Please enable php-zip extension in your PHP configuration. On Windows, uncomment "extension=zip" in php.ini and restart your web server.'
                ], 500);
            }

            $backupName = 'files_backup_' . date('Y-m-d_His') . '.zip';
            $backupPath = storage_path('app/backups/' . $backupName);
            
            // Ensure backups directory exists
            $backupDir = storage_path('app/backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            $zip = new \ZipArchive();
            $result = $zip->open($backupPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            
            if ($result === TRUE) {
                // Backup storage/app/public (includes media)
                $publicPath = storage_path('app/public');
                if (File::exists($publicPath)) {
                    $this->addDirectoryToZip($zip, $publicPath, 'storage/public');
                }

                // Backup .env file (optional - you might want to exclude this)
                $envPath = base_path('.env');
                if (File::exists($envPath)) {
                    $zip->addFile($envPath, '.env');
                }

                $zip->close();

                return response()->json([
                    'success' => true,
                    'message' => 'Files backup created successfully!',
                    'backup' => $backupName
                ]);
            } else {
                $errorMessages = [
                    \ZipArchive::ER_OK => 'No error',
                    \ZipArchive::ER_MULTIDISK => 'Multi-disk zip archives not supported',
                    \ZipArchive::ER_RENAME => 'Renaming temporary file failed',
                    \ZipArchive::ER_CLOSE => 'Closing zip archive failed',
                    \ZipArchive::ER_SEEK => 'Seek error',
                    \ZipArchive::ER_READ => 'Read error',
                    \ZipArchive::ER_WRITE => 'Write error',
                    \ZipArchive::ER_CRC => 'CRC error',
                    \ZipArchive::ER_ZIPCLOSED => 'Containing zip archive was closed',
                    \ZipArchive::ER_NOENT => 'No such file',
                    \ZipArchive::ER_EXISTS => 'File already exists',
                    \ZipArchive::ER_OPEN => 'Can\'t open file',
                    \ZipArchive::ER_TMPOPEN => 'Failure to create temporary file',
                    \ZipArchive::ER_ZLIB => 'Zlib error',
                    \ZipArchive::ER_MEMORY => 'Memory allocation failure',
                    \ZipArchive::ER_CHANGED => 'Entry has been changed',
                    \ZipArchive::ER_COMPNOTSUPP => 'Compression method not supported',
                    \ZipArchive::ER_EOF => 'Premature EOF',
                    \ZipArchive::ER_INVAL => 'Invalid argument',
                    \ZipArchive::ER_NOZIP => 'Not a zip archive',
                    \ZipArchive::ER_INTERNAL => 'Internal error',
                    \ZipArchive::ER_INCONS => 'Zip archive inconsistent',
                    \ZipArchive::ER_REMOVE => 'Can\'t remove file',
                    \ZipArchive::ER_DELETED => 'Entry has been deleted',
                ];

                $errorMsg = $errorMessages[$result] ?? 'Unknown error (code: ' . $result . ')';

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create backup: ' . $errorMsg
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download complete project with database
     */
    public function downloadCompleteProject()
    {
        try {
            // Check if ZipArchive is available
            if (!extension_loaded('zip') || !class_exists('\ZipArchive')) {
                return redirect()->route('admin.backup.index')
                    ->with('error', 'ZipArchive extension is not enabled. Please enable php-zip extension in your PHP configuration. On Windows, uncomment "extension=zip" in php.ini and restart your web server.');
            }

            $projectName = config('app.name', 'project');
            $backupName = $projectName . '_complete_' . date('Y-m-d_His') . '.zip';
            $backupPath = storage_path('app/backups/' . $backupName);
            
            // Ensure backups directory exists
            $backupDir = storage_path('app/backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            $zip = new \ZipArchive();
            $result = $zip->open($backupPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            
            if ($result === TRUE) {
                // 1. Add entire project code (excluding node_modules, vendor, etc.)
                $excludePaths = [
                    'node_modules',
                    'vendor',
                    '.git',
                    'storage/app/backups',
                    'storage/logs',
                    'storage/framework/cache',
                    'storage/framework/sessions',
                    'storage/framework/views',
                    '.env',
                    'composer.lock',
                    'package-lock.json',
                    'yarn.lock',
                ];

                $projectPath = base_path();
                $this->addProjectToZip($zip, $projectPath, $excludePaths);

                // 2. Add database backup
                $databaseSql = $this->exportDatabase();
                $zip->addFromString('database_backup.sql', $databaseSql);

                // 3. Add backup info file
                $info = [
                    'project_name' => $projectName,
                    'backup_date' => date('Y-m-d H:i:s'),
                    'database' => DB::getDatabaseName(),
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'includes' => [
                        'project_code' => true,
                        'storage_files' => true,
                        'media_files' => true,
                        'database' => true,
                    ]
                ];
                $zip->addFromString('backup_info.json', json_encode($info, JSON_PRETTY_PRINT));

                $zip->close();

                // Download the file
                return response()->download($backupPath, $backupName)->deleteFileAfterSend(true);
            } else {
                return redirect()->route('admin.backup.index')
                    ->with('error', 'Failed to create backup file. Please check server permissions.');
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.backup.index')
                ->with('error', 'Error creating backup: ' . $e->getMessage());
        }
    }

    /**
     * Download files backup
     */
    public function downloadFilesBackup($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);
        
        if (!File::exists($filePath)) {
            abort(404, 'Backup file not found');
        }

        return response()->download($filePath, $filename)->deleteFileAfterSend(false);
    }

    /**
     * Delete backup
     */
    public function deleteBackup($filename)
    {
        $filePath = storage_path('app/backups/' . $filename);
        
        if (File::exists($filePath)) {
            File::delete($filePath);
            
            return response()->json([
                'success' => true,
                'message' => 'Backup deleted successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Backup file not found'
        ], 404);
    }

    /**
     * Get list of backups
     */
    private function getBackups(): array
    {
        $backupDir = storage_path('app/backups');
        $backups = [];

        if (File::exists($backupDir)) {
            $files = File::files($backupDir);
            
            foreach ($files as $file) {
                if (strtolower($file->getExtension()) === 'zip') {
                    $backups[] = [
                        'name' => $file->getFilename(),
                        'size' => $this->formatBytes($file->getSize()),
                        'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
                        'type' => str_contains($file->getFilename(), 'complete') ? 'complete' : 'files',
                    ];
                }
            }

            // Sort by creation time (newest first)
            usort($backups, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
        }

        return $backups;
    }

    /**
     * Add directory to zip recursively
     */
    private function addDirectoryToZip($zip, $dir, $zipPath = '')
    {
        $files = File::allFiles($dir);
        
        foreach ($files as $file) {
            $relativePath = $zipPath . '/' . $file->getRelativePathname();
            $zip->addFile($file->getPathname(), $relativePath);
        }
    }

    /**
     * Add project files to zip (excluding certain paths)
     */
    private function addProjectToZip($zip, $basePath, $excludePaths = [])
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($basePath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relativePath = str_replace($basePath . DIRECTORY_SEPARATOR, '', $item->getPathname());
            
            // Skip excluded paths
            $shouldExclude = false;
            foreach ($excludePaths as $exclude) {
                if (strpos($relativePath, $exclude) === 0) {
                    $shouldExclude = true;
                    break;
                }
            }

            if ($shouldExclude) {
                continue;
            }

            if ($item->isFile()) {
                $zip->addFile($item->getPathname(), $relativePath);
            }
        }
    }

    /**
     * Export database (similar to DatabaseController)
     */
    private function exportDatabase(): string
    {
        $database = DB::getDatabaseName();
        $tables = $this->getTables();
        
        $sql = "-- Database Export: {$database}\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
        $sql .= "SET time_zone = \"+00:00\";\n\n";
        $sql .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
        $sql .= "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
        $sql .= "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
        $sql .= "/*!40101 SET NAMES utf8mb4 */;\n\n";

        foreach ($tables as $table) {
            $sql .= "\n-- --------------------------------------------------------\n";
            $sql .= "-- Table structure for table `{$table}`\n";
            $sql .= "-- --------------------------------------------------------\n\n";
            
            $createTable = DB::select("SHOW CREATE TABLE `{$table}`");
            if (!empty($createTable)) {
                $createTableArray = (array) $createTable[0];
                $createStatement = $createTableArray['Create Table'];
                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $sql .= $createStatement . ";\n\n";
            }

            $rows = DB::table($table)->get();
            if ($rows->count() > 0) {
                $sql .= "-- Dumping data for table `{$table}`\n\n";
                
                $columns = Schema::getColumnListing($table);
                $chunkSize = 100;
                
                foreach ($rows->chunk($chunkSize) as $chunk) {
                    $sql .= "INSERT INTO `{$table}` (`" . implode('`, `', $columns) . "`) VALUES\n";
                    
                    $values = [];
                    foreach ($chunk as $row) {
                        $rowArray = (array) $row;
                        $rowValues = [];
                        foreach ($columns as $column) {
                            $value = $rowArray[$column] ?? null;
                            if ($value === null) {
                                $rowValues[] = 'NULL';
                            } elseif (is_numeric($value) && !is_string($value)) {
                                $rowValues[] = $value;
                            } elseif (is_bool($value)) {
                                $rowValues[] = $value ? '1' : '0';
                            } else {
                                $rowValues[] = "'" . str_replace(["'", "\\", "\n", "\r"], ["''", "\\\\", "\\n", "\\r"], $value) . "'";
                            }
                        }
                        $values[] = "(" . implode(", ", $rowValues) . ")";
                    }
                    
                    $sql .= implode(",\n", $values) . ";\n\n";
                }
            }
        }

        $sql .= "\n/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n";
        $sql .= "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n";
        $sql .= "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n";

        return $sql;
    }

    /**
     * Get all table names
     */
    private function getTables(): array
    {
        $database = DB::getDatabaseName();
        $tables = DB::select("SHOW TABLES");
        $tableName = 'Tables_in_' . $database;
        
        return array_map(function($table) use ($tableName) {
            return $table->$tableName;
        }, $tables);
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
}
