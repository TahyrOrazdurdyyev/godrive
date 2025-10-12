<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends Model
{
    use SoftDeletes;

    protected $connection = 'mysql_main';
    protected $table = 'subscription_plans';

    protected $fillable = [
        'title',
        'type',
        'amount',
        'duration_days',
        'total_orders',
        'enable',
        'description',
        'image',
        'plan_points',
        'display_order'
    ];

    protected $casts = [
        'plan_points' => 'array',
        'enable' => 'boolean',
        'amount' => 'decimal:2',
    ];
}
