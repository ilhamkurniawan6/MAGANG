<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageService
{
    /**
     * Dapatkan disk aktif (MinIO/S3 jika env terisi, fallback ke public lokal)
     */
    public function getActiveDisk(): string
    {
        $minioEndpoint = env('MINIO_ENDPOINT');
        $minioKey = env('MINIO_KEY') ?? env('AWS_ACCESS_KEY_ID');
        $minioBucket = env('MINIO_BUCKET') ?? env('AWS_BUCKET');

        if (!empty($minioEndpoint) && !empty($minioKey) && !empty($minioBucket)) {
            return 's3';
        }

        return 'public';
    }

    /**
     * Simpan file unggahan ke storage
     */
    public function uploadFile(UploadedFile $file, string $folder = 'uploads'): string
    {
        $disk = $this->getActiveDisk();
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::uuid() . '.' . $extension;
        $path = $folder . '/' . $fileName;

        Storage::disk($disk)->put($path, file_get_contents($file), 'public');

        return $path;
    }

    /**
     * Dapatkan URL publik dari path file
     */
    public function getUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $disk = $this->getActiveDisk();

        if ($disk === 'public') {
            return url('storage/' . ltrim($path, '/'));
        }

        return Storage::disk($disk)->url($path);
    }

    /**
     * Hapus file dari storage
     */
    public function deleteFile(?string $path): bool
    {
        if (empty($path) || filter_var($path, FILTER_VALIDATE_URL)) {
            return false;
        }

        $disk = $this->getActiveDisk();
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }
}
