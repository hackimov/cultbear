<?php

namespace App\Services;

use App\Models\ProductMedia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaProcessingService
{
    public function processImage(ProductMedia $media): void
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagewebp')) {
            return;
        }

        $disk = Storage::disk($media->disk);
        if (! $disk->exists($media->path)) {
            return;
        }

        $raw = $disk->get($media->path);
        $resource = @imagecreatefromstring($raw);
        if (! $resource) {
            return;
        }

        $base = pathinfo($media->path, PATHINFO_FILENAME);
        $dir = trim(pathinfo($media->path, PATHINFO_DIRNAME), '.');
        $webpPath = ($dir ? $dir.'/' : '').$base.'.webp';
        $previewPath = ($dir ? $dir.'/' : '').$base.'_preview.jpg';

        $tempWebp = tempnam(sys_get_temp_dir(), 'webp_');
        $tempPreview = tempnam(sys_get_temp_dir(), 'preview_');

        imagewebp($resource, $tempWebp, 80);

        $width = imagesx($resource);
        $height = imagesy($resource);
        $previewWidth = min(480, $width);
        $previewHeight = (int) round(($previewWidth / max(1, $width)) * $height);
        $preview = imagecreatetruecolor($previewWidth, $previewHeight);
        imagecopyresampled($preview, $resource, 0, 0, 0, 0, $previewWidth, $previewHeight, $width, $height);
        imagejpeg($preview, $tempPreview, 82);

        $disk->put($webpPath, file_get_contents($tempWebp));
        $disk->put($previewPath, file_get_contents($tempPreview));

        $media->forceFill([
            'webp_path' => $webpPath,
            'preview_path' => $previewPath,
        ])->saveQuietly();

        @imagedestroy($preview);
        @imagedestroy($resource);
        @unlink($tempWebp);
        @unlink($tempPreview);
    }
}
