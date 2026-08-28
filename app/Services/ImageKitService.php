<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImageKitService
{
    private $privateKey;

    public function __construct()
    {
        $this->privateKey = config('services.imagekit.private_key', '');
    }

    /**
     * Upload a file to ImageKit
     *
     * @return string|null URL of the uploaded file or null on failure
     */
    public function upload(UploadedFile $file, string $folder = '/uploads', string $fileName = ''): ?string
    {
        if (empty($this->privateKey)) {
            Log::warning('ImageKit upload skipped: IMAGEKIT_PRIVATE_KEY is not set in config');

            return null;
        }

        Log::info('ImageKit upload starting', [
            'folder' => $folder,
            'size' => $file->getSize(),
            'mime' => $file->getMimeType(),
            'name' => $file->getClientOriginalName(),
        ]);

        try {
            $contents = file_get_contents($file->getRealPath());
            Log::info('ImageKit file_read contents length: '.strlen($contents));

            $response = Http::withBasicAuth($this->privateKey, '')
                ->attach(
                    'file',
                    $contents,
                    $file->getClientOriginalName()
                )
                ->post('https://upload.imagekit.io/api/v1/files/upload', [
                    'fileName' => $fileName ?: 'file_'.time(),
                    'useUniqueFileName' => 'true',
                    'folder' => $folder,
                ]);

            Log::info('ImageKit response status: '.$response->status());

            if ($response->successful()) {
                return $response->json()['url'];
            } else {
                Log::error('ImageKit Upload Failed: '.$response->body());

                return null;
            }
        } catch (\Exception $e) {
            Log::error('ImageKit Upload Error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Upload a profile picture
     *
     * @return string|null URL of the uploaded file or null on failure
     */
    public function uploadProfilePicture(UploadedFile $file): ?string
    {
        return $this->upload($file, '/profiles', 'profile_'.time());
    }

    /**
     * Upload a WiFi QR code
     *
     * @return string|null URL of the uploaded file or null on failure
     */
    public function uploadWifiQrCode(UploadedFile $file): ?string
    {
        return $this->upload($file, '/room_wifi', 'wifi_qr_'.time());
    }

    /**
     * Check if ImageKit is configured
     */
    public function isConfigured(): bool
    {
        return ! empty($this->privateKey);
    }
}
