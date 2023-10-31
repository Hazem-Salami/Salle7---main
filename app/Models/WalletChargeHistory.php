<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletChargeHistory extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'pre_mount',
        'new_amount',
        'charge',
        'wallet_id',
        'type'
    ];

    /**
     * Get the wallet that owns the history.
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(UserWallet::class, 'wallet_id');
    }
}
