<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    protected $connection = 'mysql_main';
    protected $table = 'drivers';

    protected $fillable = [
        'uid',
        'full_name',
        'email',
        'phone',
        'country_code',
        'profile_pic',
        'fcm_token',
        'is_active',
        'is_online',
        'wallet_amount',
        'service_id',
        'vehicle_number',
        'vehicle_type',
        'latitude',
        'longitude',
        'rotation',
        'subscription_plan_id',
        'subscription_total_orders',
        'subscription_expiry_date'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_online' => 'boolean',
        'wallet_amount' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'rotation' => 'decimal:2',
        'subscription_expiry_date' => 'datetime',
    ];

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}
