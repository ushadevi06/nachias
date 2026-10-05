<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleDriveBackupService;

class TestGoogleDriveBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:test-drive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Google Drive connection and folder access for database backups';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Google Drive Backup Connection...');

        $service = new GoogleDriveBackupService();

        if (!$service->isConfigured()) {
            $this->error('Google Drive is not configured or disabled.');
            $this->line('');
            $this->line('Please add the following variables to your .env file:');
            $this->comment('GOOGLE_DRIVE_BACKUP_ENABLED=true');
            $this->comment('GOOGLE_DRIVE_FOLDER_ID=your_google_drive_folder_id');
            $this->comment('GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_PATH=storage/app/google-service-account.json');
            return 1;
        }

        $this->info('Connecting to Google Drive API...');
        $result = $service->testConnection();

        if ($result['success']) {
            $this->info('SUCCESS: ' . $result['message']);
            if (isset($result['can_write']) && !$result['can_write']) {
                $this->warn('WARNING: The service account has view access but might not have WRITE/EDITOR permission to this folder.');
            } else {
                $this->info('Write Permission: Verified OK');
            }
            return 0;
        } else {
            $this->error('CONNECTION FAILED: ' . $result['message']);
            return 1;
        }
    }
}
