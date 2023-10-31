<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TowingOrder extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'type',
        'stage',
        'has_on_road',
        'price',
        'user_id',
        'towing_id',
        'payment_method',
        'address',
        'qr_code',
        'user_latitude',
        'user_longitude'
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the towing that owns the order.
     */
    public function towing(): BelongsTo
    {
        return $this->belongsTo(Towing::class);
    }
}
