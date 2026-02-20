<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Backup;
use Illuminate\Support\Facades\Log;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new database backup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting backup process...');

        try {
            // Check for existing running backups
            if (Backup::where('status', 'Running')->exists()) {
                $this->error('A backup process is already running.');
                return;
            }

            $backupNo = 'BK-' . date('YmdHis');
            $filename = $backupNo . '.sql';
            $path = public_path('uploads/backup/');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $backup = Backup::create([
                'backup_no' => $backupNo,
                'filename' => $filename,
                'backup_type' => 'Database Only',
                'status' => 'Running',
                'location' => 'Local',
                'created_by' => null, 
            ]);

            $dbName = env('DB_DATABASE');
            $dbUser = env('DB_USERNAME');
            $dbPass = env('DB_PASSWORD');
            $dbHost = env('DB_HOST');

            $dumpPath = 'C:\xampp\mysql\bin\mysqldump.exe';
            
            $command = "\"{$dumpPath}\" --user={$dbUser} --password={$dbPass} --host={$dbHost} {$dbName} > \"{$path}{$filename}\" 2>&1";
            
            exec($command, $output, $returnVar);

            if ($returnVar === 0) {
                $size = filesize($path . $filename);
                $backup->update([
                    'status' => 'Success',
                    'file_size' => $this->formatSize($size),
                ]);
                $this->info('Backup generated successfully: ' . $filename);
            } else {
                $errorMessage = implode("\n", $output);
                Log::error("Automated Backup failed. Return var: $returnVar. Output: " . $errorMessage);
                
                $backup->update([
                    'status' => 'Failed',
                    'error_message' => 'mysqldump failed. Code: ' . $returnVar . '. Error: ' . $errorMessage,
                ]);
                $this->error('Backup generation failed. Check logs for details.');
            }

        } catch (\Exception $e) {
            Log::error("Automated Backup Exception: " . $e->getMessage());
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }

    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return $bytes . ' byte';
        } else {
            return '0 bytes';
        }
    }
}
