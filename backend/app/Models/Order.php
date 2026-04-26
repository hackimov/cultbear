<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'number',
        'status',
        'payment_status',
        'customer_name',
        'email',
        'phone',
        'delivery_address_json',
        'address_line',
        'city',
        'postal_code',
        'subtotal_amount',
        'total_amount',
        'paid_at',
    ];

    protected $casts = [
        'delivery_address_json' => 'array',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public static function calculateTotal(iterable $items): int
    {
        $total = 0;

        foreach ($items as $item) {
            $total += ((int) $item['quantity']) * ((int) $item['unit_price']);
        }

        return $total;
    }
}
