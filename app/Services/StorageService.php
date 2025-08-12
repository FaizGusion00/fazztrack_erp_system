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
        
        // Store the file
        $path = $file->storeAs($directory, $filename, config('filesystems.default'));
        
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

        $disk = Storage::disk(config('filesystems.default'));
        
        // For S3 storage, use the disk's URL method
        if (config('filesystems.default') === 's3') {
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
        
        // For local storage, use the Storage::url method
        return Storage::url($path);
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

        return Storage::disk(config('filesystems.default'))->delete($path);
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

        return Storage::disk(config('filesystems.default'))->delete($paths);
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

        return Storage::disk(config('filesystems.default'))->exists($path);
    }

    /**
     * Get file size in bytes
     *
     * @param string|null $path
     * @return int|null
     */
    public static function size(?string $path): ?int
    {
        if (!$path || !self::exists($path)) {
            return null;
        }

        return Storage::disk(config('filesystems.default'))->size($path);
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

        return Storage::disk(config('filesystems.default'))->copy($from, $to);
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

        return Storage::disk(config('filesystems.default'))->move($from, $to);
    }

    /**
     * Get file MIME type
     *
     * @param string|null $path
     * @return string|null
     */
    public static function mimeType(?string $path): ?string
    {
        if (!$path || !self::exists($path)) {
            return null;
        }

        return Storage::disk(config('filesystems.default'))->mimeType($path);
    }

    /**
     * Get all files in a directory
     *
     * @param string $directory
     * @return array
     */
    public static function files(string $directory): array
    {
        return Storage::disk(config('filesystems.default'))->files($directory);
    }

    /**
     * Get all directories in a directory
     *
     * @param string $directory
     * @return array
     */
    public static function directories(string $directory): array
    {
        return Storage::disk(config('filesystems.default'))->directories($directory);
    }
}
