<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $id
 * @property mixed $stage
 * @property mixed $price
 * @property mixed $user
 * @property mixed $workshop
 * @property int|mixed $has_on_road
 */
class WorkshopOrder extends BaseModel
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
        'workshop_id',
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
     * Get the workshop that owns the order.
     */
    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

}
