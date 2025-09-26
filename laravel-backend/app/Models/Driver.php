<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'drivers';

    protected $fillable = [
        'firebase_uid',
        'full_name',
        'email',
        'phone_number',
        'country_code',
        'login_type',
        'profile_pic',
        'fcm_token',
        'is_online',
        'is_active',
        'document_verification',
        'service_id',
        'reviews_count',
        'reviews_sum',
        'wallet_amount',
        'location_lat',
        'location_lng',
        'rotation',
        'vehicle_information',
        'zone_ids',
        'subscription_plan_id',
        'subscription_total_orders',
        'subscription_expiry_date',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'is_active' => 'boolean',
        'document_verification' => 'boolean',
        'reviews_count' => 'integer',
        'reviews_sum' => 'decimal:2',
        'wallet_amount' => 'decimal:2',
        'location_lat' => 'decimal:10,8',
        'location_lng' => 'decimal:11,8',
        'rotation' => 'decimal:8,2',
        'vehicle_information' => 'json',
        'zone_ids' => 'json',
        'subscription_total_orders' => 'integer',
        'subscription_expiry_date' => 'datetime',
    ];

    // Relationships
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'driver_id');
    }

    public function intercityOrders()
    {
        return $this->hasMany(IntercityOrder::class, 'driver_id');
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class, 'user_id')->where('user_type', 'driver');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'driver_id');
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'driver_id');
    }

    // Scopes
    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('document_verification', true);
    }

    public function scopeInZone($query, $zoneId)
    {
        return $query->whereJsonContains('zone_ids', $zoneId);
    }

    // Accessors
    public function getAverageRatingAttribute()
    {
        return $this->reviews_count > 0 ? round($this->reviews_sum / $this->reviews_count, 2) : 0;
    }

    // Helper methods
    public function updateLocation($lat, $lng, $rotation = null)
    {
        $this->update([
            'location_lat' => $lat,
            'location_lng' => $lng,
            'rotation' => $rotation ?? $this->rotation,
        ]);
    }
}

