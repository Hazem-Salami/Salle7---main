<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'phone_number',
        'user_type',
        'is_active',
        'password',
        'fcm_token',
        'blocked',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the wallet record associated with the user.
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(UserWallet::class);
    }

    /**
     * Get the workshop record associated with the user.
     */
    public function workshop(): HasOne
    {
        return $this->hasOne(Workshop::class);
    }

    /**
     * Get the towing record associated with the user.
     */
    public function towing(): HasOne
    {
        return $this->hasOne(Towing::class);
    }

    /**
     * Get the workshop order associated with the user.
     */
    public function workshopOrder(): HasOne
    {
        return $this->hasOne(WorkshopOrder::class);
    }

    /************* query functions *************/
    public function getWorkshopOrders($user)
    {
        return WorkshopOrder::where('user_id', $user->id)->get();
    }

    public function getPreOrders($user)
    {
        return Preorder::where('user_id', $user->id)->get();
    }

    public function getTowingOrders($user)
    {
        return TowingOrder::where('user_id', $user->id)->get();
    }

    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }

    public function suggestions(): HasMany
    {
        return $this->hasMany(Suggestion::class);
    }

    public function userfiles(): hasMany
    {
        return $this->hasMany(UserFile::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }
}
