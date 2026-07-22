<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('drive:test', function (\App\Services\GoogleDriveService $driveService) {
    if (!$driveService->isConfigured()) {
        $this->error('Google Drive service is not configured correctly. Check your JSON path and .env configuration.');
        return;
    }

    $this->info('Initializing Google Drive test...');
    
    // Create a temporary test file
    $testFilePath = storage_path('app/drive_test.txt');
    file_put_contents($testFilePath, 'Google Drive Test File - ' . now());

    $this->info('Uploading test file to Google Drive...');
    
    $link = $driveService->uploadFile($testFilePath, 'drive_test.txt', 'Test Folder');
    
    // Delete temp file
    if (file_exists($testFilePath)) {
        unlink($testFilePath);
    }

    if ($link) {
        $this->info('Upload Successful! File Link:');
        $this->line($link);
    } else {
        $this->error('Upload Failed. Check logs in storage/logs/laravel.log for details.');
    }
})->purpose('Test Google Drive API integration');
