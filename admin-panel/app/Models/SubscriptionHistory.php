<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionHistory extends Model
{
    protected $connection = 'mysql_main';
    protected $table = 'subscription_history';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'subscription_plan_id',
        'subscription_plan_data',
        'payment_type',
        'expiry_date'
    ];

    protected $casts = [
        'subscription_plan_data' => 'array',
        'expiry_date' => 'datetime',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'user_id', 'id');
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}
