<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Service class for handling file storage operations
 * Provides consistent interface for both local and S3 storage
 */
class StorageService
{
    /**
     * Store a file and return the path
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param string|null $filename
     * @return string
     */
    public static function store(UploadedFile $file, string $directory, ?string $filename = null): string
    {
        // Generate filename if not provided
        if (!$filename) {
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        }

        // Ensure directory doesn't have leading/trailing slashes
        $directory = trim($directory, '/');
        
        // Use 'public' disk for storing files that need to be publicly accessible
        // This ensures files are stored in storage/app/public instead of storage/app/private
        $disk = config('filesystems.default') === 's3' ? 's3' : 'public';
        $path = $file->storeAs($directory, $filename, $disk);
        
        return $path;
    }

    /**
     * Store multiple files and return array of paths
     *
     * @param array $files
     * @param string $directory
     * @return array
     */
    public static function storeMultiple(array $files, string $directory): array
    {
        $paths = [];
        
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $paths[] = self::store($file, $directory);
            }
        }
        
        return $paths;
    }

    /**
     * Get the public URL for a stored file
     *
     * @param string|null $path
     * @return string|null
     */
    public static function url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        // Use 'public' disk for generating URLs for files stored in public storage
        // This ensures URLs point to storage/app/public via the public/storage symlink
        $diskName = config('filesystems.default') === 's3' ? 's3' : 'public';
        $disk = Storage::disk($diskName);
        
        // For S3 storage, use the disk's URL method
        if ($diskName === 's3') {
            $url = $disk->url($path);
            
            // For path-style endpoints, ensure bucket name is included
            if (config('filesystems.disks.s3.use_path_style_endpoint', false)) {
                $bucket = config('filesystems.disks.s3.bucket');
                $endpoint = config('filesystems.disks.s3.endpoint');
                
                // If URL doesn't contain bucket name, add it
                if (!str_contains($url, $bucket)) {
                    $url = rtrim($endpoint, '/') . '/' . $bucket . '/' . $path;
                }
            }
            
            return $url;
        }
        
        // For local/public storage, use the public disk's URL method
        return $disk->url($path);
    }

    /**
     * Delete a file from storage
     *
     * @param string|null $path
     * @return bool
     */
    public static function delete(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        // Use 'public' disk for deleting files from public storage
        $diskName = config('filesystems.default') === 's3' ? 's3' : 'public';
        return Storage::disk($diskName)->delete($path);
    }

    /**
     * Delete multiple files from storage
     *
     * @param array $paths
     * @return bool
     */
    public static function deleteMultiple(array $paths): bool
    {
        $paths = array_filter($paths); // Remove null/empty values
        
        if (empty($paths)) {
            return false;
        }

        // Use 'public' disk for deleting files from public storage
        $diskName = config('filesystems.default') === 's3' ? 's3' : 'public';
        return Storage::disk($diskName)->delete($paths);
    }

    /**
     * Check if a file exists in storage
     *
     * @param string|null $path
     * @return bool
     */
    public static function exists(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        // Use 'public' disk for checking files in public storage
        $diskName = config('filesystems.default') === 's3' ? 's3' : 'public';
        return Storage::disk($diskName)->exists($path);
    }

    /**
     * Get file size in bytes
     *
     * @param string|null $path
     * @return int|null
     */
    public static function size(?string $path): ?int
    {
        if (!$path) {
            return null;
        }

        // Use 'public' disk for checking file size in public storage
        $diskName = config('filesystems.default') === 's3' ? 's3' : 'public';
        
        if (!Storage::disk($diskName)->exists($path)) {
            return null;
        }

        return Storage::disk($diskName)->size($path);
    }

    /**
     * Copy a file to a new location
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    public static function copy(string $from, string $to): bool
    {
        if (!self::exists($from)) {
            return false;
        }

        // Use 'public' disk for copying files in public storage
        $diskName = config('filesystems.default') === 's3' ? 's3' : 'public';
        return Storage::disk($diskName)->copy($from, $to);
    }

    /**
     * Move a file to a new location
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    public static function move(string $from, string $to): bool
    {
        if (!self::exists($from)) {
            return false;
        }

        // Use 'public' disk for moving files in public storage
        $diskName = config('filesystems.default') === 's3' ? 's3' : 'public';
        return Storage::disk($diskName)->move($from, $to);
    }

    /**
     * Get file MIME type
     *
     * @param string|null $path
     * @return string|null
     */
    public static function mimeType(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        // Use 'public' disk for checking MIME type in public storage
        $diskName = config('filesystems.default') === 's3' ? 's3' : 'public';
        
        if (!Storage::disk($diskName)->exists($path)) {
            return null;
        }

        return Storage::disk($diskName)->mimeType($path);
    }

    /**
     * Get all files in a directory
     *
     * @param string $directory
     * @return array
     */
    public static function files(string $directory): array
    {
        // Use 'public' disk for listing files in public storage
        $diskName = config('filesystems.default') === 's3' ? 's3' : 'public';
        return Storage::disk($diskName)->files($directory);
    }

    /**
     * Get all directories in a directory
     *
     * @param string $directory
     * @return array
     */
    public static function directories(string $directory): array
    {
        // Use 'public' disk for listing directories in public storage
        $diskName = config('filesystems.default') === 's3' ? 's3' : 'public';
        return Storage::disk($diskName)->directories($directory);
    }
}
