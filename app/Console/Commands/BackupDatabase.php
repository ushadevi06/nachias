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
            // Auto-recover stale running backups (e.g. if previous process was killed or timed out)
            Backup::where('status', 'Running')
                ->where('created_at', '<', now()->subMinutes(30))
                ->update(['status' => 'Failed', 'error_message' => 'Process timed out or was interrupted.']);

            // Check for existing running backups
            if (Backup::where('status', 'Running')->exists()) {
                $this->error('A backup process is already running.');
                return;
            }

            $backupNo = 'BK-' . date('YmdHis');
            $rawFilename = $backupNo . '.sql';
            $gzFilename = $backupNo . '.sql.gz';
            $path = public_path('uploads/backup/');

            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            $backup = Backup::create([
                'backup_no' => $backupNo,
                'filename' => $gzFilename,
                'backup_type' => 'Database Only',
                'status' => 'Running',
                'location' => 'Local',
                'created_by' => null, 
            ]);

            $driveService = new \App\Services\GoogleDriveBackupService();
            $dumpRes = $driveService->dumpDatabase($path . $rawFilename);

            if ($dumpRes['success']) {
                $finalUploadPath = $path . $rawFilename;
                $finalUploadName = $rawFilename;

                // GZIP Compression step: shrinks 700MB+ to ~45MB in seconds
                $this->info('Compressing SQL backup with GZIP...');
                $compressSuccess = $driveService->compressSqlToGz($path . $rawFilename, $path . $gzFilename);

                if ($compressSuccess && file_exists($path . $gzFilename) && filesize($path . $gzFilename) > 0) {
                    $finalUploadPath = $path . $gzFilename;
                    $finalUploadName = $gzFilename;
                    // Safely remove the large raw .sql to conserve disk space
                    @unlink($path . $rawFilename);
                    $this->info("Backup compressed successfully: {$gzFilename} (" . $this->formatSize(filesize($finalUploadPath)) . ")");
                } else {
                    $this->warn('GZIP compression failed or produced empty file; falling back to raw .sql');
                }

                $size = filesize($finalUploadPath);
                $location = 'Local';

                // Check and upload to Google Drive if configured
                if ($driveService->isConfigured()) {
                    $this->info('Uploading backup to Google Drive...');
                    $driveRes = $driveService->uploadBackup($finalUploadPath, $finalUploadName, true, true);
                    if ($driveRes['success']) {
                        $location = 'Local & Google Drive (' . ($driveRes['day_folder'] ?? date('l')) . ')';
                        $this->info('Successfully uploaded to Google Drive (' . ($driveRes['day_folder'] ?? date('l')) . ' folder)! File ID: ' . ($driveRes['file_id'] ?? 'N/A'));
                    } else {
                        $this->warn('Google Drive upload warning: ' . ($driveRes['message'] ?? 'Unknown error'));
                    }
                }

                $backup->update([
                    'status' => 'Success',
                    'filename' => $finalUploadName,
                    'file_size' => $this->formatSize($size),
                    'location' => $location,
                ]);
                $this->info('Backup generated successfully: ' . $finalUploadName . ' (via ' . ($dumpRes['method'] ?? 'dump') . ')');
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
