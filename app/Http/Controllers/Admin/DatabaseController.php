<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DatabaseController extends Controller
{
    /**
     * Display database tables
     */
    public function index(): View
    {
        $tables = $this->getTables();
        $tableInfo = [];
        
        foreach ($tables as $table) {
            $tableInfo[$table] = [
                'name' => $table,
                'rows' => $this->getTableRowCount($table),
                'size' => $this->getTableSize($table),
                'columns' => $this->getTableColumns($table),
            ];
        }
        
        return view('admin.database.index', compact('tableInfo'));
    }

    /**
     * Show table structure and data
     */
    public function show(Request $request, string $table): View
    {
        if (!$this->tableExists($table)) {
            abort(404, 'Table not found');
        }

        $columns = $this->getTableColumns($table);
        $rows = DB::table($table)->paginate(50);
        $rowCount = $this->getTableRowCount($table);
        $size = $this->getTableSize($table);
        $structure = $this->getTableStructure($table);

        return view('admin.database.show', compact('table', 'columns', 'rows', 'rowCount', 'size', 'structure'));
    }

    /**
     * Download/Export database as SQL file
     */
    public function download(): Response
    {
        $database = DB::getDatabaseName();
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        $filename = $database . '_' . date('Y-m-d_His') . '.sql';
        
        // Try to use mysqldump if available
        $mysqldumpPath = $this->findMysqldumpPath();
        
        if ($mysqldumpPath && function_exists('exec')) {
            // Use mysqldump for better performance
            // Create temporary config file for password (more secure)
            $configFile = storage_path('app/temp_mysqldump.cnf');
            file_put_contents($configFile, "[client]\nuser={$username}\npassword={$password}\nhost={$host}\nport={$port}\n");
            chmod($configFile, 0600); // Secure permissions
            
            $command = sprintf(
                '"%s" --defaults-file=%s --single-transaction --routines --triggers %s 2>&1',
                $mysqldumpPath,
                escapeshellarg($configFile),
                escapeshellarg($database)
            );

            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);
            
            // Clean up config file
            @unlink($configFile);

            if ($returnVar === 0 && !empty($output)) {
                $sql = implode("\n", $output);
            } else {
                // Fallback to manual export
                $sql = $this->exportDatabaseManually();
            }
        } else {
            // Fallback to manual export
            $sql = $this->exportDatabaseManually();
        }

        return response($sql, 200)
            ->header('Content-Type', 'application/sql')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Export database manually by reading all tables
     */
    private function exportDatabaseManually(): string
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
            
            // Get CREATE TABLE statement
            $createTable = DB::select("SHOW CREATE TABLE `{$table}`");
            if (!empty($createTable)) {
                $createTableArray = (array) $createTable[0];
                $createStatement = $createTableArray['Create Table'];
                $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
                $sql .= $createStatement . ";\n\n";
            }

            // Get table data
            $rows = DB::table($table)->get();
            if ($rows->count() > 0) {
                $sql .= "-- Dumping data for table `{$table}`\n\n";
                
                $columns = $this->getTableColumns($table);
                $chunkSize = 100; // Insert in chunks to avoid memory issues
                
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
                                // Numeric values don't need quotes
                                $rowValues[] = $value;
                            } elseif (is_bool($value)) {
                                // Boolean values
                                $rowValues[] = $value ? '1' : '0';
                            } else {
                                // String values - escape properly
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
     * Find mysqldump executable path
     */
    private function findMysqldumpPath(): ?string
    {
        // Common paths for mysqldump
        $paths = [
            'mysqldump', // If in PATH
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\wamp\\bin\\mysql\\mysql' . substr(phpversion(), 0, 3) . '\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\mysqldump.exe',
        ];

        foreach ($paths as $path) {
            if (is_executable($path) || @exec("which {$path}") || @exec("where {$path}")) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Clear database (truncate all tables)
     */
    public function clear(Request $request)
    {
        if ($request->method() === 'POST') {
            $request->validate([
                'confirm' => 'required|accepted',
            ]);

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            $tables = $this->getTables();
            foreach ($tables as $table) {
                DB::table($table)->truncate();
            }
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->route('admin.database.index')
                ->with('success', 'Database cleared successfully!');
        }

        $tables = $this->getTables();
        $totalRows = 0;
        foreach ($tables as $table) {
            $totalRows += $this->getTableRowCount($table);
        }

        return view('admin.database.clear', compact('tables', 'totalRows'));
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
     * Check if table exists
     */
    private function tableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }

    /**
     * Get table row count
     */
    private function getTableRowCount(string $table): int
    {
        try {
            return DB::table($table)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get table size
     */
    private function getTableSize(string $table): string
    {
        try {
            $database = DB::getDatabaseName();
            $result = DB::select("
                SELECT 
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
                FROM information_schema.TABLES 
                WHERE table_schema = ? 
                AND table_name = ?
            ", [$database, $table]);
            
            if (!empty($result)) {
                return number_format($result[0]->size_mb, 2) . ' MB';
            }
        } catch (\Exception $e) {
            // Ignore
        }
        
        return 'N/A';
    }

    /**
     * Get table columns
     */
    private function getTableColumns(string $table): array
    {
        try {
            return Schema::getColumnListing($table);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get table structure (column details)
     */
    private function getTableStructure(string $table): array
    {
        try {
            $database = DB::getDatabaseName();
            $columns = DB::select("
                SELECT 
                    COLUMN_NAME,
                    DATA_TYPE,
                    IS_NULLABLE,
                    COLUMN_DEFAULT,
                    COLUMN_KEY,
                    EXTRA
                FROM information_schema.COLUMNS
                WHERE TABLE_SCHEMA = ?
                AND TABLE_NAME = ?
                ORDER BY ORDINAL_POSITION
            ", [$database, $table]);
            
            return $columns;
        } catch (\Exception $e) {
            return [];
        }
    }
}
