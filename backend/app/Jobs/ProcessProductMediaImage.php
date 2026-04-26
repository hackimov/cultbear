<?php

namespace App\Jobs;

use App\Models\ProductMedia;
use App\Services\MediaProcessingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessProductMediaImage implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(private readonly int $mediaId)
    {
    }

    public function handle(MediaProcessingService $mediaProcessingService): void
    {
        $media = ProductMedia::query()->find($this->mediaId);
        if (! $media || $media->type !== 'image') {
            return;
        }

        $mediaProcessingService->processImage($media);
    }
}
