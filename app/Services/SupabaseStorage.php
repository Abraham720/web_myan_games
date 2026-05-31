<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class SupabaseStorage
{
    protected $baseUrl;
    protected $serviceKey;
    protected $bucket;

    public function __construct()
    {
        $this->baseUrl = config('services.supabase.url');
        $this->serviceKey = config('services.supabase.service_key');
        $this->bucket = 'news-images';
    }

    /**
     * Upload news image to Supabase Storage (Fixed for Supabase API)
     */
    public function uploadNewsImage(UploadedFile $file): ?string
    {
        try {
            // Generate unique filename
            $filename = uniqid('news_') . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Read file content as binary
            $fileContent = file_get_contents($file->getRealPath());
            if ($fileContent === false) {
                Log::error('Failed to read file content: ' . $file->getRealPath());
                return null;
            }
            
            // Supabase Storage API endpoint
            $url = "{$this->baseUrl}/storage/v1/object/{$this->bucket}/{$filename}";
            
            // Upload with raw binary body (NOT multipart/form-data)
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->serviceKey}",
                'apikey' => $this->serviceKey,
                'Content-Type' => $file->getMimeType(),
                'Content-Length' => strlen($fileContent),
                'Cache-Control' => 'no-cache',
            ])->send('POST', $url, [
                'body' => $fileContent,
            ]);

            if ($response->successful()) {
                // Return public URL
                return "{$this->baseUrl}/storage/v1/object/public/{$this->bucket}/{$filename}";
            }

            // Log detailed error for debugging
            Log::error('Supabase upload failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'filename' => $filename,
                'url' => $url,
            ]);
            return null;
            
        } catch (\Exception $e) {
            Log::error('SupabaseStorage uploadNewsImage error: ' . $e->getMessage(), [
                'file' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ]);
            return null;
        }
    }

    /**
     * Generic upload method for reuse (used by PostController)
     */
    public function upload(string $bucket, UploadedFile $file): ?string
    {
        try {
            $filename = uniqid('post_') . '_' . time() . '.' . $file->getClientOriginalExtension();
            $fileContent = file_get_contents($file->getRealPath());
            
            if ($fileContent === false) {
                Log::error('Failed to read file content: ' . $file->getRealPath());
                return null;
            }
            
            $url = "{$this->baseUrl}/storage/v1/object/{$bucket}/{$filename}";
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->serviceKey}",
                'apikey' => $this->serviceKey,
                'Content-Type' => $file->getMimeType(),
                'Content-Length' => strlen($fileContent),
                'Cache-Control' => 'no-cache',
            ])->send('POST', $url, [
                'body' => $fileContent,
            ]);

            if ($response->successful()) {
                return "{$this->baseUrl}/storage/v1/object/public/{$bucket}/{$filename}";
            }

            Log::error('Supabase generic upload failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'bucket' => $bucket,
                'filename' => $filename,
            ]);
            return null;
            
        } catch (\Exception $e) {
            Log::error('SupabaseStorage upload error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete image from Supabase Storage
     */
    public function deleteNewsImage(string $imageUrl): bool
    {
        try {
            $path = parse_url($imageUrl, PHP_URL_PATH);
            $filename = basename($path);
            $url = "{$this->baseUrl}/storage/v1/object/{$this->bucket}/{$filename}";
            
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->serviceKey}",
                'apikey' => $this->serviceKey,
            ])->delete($url);

            return $response->successful();
            
        } catch (\Exception $e) {
            Log::error('SupabaseStorage deleteNewsImage error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get public URL for a file in bucket
     */
    public function getPublicUrl(string $filename): string
    {
        return "{$this->baseUrl}/storage/v1/object/public/{$this->bucket}/{$filename}";
    }
}