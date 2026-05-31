<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--filename=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a backup of the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Starting database backup...');

            // Create backup directory if it doesn't exist
            $backupPath = storage_path('app/backups');
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            // Generate filename
            $filename = $this->option('filename') ?: 'backup-' . date('Y-m-d-His') . '.sql';
            $filepath = $backupPath . '/' . $filename;

            // Get database configuration
            $connection = config('database.default');
            $database = config("database.connections.{$connection}.database");
            $username = config("database.connections.{$connection}.username");
            $password = config("database.connections.{$connection}.password");
            $host = config("database.connections.{$connection}.host");
            $port = config("database.connections.{$connection}.port", 3306);

            // Try mysqldump first
            if ($this->tryMysqldump($filepath, $host, $port, $database, $username, $password)) {
                $size = $this->formatBytes(File::size($filepath));
                $this->info("✓ Database backup created successfully using mysqldump!");
                $this->info("Location: {$filepath}");
                $this->info("Size: {$size}");
                $this->cleanupOldBackups($backupPath);
                return Command::SUCCESS;
            }

            // Fallback to Laravel's database export
            $this->info('mysqldump not available, using Laravel database export...');
            if ($this->exportDatabaseUsingLaravel($filepath)) {
                $size = $this->formatBytes(File::size($filepath));
                $this->info("✓ Database backup created successfully!");
                $this->info("Location: {$filepath}");
                $this->info("Size: {$size}");
                $this->cleanupOldBackups($backupPath);
                return Command::SUCCESS;
            }

            $this->error('All backup methods failed.');
            return Command::FAILURE;

        } catch (\Exception $e) {
            $this->error('Backup failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Try to backup using mysqldump
     */
    protected function tryMysqldump($filepath, $host, $port, $database, $username, $password)
    {
        try {
            // Build mysqldump command
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s 2>&1',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($database),
                escapeshellarg($filepath)
            );

            $returnVar = null;
            $output = null;
            exec($command, $output, $returnVar);

            return $returnVar === 0 && File::exists($filepath) && File::size($filepath) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Export database using Laravel's DB facade
     */
    protected function exportDatabaseUsingLaravel($filepath)
    {
        try {
            $sql = '';

            // Get all tables
            $tables = \DB::select('SHOW TABLES');
            $dbName = config('database.connections.' . config('database.default') . '.database');
            $tableKey = 'Tables_in_' . $dbName;

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;

                // Add drop table statement
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n\n";

                // Get create table statement
                $createTable = \DB::select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

                // Get table data
                $rows = \DB::table($tableName)->get();

                if ($rows->count() > 0) {
                    $sql .= "INSERT INTO `{$tableName}` VALUES\n";

                    $values = [];
                    foreach ($rows as $row) {
                        $rowData = (array) $row;
                        $escapedValues = array_map(function ($value) {
                            if ($value === null) {
                                return 'NULL';
                            }
                            return "'" . addslashes($value) . "'";
                        }, $rowData);
                        $values[] = '(' . implode(', ', $escapedValues) . ')';
                    }

                    $sql .= implode(",\n", $values) . ";\n\n";
                }
            }

            // Write to file
            File::put($filepath, $sql);

            return File::exists($filepath) && File::size($filepath) > 0;

        } catch (\Exception $e) {
            $this->error('Laravel export failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clean up old backup files, keeping only the latest 10
     */
    protected function cleanupOldBackups($backupPath)
    {
        try {
            $files = File::files($backupPath);

            // Sort files by modification time (newest first)
            usort($files, function ($a, $b) {
                return File::lastModified($b) - File::lastModified($a);
            });

            // Keep only the 10 most recent backups
            $filesToDelete = array_slice($files, 10);

            foreach ($filesToDelete as $file) {
                File::delete($file);
                $this->info("Cleaned up old backup: " . basename($file));
            }
        } catch (\Exception $e) {
            $this->warn('Could not clean up old backups: ' . $e->getMessage());
        }
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
