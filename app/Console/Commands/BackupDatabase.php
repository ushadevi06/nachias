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

            $driveService = new \App\Services\GoogleDriveBackupService();
            $dumpRes = $driveService->dumpDatabase($path . $filename);

            if ($dumpRes['success']) {
                $size = filesize($path . $filename);
                $location = 'Local';

                // Check and upload to Google Drive if configured
                if ($driveService->isConfigured()) {
                    $this->info('Uploading backup to Google Drive...');
                    $driveRes = $driveService->uploadBackup($path . $filename, $filename, true, true);
                    if ($driveRes['success']) {
                        $location = 'Local & Google Drive (' . ($driveRes['day_folder'] ?? date('l')) . ')';
                        $this->info('Successfully uploaded to Google Drive (' . ($driveRes['day_folder'] ?? date('l')) . ' folder)! File ID: ' . ($driveRes['file_id'] ?? 'N/A'));
                    } else {
                        $this->warn('Google Drive upload warning: ' . ($driveRes['message'] ?? 'Unknown error'));
                    }
                }

                $backup->update([
                    'status' => 'Success',
                    'file_size' => $this->formatSize($size),
                    'location' => $location,
                ]);
                $this->info('Backup generated successfully: ' . $filename . ' (via ' . ($dumpRes['method'] ?? 'dump') . ')');
            } else {
                $errorMessage = $dumpRes['message'] ?? 'Database export failed';
                Log::error("Automated Backup failed: " . $errorMessage);
                
                $backup->update([
                    'status' => 'Failed',
                    'error_message' => $errorMessage,
                ]);
                $this->error('Backup generation failed: ' . $errorMessage);
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
