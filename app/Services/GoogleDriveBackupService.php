<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class GoogleDriveBackupService
{
    protected ?string $serviceAccountPath;
    protected ?string $serviceAccountJson;
    protected ?string $folderId;
    protected bool $enabled;

    protected ?string $clientId;
    protected ?string $clientSecret;
    protected ?string $refreshToken;

    public function __construct()
    {
        $this->enabled = filter_var($this->getSetting('enabled', 'GOOGLE_DRIVE_BACKUP_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
        $this->folderId = $this->getSetting('folder_id', 'GOOGLE_DRIVE_FOLDER_ID');
        $this->serviceAccountPath = $this->getSetting('service_account_path', 'GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_PATH', storage_path('app/google-service-account.json'));
        $this->serviceAccountJson = $this->getSetting('service_account_json', 'GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON');

        $this->clientId = $this->getSetting('client_id', 'GOOGLE_DRIVE_CLIENT_ID');
        $this->clientSecret = $this->getSetting('client_secret', 'GOOGLE_DRIVE_CLIENT_SECRET');
        $this->refreshToken = $this->getSetting('refresh_token', 'GOOGLE_DRIVE_REFRESH_TOKEN');
    }

    /**
     * Read setting from config, env, getenv, or directly from .env file.
     */
    protected function getSetting(string $configKey, string $envKey, $default = null)
    {
        // 1. Try Laravel config
        $val = config("services.google_drive.{$configKey}");
        if (!is_null($val) && $val !== '') {
            return $val;
        }

        // 2. Try env()
        $val = env($envKey);
        if (!is_null($val) && $val !== '') {
            return $val;
        }

        // 3. Try getenv()
        $val = getenv($envKey);
        if ($val !== false && $val !== '') {
            return $val;
        }

        // 4. Try $_ENV / $_SERVER
        if (isset($_ENV[$envKey]) && $_ENV[$envKey] !== '') {
            return $_ENV[$envKey];
        }
        if (isset($_SERVER[$envKey]) && $_SERVER[$envKey] !== '') {
            return $_SERVER[$envKey];
        }

        // 5. Direct .env file reader fallback (for cached production environments)
        static $envFileParsed = null;
        if ($envFileParsed === null) {
            $envFileParsed = [];
            $envPath = base_path('.env');
            if (file_exists($envPath)) {
                $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (str_starts_with($line, '#') || !str_contains($line, '=')) continue;
                    list($k, $v) = explode('=', $line, 2);
                    $k = trim($k);
                    $v = trim($v, " \t\n\r\0\x0B\"'");
                    $envFileParsed[$k] = $v;
                }
            }
        }

        if (isset($envFileParsed[$envKey]) && $envFileParsed[$envKey] !== '') {
            return $envFileParsed[$envKey];
        }

        return $default;
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

        // 1. Check if OAuth User Credentials (Refresh Token) are present
        if (!empty($this->clientId) && !empty($this->clientSecret) && !empty($this->refreshToken)) {
            return true;
        }

        // 2. Check if Service Account JSON is present
        if (!empty($this->serviceAccountJson)) {
            return true;
        }

        return !empty($this->serviceAccountPath) && file_exists($this->serviceAccountPath);
    }

    /**
     * Get OAuth2 Access Token (either via User Refresh Token or Service Account JWT).
     */
    public function getAccessToken(): ?string
    {
        // Preferred: User OAuth Refresh Token (Uses personal Google Drive 15GB quota)
        if (!empty($this->clientId) && !empty($this->clientSecret) && !empty($this->refreshToken)) {
            return $this->getAccessTokenFromRefreshToken();
        }

        // Fallback: Service Account JWT (For Shared Drives / Workspace)
        return $this->getAccessTokenFromServiceAccount();
    }

    /**
     * Get Access Token using OAuth 2.0 Refresh Token.
     */
    protected function getAccessTokenFromRefreshToken(): ?string
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://oauth2.googleapis.com/token',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $this->refreshToken,
                'grant_type' => 'refresh_token',
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $resData = json_decode($response, true);
            return $resData['access_token'] ?? null;
        }

        Log::error("GoogleDriveBackupService: Failed to get access token from refresh token (HTTP {$httpCode}). Err: {$curlErr}. Resp: {$response}");
        return null;
    }

    /**
     * Get credentials parsed from JSON string or file for Service Account.
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
     * Generate OAuth2 Access Token using Service Account JWT without third-party SDK.
     */
    protected function getAccessTokenFromServiceAccount(): ?string
    {
        $creds = $this->getCredentials();
        if (!$creds) {
            Log::error('GoogleDriveBackupService: Service Account credentials not found or invalid.');
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
     * Get or automatically create the Day folder (e.g., 'Tuesday', 'Wednesday') inside parent folder.
     */
    public function getOrCreateDayFolder(string $parentFolderId, ?string $dayName = null, ?string $token = null): ?string
    {
        $dayName = $dayName ?: date('l'); // Sunday, Monday, Tuesday, ...
        $token = $token ?: $this->getAccessToken();
        if (!$token) {
            return $parentFolderId;
        }

        // 1. Search for existing folder with $dayName inside $parentFolderId
        $query = "'" . $parentFolderId . "' in parents and name = '" . addslashes($dayName) . "' and mimeType = 'application/vnd.google-apps.folder' and trashed = false";
        $url = 'https://www.googleapis.com/drive/v3/files?' . http_build_query([
            'q' => $query,
            'supportsAllDrives' => 'true',
            'includeItemsFromAllDrives' => 'true',
            'fields' => 'files(id, name)',
        ]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token],
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $res) {
            $data = json_decode($res, true);
            if (!empty($data['files'][0]['id'])) {
                return $data['files'][0]['id'];
            }
        }

        // 2. Folder does not exist, create it
        $createData = [
            'name' => $dayName,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => [$parentFolderId],
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://www.googleapis.com/drive/v3/files?supportsAllDrives=true',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($createData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (($httpCode === 200 || $httpCode === 201) && $res) {
            $data = json_decode($res, true);
            if (!empty($data['id'])) {
                Log::info("GoogleDriveBackupService: Created day folder '{$dayName}' (ID: {$data['id']})");
                return $data['id'];
            }
        }

        Log::warning("GoogleDriveBackupService: Could not create day folder '{$dayName}', falling back to parent folder.");
        return $parentFolderId;
    }

    /**
     * Delete existing old backup files inside the target day folder (Replacing old weekly files).
     */
    public function cleanupOldFilesInFolder(string $folderId, string $token): void
    {
        $query = "'" . $folderId . "' in parents and mimeType != 'application/vnd.google-apps.folder' and trashed = false";
        $url = 'https://www.googleapis.com/drive/v3/files?' . http_build_query([
            'q' => $query,
            'supportsAllDrives' => 'true',
            'includeItemsFromAllDrives' => 'true',
            'fields' => 'files(id, name)',
        ]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token],
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $res) {
            $data = json_decode($res, true);
            if (!empty($data['files'])) {
                foreach ($data['files'] as $file) {
                    $delId = $file['id'];
                    $delName = $file['name'];
                    
                    // Permanently delete / trash previous file
                    $delCh = curl_init();
                    curl_setopt_array($delCh, [
                        CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$delId}?supportsAllDrives=true",
                        CURLOPT_CUSTOMREQUEST => 'DELETE',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token],
                        CURLOPT_TIMEOUT => 15,
                        CURLOPT_SSL_VERIFYPEER => true,
                    ]);
                    curl_exec($delCh);
                    curl_close($delCh);

                    Log::info("GoogleDriveBackupService: Removed previous backup file '{$delName}' (ID: {$delId}) from day folder.");
                }
            }
        }
    }

    /**
     * Upload a local file directly to Google Drive day folder using Resumable Stream Upload.
     * Uses less than 10MB RAM regardless of whether the database file is 50MB, 500MB, or 5GB!
     */
    public function uploadBackup(string $filePath, ?string $fileName = null, bool $useDayFolder = true, bool $replaceExisting = true): array
    {
        @ini_set('memory_limit', '512M');
        @ini_set('max_execution_time', '600');
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

        $currentDayName = date('l'); 
        $targetFolderId = $this->folderId;

        if ($useDayFolder) {
            $targetFolderId = $this->getOrCreateDayFolder($this->folderId, $currentDayName, $token);
        }

        // If replacing existing files in this day's folder
        if ($replaceExisting && $targetFolderId) {
            $this->cleanupOldFilesInFolder($targetFolderId, $token);
        }

        $actualFileName = $fileName ?: basename($filePath);
        $fileSize = filesize($filePath);

        $mimeType = 'application/sql';
        if (str_ends_with($actualFileName, '.gz')) {
            $mimeType = 'application/gzip';
        } elseif (str_ends_with($actualFileName, '.zip')) {
            $mimeType = 'application/zip';
        }

        // Step 1: Initiate Resumable Upload Session (Uses 0 MB memory)
        $metadata = [
            'name' => $actualFileName,
            'parents' => [$targetFolderId],
            'description' => "Nachias ERP Automated Database Backup ({$currentDayName}) - " . date('d-M-Y H:i:s'),
        ];

        $initCh = curl_init();
        curl_setopt_array($initCh, [
            CURLOPT_URL => 'https://www.googleapis.com/upload/drive/v3/files?uploadType=resumable&supportsAllDrives=true',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($metadata),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json; charset=UTF-8',
                'X-Upload-Content-Type: ' . $mimeType,
                'X-Upload-Content-Length: ' . $fileSize,
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $initResponse = curl_exec($initCh);
        $initHttpCode = curl_getinfo($initCh, CURLINFO_HTTP_CODE);
        curl_close($initCh);

        $locationUrl = null;
        if ($initResponse && preg_match('/Location:\s*([^\r\n]+)/i', $initResponse, $matches)) {
            $locationUrl = trim($matches[1]);
        }

        if (!$locationUrl) {
            Log::error("GoogleDriveBackupService: Failed to initiate resumable upload (HTTP {$initHttpCode}). Response: {$initResponse}");
            return [
                'success' => false,
                'message' => "Failed to initiate resumable upload with Google Drive (HTTP {$initHttpCode})",
                'raw_response' => $initResponse,
            ];
        }

        // Step 2: Stream file in 10 MB chunks directly from disk to Google Drive (Max 10MB RAM)
        $chunkSize = 10 * 1024 * 1024; // 10 MB chunk
        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            return ['success' => false, 'message' => "Cannot open file for reading: {$filePath}"];
        }

        $offset = 0;
        $finalData = null;

        while ($offset < $fileSize) {
            $bytesToRead = min($chunkSize, $fileSize - $offset);
            $chunkData = fread($handle, $bytesToRead);
            $chunkLen = strlen($chunkData);
            $rangeEnd = $offset + $chunkLen - 1;

            $uploadCh = curl_init();
            curl_setopt_array($uploadCh, [
                CURLOPT_URL => $locationUrl,
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_POSTFIELDS => $chunkData,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Length: ' . $chunkLen,
                    "Content-Range: bytes {$offset}-{$rangeEnd}/{$fileSize}",
                    'Content-Type: ' . $mimeType,
                ],
                CURLOPT_TIMEOUT => 300,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);

            $uploadResponse = curl_exec($uploadCh);
            $uploadHttpCode = curl_getinfo($uploadCh, CURLINFO_HTTP_CODE);
            $uploadErr = curl_error($uploadCh);
            curl_close($uploadCh);

            unset($chunkData);

            if ($uploadHttpCode === 200 || $uploadHttpCode === 201) {
                $finalData = json_decode($uploadResponse, true);
                break;
            } elseif ($uploadHttpCode === 308) {
                $offset += $chunkLen;
            } else {
                fclose($handle);
                $errorMsg = "Google Drive chunk upload failed at byte {$offset}/{$fileSize} (HTTP {$uploadHttpCode}). Err: {$uploadErr}. Resp: {$uploadResponse}";
                Log::error('GoogleDriveBackupService: ' . $errorMsg);
                return [
                    'success' => false,
                    'message' => $errorMsg,
                ];
            }
        }
        fclose($handle);

        if ($finalData && !empty($finalData['id'])) {
            Log::info("GoogleDriveBackupService: Successfully streamed {$actualFileName} (" . number_format($fileSize / 1048576, 2) . " MB) to {$currentDayName} folder (ID: {$finalData['id']})");
            return [
                'success' => true,
                'file_id' => $finalData['id'],
                'file_name' => $finalData['name'] ?? $actualFileName,
                'day_folder' => $currentDayName,
                'web_view_link' => $finalData['webViewLink'] ?? null,
                'message' => "Backup uploaded successfully to '{$currentDayName}' folder on Google Drive",
            ];
        }

        return [
            'success' => false,
            'message' => 'Upload finished without file confirmation from Google Drive',
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
                'message' => 'Google Drive is not enabled or missing GOOGLE_DRIVE_FOLDER_ID / credentials in .env',
            ];
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return [
                'success' => false,
                'message' => 'Could not generate access token. Please check your credentials.',
            ];
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "https://www.googleapis.com/drive/v3/files/{$this->folderId}?supportsAllDrives=true&fields=id,name,mimeType,capabilities",
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
            'message' => "Failed to access folder ID '{$this->folderId}' (HTTP {$httpCode}). Ensure proper folder access.",
            'response' => $response,
        ];
    }

    /**
     * Dump the MySQL database to the given target file path.
     * Tries mysqldump first, falls back to PDO dump if mysqldump is not available or fails.
     */
    public function dumpDatabase(string $targetSqlPath): array
    {
        $dbName = config('database.connections.mysql.database') ?: env('DB_DATABASE');
        $dbUser = config('database.connections.mysql.username') ?: env('DB_USERNAME');
        $dbPass = config('database.connections.mysql.password') ?: env('DB_PASSWORD');
        $dbHost = config('database.connections.mysql.host') ?: env('DB_HOST', '127.0.0.1');
        $dbPort = config('database.connections.mysql.port') ?: env('DB_PORT', '3306');

        $dir = dirname($targetSqlPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        // 1. Try mysqldump across common server paths
        $possiblePaths = array_filter([
            env('MYSQLDUMP_PATH'),
            '/usr/bin/mysqldump',
            '/usr/local/mysql/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            'mysqldump',
            'C:\xampp\mysql\bin\mysqldump.exe',
        ]);

        $passwordPart = $dbPass ? "--password=" . escapeshellarg($dbPass) : "";
        $portPart = $dbPort ? "--port=" . escapeshellarg($dbPort) : "";

        foreach ($possiblePaths as $dumpPath) {
            $command = "\"{$dumpPath}\" --user=" . escapeshellarg($dbUser) . " {$passwordPart} --host=" . escapeshellarg($dbHost) . " {$portPart} " . escapeshellarg($dbName) . " > \"{$targetSqlPath}\" 2>&1";
            $output = [];
            $returnVar = -1;

            if (function_exists('exec')) {
                try {
                    @exec($command, $output, $returnVar);
                } catch (\Throwable $e) {
                    $returnVar = -1;
                }
            }

            if ($returnVar === 0 && file_exists($targetSqlPath) && filesize($targetSqlPath) > 0) {
                return [
                    'success' => true,
                    'method' => 'mysqldump',
                    'file_path' => $targetSqlPath,
                    'file_size' => filesize($targetSqlPath),
                ];
            }
        }

        // 2. Fallback to Pure PHP PDO Dump (Streamed line by line without memory accumulation)
        $pdoSuccess = $this->dumpDatabaseViaPdo($targetSqlPath);
        if ($pdoSuccess && file_exists($targetSqlPath) && filesize($targetSqlPath) > 0) {
            return [
                'success' => true,
                'method' => 'pdo',
                'file_path' => $targetSqlPath,
                'file_size' => filesize($targetSqlPath),
            ];
        }

        return [
            'success' => false,
            'message' => 'Both mysqldump and PHP PDO database dump failed.',
        ];
    }

    /**
     * Pure PHP PDO MySQL table and data exporter with streaming writes (Low RAM usage).
     */
    public function dumpDatabaseViaPdo(string $filePath): bool
    {
        @ini_set('memory_limit', '512M');
        @ini_set('max_execution_time', '600');
        try {
            $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
            $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
            $tables = [];
            $stmt = $pdo->query('SHOW FULL TABLES WHERE Table_type = "BASE TABLE"');
            while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }

            $handle = fopen($filePath, 'w');
            if (!$handle) {
                return false;
            }

            fwrite($handle, "-- Nachias ERP Database Backup (PHP PDO Engine)\n");
            fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n");
            fwrite($handle, "SET SQL_MODE=\"NO_AUTO_VALUE_ON_ZERO\";\n\n");

            foreach ($tables as $table) {
                // Table structure
                $createStmt = $pdo->query("SHOW CREATE TABLE `{$table}`");
                $createRow = $createStmt->fetch(\PDO::FETCH_NUM);
                fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
                fwrite($handle, $createRow[1] . ";\n\n");

                // Stream rows directly to disk
                $rowsStmt = $pdo->query("SELECT * FROM `{$table}`");
                while ($row = $rowsStmt->fetch(\PDO::FETCH_NUM)) {
                    $escapedValues = array_map(function ($value) use ($pdo) {
                        if ($value === null) {
                            return 'NULL';
                        }
                        return $pdo->quote($value);
                    }, $row);

                    fwrite($handle, "INSERT INTO `{$table}` VALUES(" . implode(',', $escapedValues) . ");\n");
                }
                fwrite($handle, "\n");
                fflush($handle);
            }

            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");
            fclose($handle);
            return true;
        } catch (\Throwable $e) {
            Log::error("PDO Database Dump Failed: " . $e->getMessage());
            return false;
        }
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
