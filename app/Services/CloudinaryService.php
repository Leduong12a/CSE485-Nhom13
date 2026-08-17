<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    /**
     * Upload ảnh đính kèm lên Cloudinary và trả về URL CDN công khai (secure_url)
     */
    public static function upload($file, string $folder = 'tlu_helpdesk_attachments'): ?string
    {
        $cloudName = env('CLOUDINARY_CLOUD_NAME', 'dj3mheysm');
        $apiKey    = env('CLOUDINARY_API_KEY', '572764996596634');
        $apiSecret = env('CLOUDINARY_API_SECRET', 'DBBEdgXPfiYoJrqtwFhxrLW-35s');

        if (! $cloudName || ! $apiKey || ! $apiSecret) {
            return null;
        }

        try {
            $timestamp       = time();
            $signatureString = "folder={$folder}&timestamp={$timestamp}" . $apiSecret;
            $signature       = sha1($signatureString);

            $response = Http::asMultipart()->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", [
                [
                    'name'     => 'file',
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                ],
                ['name' => 'api_key',   'contents' => $apiKey],
                ['name' => 'timestamp', 'contents' => (string) $timestamp],
                ['name' => 'folder',    'contents' => $folder],
                ['name' => 'signature', 'contents' => $signature],
            ]);

            if ($response->successful()) {
                return $response->json('secure_url');
            }

            Log::error('Cloudinary Upload Failed: ' . $response->body());
        } catch (\Throwable $e) {
            Log::error('Cloudinary Upload Exception: ' . $e->getMessage());
        }

        return null;
    }
}
