<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Workshop extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'name',
        'address',
        'is_active',
        'latitude',
        'longitude',
        'description',
        'type',
    ];

    /**
     * Get the user that owns the workshop.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with the workshop.
     */
    public function order(): HasOne
    {
        return $this->hasOne(WorkshopOrder::class);
    }

    /**
     * Get the preorder associated with the workshop.
     */
    public function preorder(): HasOne
    {
        return $this->hasOne(Preorder::class);
    }

    /**
     * Get the times associated with the workshop.
     */
    public function times(): HasMany
    {
        return $this->hasMany(WorkshopTimes::class);
    }
}
