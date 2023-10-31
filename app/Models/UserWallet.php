<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserWallet extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'type',
        'amount',
        'user_id'
    ];

    /**
     * Get the wallet record associated with the user.
     */
    public function charges(): HasOne
    {
        return $this->hasOne(WalletChargeHistory::class, 'wallet_id');
    }

    /**
     * Get the user that owns the wallet.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
