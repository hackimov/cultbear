<?php

namespace App\Models;

use App\Jobs\ProcessProductMediaImage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ProductMedia extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'disk',
        'path',
        'preview_path',
        'webp_path',
        'mime_type',
        'size',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $media): void {
            if ($media->type !== 'image' || ! $media->path) {
                return;
            }

            ProcessProductMediaImage::dispatch($media->id);
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
