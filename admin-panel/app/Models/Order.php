<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $connection = 'mysql_main';
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'driver_id',
        'service_id',
        'zone_id',
        'coupon_id',
        'source_location_name',
        'destination_location_name',
        'source_lat',
        'source_lng',
        'destination_lat',
        'destination_lng',
        'distance',
        'distance_type',
        'duration',
        'offer_rate',
        'final_rate',
        'payment_type',
        'payment_status',
        'status'
    ];

    protected $casts = [
        'source_lat' => 'decimal:8',
        'source_lng' => 'decimal:8',
        'destination_lat' => 'decimal:8',
        'destination_lng' => 'decimal:8',
        'distance' => 'decimal:2',
        'offer_rate' => 'decimal:2',
        'final_rate' => 'decimal:2',
        'payment_status' => 'boolean',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
