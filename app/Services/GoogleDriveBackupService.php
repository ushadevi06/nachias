<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class GoogleDriveBackupService
{
    protected ?string $serviceAccountPath;
    protected ?string $serviceAccountJson;
    protected ?string $folderId;
    protected bool $enabled;

    public function __construct()
    {
        $this->enabled = filter_var(env('GOOGLE_DRIVE_BACKUP_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
        $this->folderId = env('GOOGLE_DRIVE_FOLDER_ID');
        $this->serviceAccountPath = env('GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_PATH', storage_path('app/google-service-account.json'));
        $this->serviceAccountJson = env('GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON');
    }

    /**
     * Check if Google Drive backup configuration is set up and available.
     */
    public function isConfigured(): bool
    {
        if (!$this->enabled) {
            return false;
        }

        if (empty($this->folderId)) {
            return false;
        }

        if (!empty($this->serviceAccountJson)) {
            return true;
        }

        return !empty($this->serviceAccountPath) && file_exists($this->serviceAccountPath);
    }

    /**
     * Get credentials parsed from JSON string or file.
     */
    protected function getCredentials(): ?array
    {
        if (!empty($this->serviceAccountJson)) {
            $data = json_decode($this->serviceAccountJson, true);
            if ($data && isset($data['client_email'], $data['private_key'])) {
                return $data;
            }
        }

        if ($this->serviceAccountPath && file_exists($this->serviceAccountPath)) {
            $content = file_get_contents($this->serviceAccountPath);
            $data = json_decode($content, true);
            if ($data && isset($data['client_email'], $data['private_key'])) {
                return $data;
            }
        }

        return null;
    }

    /**
     * Generate OAuth2 Access Token using Service Account JWT without any third-party SDK.
     */
    public function getAccessToken(): ?string
    {
        $creds = $this->getCredentials();
        if (!$creds) {
            Log::error('GoogleDriveBackupService: Credentials not found or invalid.');
            return null;
        }

        $now = time();
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT',
        ];

        $claim = [
            'iss' => $creds['client_email'],
            'scope' => 'https://www.googleapis.com/auth/drive.file https://www.googleapis.com/auth/drive',
            'aud' => $creds['token_uri'] ?? 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now,
        ];

        $base64UrlHeader = $this->base64UrlEncode(json_encode($header));
        $base64UrlClaim = $this->base64UrlEncode(json_encode($claim));
        $signatureInput = $base64UrlHeader . '.' . $base64UrlClaim;

        $privateKey = $creds['private_key'];
        $signature = '';

        if (!openssl_sign($signatureInput, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            Log::error('GoogleDriveBackupService: Failed to sign JWT with private key. OpenSSL Error: ' . openssl_error_string());
            return null;
        }

        $jwt = $signatureInput . '.' . $this->base64UrlEncode($signature);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $creds['token_uri'] ?? 'https://oauth2.googleapis.com/token',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            Log::error("GoogleDriveBackupService: OAuth token request failed with HTTP {$httpCode}. Err: {$curlErr}. Resp: {$response}");
            return null;
        }

        $resData = json_decode($response, true);
        return $resData['access_token'] ?? null;
    }

    /**
     * Upload a local file directly to Google Drive folder using multipart REST API.
     */
    public function uploadBackup(string $filePath, ?string $fileName = null): array
    {
        if (!file_exists($filePath)) {
            return ['success' => false, 'message' => 'Local backup file not found at: ' . $filePath];
        }

        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'Google Drive backup is not enabled or not configured in .env'];
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Failed to obtain Google Drive OAuth access token'];
        }

        $actualFileName = $fileName ?: basename($filePath);
        $fileContent = file_get_contents($filePath);

        $metadata = [
            'name' => $actualFileName,
            'parents' => [$this->folderId],
            'description' => 'Nachias ERP Automated Database Backup - ' . date('d-M-Y H:i:s'),
        ];

        $boundary = '-------314159265358979323846';
        $delimiter = "\r\n--" . $boundary . "\r\n";
        $closeDelimiter = "\r\n--" . $boundary . "--";

        $mimeType = 'application/sql';
        if (str_ends_with($actualFileName, '.gz')) {
            $mimeType = 'application/gzip';
        } elseif (str_ends_with($actualFileName, '.zip')) {
            $mimeType = 'application/zip';
        }

        $body = $delimiter
            . "Content-Type: application/json; charset=UTF-8\r\n\r\n"
            . json_encode($metadata)
            . $delimiter
            . "Content-Type: {$mimeType}\r\n"
            . "Content-Transfer-Encoding: binary\r\n\r\n"
            . $fileContent
            . $closeDelimiter;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&fields=id,name,webViewLink,size',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: multipart/related; boundary=' . $boundary,
                'Content-Length: ' . strlen($body),
            ],
            CURLOPT_TIMEOUT => 300,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            Log::info("GoogleDriveBackupService: Successfully uploaded {$actualFileName} (ID: " . ($data['id'] ?? '') . ")");
            return [
                'success' => true,
                'file_id' => $data['id'] ?? null,
                'file_name' => $data['name'] ?? $actualFileName,
                'web_view_link' => $data['webViewLink'] ?? null,
                'message' => 'Backup uploaded successfully to Google Drive',
            ];
        }

        $errorMsg = "Google Drive upload failed (HTTP {$httpCode}). Err: {$curlErr}. Response: {$response}";
        Log::error('GoogleDriveBackupService: ' . $errorMsg);
        return [
            'success' => false,
            'message' => $errorMsg,
            'raw_response' => $response,
        ];
    }

    /**
     * Test connection to Google Drive and verify write permission in the target folder.
     */
    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Google Drive is not enabled or missing GOOGLE_DRIVE_FOLDER_ID / Service Account credentials in .env',
            ];
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return [
                'success' => false,
                'message' => 'Could not generate access token. Please check your Service Account JSON credentials.',
            ];
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$this->folderId}?fields=id,name,mimeType,capabilities",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
            ],
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $folder = json_decode($response, true);
            $canAddChildren = $folder['capabilities']['canAddChildren'] ?? false;
            return [
                'success' => true,
                'folder_name' => $folder['name'] ?? 'Target Folder',
                'folder_id' => $folder['id'] ?? $this->folderId,
                'can_write' => $canAddChildren,
                'message' => 'Successfully connected to Google Drive folder: ' . ($folder['name'] ?? $this->folderId),
            ];
        }

        return [
            'success' => false,
            'message' => "Failed to access folder ID '{$this->folderId}' (HTTP {$httpCode}). Ensure the Service Account email is added as 'Editor' to the Google Drive folder.",
            'response' => $response,
        ];
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
