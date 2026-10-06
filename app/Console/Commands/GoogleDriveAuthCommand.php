<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GoogleDriveAuthCommand extends Command
{
    protected $signature = 'backup:drive-auth';
    protected $description = 'Generate Google Drive OAuth 2.0 Refresh Token for database backups';

    public function handle()
    {
        $this->info('========================================================');
        $this->info('  Google Drive OAuth 2.0 Authorization Setup');
        $this->info('========================================================');
        $this->line('');

        $clientId = env('GOOGLE_DRIVE_CLIENT_ID');
        $clientSecret = env('GOOGLE_DRIVE_CLIENT_SECRET');

        if (empty($clientId)) {
            $clientId = $this->ask('Enter your Google OAuth Client ID');
        } else {
            $this->info("Using GOOGLE_DRIVE_CLIENT_ID from .env: {$clientId}");
        }

        if (empty($clientSecret)) {
            $clientSecret = $this->secret('Enter your Google OAuth Client Secret');
        }

        if (empty($clientId) || empty($clientSecret)) {
            $this->error('Client ID and Client Secret are required!');
            return 1;
        }

        $redirectUri = 'urn:ietf:wg:oauth:2.0:oob';
        $scope = urlencode('https://www.googleapis.com/auth/drive.file https://www.googleapis.com/auth/drive');

        $authUrl = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/drive.file https://www.googleapis.com/auth/drive',
            'access_type' => 'offline',
            'prompt' => 'consent',
        ]);

        $this->line('');
        $this->info('1. Open this URL in your web browser:');
        $this->comment($authUrl);
        $this->line('');
        $this->info('2. Log in with your Google account & allow permissions.');
        $this->info('3. Copy the authorization code shown on screen.');
        $this->line('');

        $authCode = $this->ask('Paste the authorization code here');

        if (empty($authCode)) {
            $this->error('Authorization code cannot be empty.');
            return 1;
        }

        $this->info('Exchanging code for Refresh Token...');

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://oauth2.googleapis.com/token',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'code' => trim($authCode),
                'grant_type' => 'authorization_code',
                'redirect_uri' => $redirectUri,
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $resData = json_decode($response, true);

        if ($httpCode === 200 && isset($resData['refresh_token'])) {
            $refreshToken = $resData['refresh_token'];
            $this->info('');
            $this->info('SUCCESS! Refresh Token obtained.');
            $this->line('');
            $this->comment("GOOGLE_DRIVE_CLIENT_ID={$clientId}");
            $this->comment("GOOGLE_DRIVE_CLIENT_SECRET={$clientSecret}");
            $this->comment("GOOGLE_DRIVE_REFRESH_TOKEN={$refreshToken}");
            $this->line('');

            if ($this->confirm('Do you want to save these automatically to your .env file?', true)) {
                $this->updateEnvFile([
                    'GOOGLE_DRIVE_CLIENT_ID' => $clientId,
                    'GOOGLE_DRIVE_CLIENT_SECRET' => $clientSecret,
                    'GOOGLE_DRIVE_REFRESH_TOKEN' => $refreshToken,
                    'GOOGLE_DRIVE_BACKUP_ENABLED' => 'true',
                ]);
                $this->info('Successfully updated .env file!');
            }
            return 0;
        }

        $this->error("Failed to get refresh token (HTTP {$httpCode}). Response: " . $response);
        return 1;
    }

    private function updateEnvFile(array $data)
    {
        $envPath = base_path('.env');
        if (!file_exists($envPath)) return;

        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $val) {
            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}=\"{$val}\"", $envContent);
            } else {
                $envContent .= "\n{$key}=\"{$val}\"";
            }
        }

        file_put_contents($envPath, $envContent);
    }
}
