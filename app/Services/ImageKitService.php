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
     * Upload a base64-encoded image to ImageKit (bypasses PHP upload_max_filesize).
     *
     * @param string $base64Data  Data URL or raw base64 string
     * @param string $folder      ImageKit folder
     * @param string $fileName    Base file name
     * @return string|null        URL of the uploaded file or null on failure
     */
    public function uploadBase64(string $base64Data, string $folder = '/uploads', string $fileName = ''): ?string
    {
        if (empty($this->privateKey)) {
            Log::warning('ImageKit upload skipped: IMAGEKIT_PRIVATE_KEY is not set in config');
            return null;
        }

        // Strip data URL prefix if present (e.g. "data:image/jpeg;base64,...")
        if (str_starts_with($base64Data, 'data:')) {
            $parts = explode(';', $base64Data, 2);
            $base64Data = $parts[1] ?? $base64Data;
            $base64Data = preg_replace('/^base64,/', '', $base64Data);
        }

        $contents = base64_decode($base64Data, true);
        if ($contents === false) {
            Log::error('ImageKit base64 decode failed');
            return null;
        }

        // Detect MIME from decoded bytes (in-memory, no temp file)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($contents);

        $ext = 'jpg';

        if ($mime === 'image/png') {
            $ext = 'png';
        } elseif ($mime === 'image/gif') {
            $ext = 'gif';
        } elseif ($mime === 'image/webp') {
            $ext = 'webp';
        }

        Log::info('ImageKit base64 upload starting', [
            'folder' => $folder,
            'size' => strlen($contents),
            'mime' => $mime,
        ]);

        try {
            $response = Http::withBasicAuth($this->privateKey, '')
                ->attach(
                    'file',
                    $contents,
                    ($fileName ?: 'file_'.time()).'.'.$ext
                )
                ->post('https://upload.imagekit.io/api/v1/files/upload', [
                    'fileName' => $fileName ?: 'file_'.time(),
                    'useUniqueFileName' => 'true',
                    'folder' => $folder,
                ]);

            Log::info('ImageKit base64 response status: '.$response->status());

            if ($response->successful()) {
                return $response->json()['url'];
            } else {
                Log::error('ImageKit base64 Upload Failed: '.$response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('ImageKit base64 Upload Error: '.$e->getMessage());
            return null;
        }
    }

    /**
     * Upload a base64-encoded profile picture
     */
    public function uploadProfilePictureBase64(string $base64Data): ?string
    {
        return $this->uploadBase64($base64Data, '/profiles', 'profile_'.time());
    }

    /**
     * Upload a base64-encoded WiFi QR code
     */
    public function uploadWifiQrCodeBase64(string $base64Data): ?string
    {
        return $this->uploadBase64($base64Data, '/room_wifi', 'wifi_qr_'.time());
    }

    /**
     * Check if ImageKit is configured
     */
    public function isConfigured(): bool
    {
        return ! empty($this->privateKey);
    }
}
