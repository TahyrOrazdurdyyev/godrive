<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;
    protected $connection = 'mysql_main';
    protected $fillable = [
        'title',
        'image',
        'enable',
        'offer_rate',
        'intercity_type',
        'is_ac_non_ac',
        'ac_charge',
        'non_ac_charge',
        'basic_fare',
        'basic_fare_charge',
        'holding_minute',
        'holding_minute_charge',
        'start_night_time',
        'end_night_time',
        'night_charge',
        'per_minute_charge',
        'km_charge',
        'admin_commission_data',
    ];

    protected $casts = [
        'enable' => 'boolean',
        'offer_rate' => 'boolean',
        'intercity_type' => 'boolean',
        'is_ac_non_ac' => 'boolean',
        'ac_charge' => 'decimal:2',
        'non_ac_charge' => 'decimal:2',
        'basic_fare' => 'decimal:2',
        'basic_fare_charge' => 'decimal:2',
        'holding_minute_charge' => 'decimal:2',
        'night_charge' => 'decimal:2',
        'per_minute_charge' => 'decimal:2',
        'km_charge' => 'decimal:2',
        'admin_commission_data' => 'array',
    ];
}
