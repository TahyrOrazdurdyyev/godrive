<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'firebase_uid',
        'full_name',
        'email',
        'phone_number',
        'country_code',
        'login_type',
        'profile_pic',
        'fcm_token',
        'reviews_count',
        'reviews_sum',
        'wallet_amount',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'reviews_count' => 'integer',
        'reviews_sum' => 'decimal:2',
        'wallet_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    // Relationships
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function intercityOrders()
    {
        return $this->hasMany(IntercityOrder::class, 'user_id');
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class, 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'customer_id');
    }

    // Accessors
    public function getAverageRatingAttribute()
    {
        return $this->reviews_count > 0 ? round($this->reviews_sum / $this->reviews_count, 2) : 0;
    }
}

