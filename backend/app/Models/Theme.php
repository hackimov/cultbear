<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Theme extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'banner_url',
        'is_home_theme',
        'layout_columns',
        'sort_order',
        'is_active',
        'layout_config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_home_theme' => 'boolean',
        'layout_config' => 'array',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Публичный URL баннера: полный URL или ключ в S3.
     */
    public function getBannerSrcAttribute(): ?string
    {
        $raw = $this->attributes['banner_url'] ?? null;
        if ($raw === null || $raw === '') {
            return null;
        }
        if (filter_var($raw, FILTER_VALIDATE_URL)) {
            return $raw;
        }

        return Storage::disk('s3')->url($raw);
    }
}
