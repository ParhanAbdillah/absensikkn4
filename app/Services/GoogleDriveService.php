<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google\Service\Drive\Permission;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    protected $client;
    protected $driveService;
    protected $parentFolderId;

    public function __construct()
    {
        $this->parentFolderId = env('GOOGLE_DRIVE_PARENT_FOLDER_ID');
        
        $clientId = env('GOOGLE_DRIVE_CLIENT_ID');
        $clientSecret = env('GOOGLE_DRIVE_CLIENT_SECRET');
        $refreshToken = env('GOOGLE_DRIVE_REFRESH_TOKEN');

        if ($clientId && $clientSecret && $refreshToken) {
            try {
                $this->client = new Client();
                $this->client->setClientId($clientId);
                $this->client->setClientSecret($clientSecret);
                
                // Add scopes to the client
                $this->client->addScope(Drive::DRIVE);
                
                // Use the refresh token to authenticate
                $tokenResult = $this->client->refreshToken($refreshToken);
                Log::info('Google Drive OAuth2 token refresh result: ' . json_encode($tokenResult));
                
                $this->driveService = new Drive($this->client);
                return;
            } catch (\Exception $e) {
                Log::error('Google Drive OAuth2 initialization failed: ' . $e->getMessage());
            }
        }
        
        $jsonPath = env('GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON');
        $jsonString = env('GOOGLE_DRIVE_SERVICE_ACCOUNT_JSON_STRING');

        // Resolve file path if string is not provided directly
        if (!$jsonString && $jsonPath) {
            $fullPath = base_path($jsonPath);
            if (file_exists($fullPath)) {
                $jsonString = file_get_contents($fullPath);
            }
        }

        if ($jsonString) {
            try {
                $this->client = new Client();
                $this->client->setAuthConfig(json_decode($jsonString, true));
                $this->client->addScope(Drive::DRIVE);
                $this->driveService = new Drive($this->client);
            } catch (\Exception $e) {
                Log::error('Google Drive client initialization failed: ' . $e->getMessage());
            }
        }
    }

    /**
     * Checks if Google Drive API credentials are configured correctly.
     */
    public function isConfigured()
    {
        return $this->driveService !== null;
    }

    /**
     * Retrieves or creates a folder by name inside the parent KKN Google Drive folder.
     */
    public function getOrCreateFolder($folderName)
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            // Escape folder name single quotes
            $escapedName = str_replace("'", "\\'", $folderName);
            
            // Search for existing folder
            $query = "name = '{$escapedName}' and mimeType = 'application/vnd.google-apps.folder' and trashed = false";
            if ($this->parentFolderId) {
                $query .= " and '{$this->parentFolderId}' in parents";
            }

            $response = $this->driveService->files->listFiles([
                'q' => $query,
                'spaces' => 'drive',
                'fields' => 'files(id, name)',
            ]);

            if (count($response->files) > 0) {
                return $response->files[0]->id;
            }

            // Create new folder if it does not exist
            $fileMetadata = new DriveFile([
                'name' => $folderName,
                'mimeType' => 'application/vnd.google-apps.folder',
            ]);

            if ($this->parentFolderId) {
                $fileMetadata->setParents([$this->parentFolderId]);
            }

            $folder = $this->driveService->files->create($fileMetadata, [
                'fields' => 'id',
            ]);

            return $folder->id;
        } catch (\Exception $e) {
            Log::error('Google Drive: failed to get/create folder: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Uploads a file to Google Drive and sets its permissions to public read-only.
     * Returns the webViewLink of the file.
     */
    public function uploadFile($localFilePath, $fileName, $userFolderName)
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            // Get or create the user's specific folder
            $folderId = $this->getOrCreateFolder($userFolderName);

            $fileMetadata = new DriveFile([
                'name' => $fileName,
            ]);

            if ($folderId) {
                $fileMetadata->setParents([$folderId]);
            }

            $content = file_get_contents($localFilePath);
            $mimeType = mime_content_type($localFilePath);

            $file = $this->driveService->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => $mimeType,
                'uploadType' => 'multipart',
                'fields' => 'id, webViewLink',
            ]);

            // Set the permission so that anyone with the link can view it (reader)
            $permission = new Permission([
                'type' => 'anyone',
                'role' => 'reader',
            ]);
            
            $this->driveService->permissions->create($file->id, $permission);

            // Fetch the updated webViewLink explicitly
            $updatedFile = $this->driveService->files->get($file->id, [
                'fields' => 'webViewLink'
            ]);

            return $updatedFile->webViewLink;
        } catch (\Exception $e) {
            Log::error('Google Drive: upload failed: ' . $e->getMessage());
            return null;
        }
    }
}
