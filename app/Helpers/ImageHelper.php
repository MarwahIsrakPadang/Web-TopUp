<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    public static function upload(UploadedFile $file, string $path = 'images'): string
    {
        return $file->store($path, 'public');
    }

    public static function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public static function replace(?UploadedFile $file, ?string $oldPath, string $path = 'images'): ?string
    {
        if (!$file) {
            return $oldPath;
        }

        static::delete($oldPath);

        return static::upload($file, $path);
    }
}
