<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'banner_url',
        'layout_columns',
        'sort_order',
        'is_active',
        'layout_config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'layout_config' => 'array',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
