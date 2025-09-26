<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntercityOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'driver_id',
        'intercity_service_id',
        'zone_id',
        'source_city',
        'source_location_name',
        'destination_city',
        'destination_location_name',
        'source_lat',
        'source_lng',
        'destination_lat',
        'destination_lng',
        'distance',
        'distance_type',
        'offer_rate',
        'final_rate',
        'payment_type',
        'payment_status',
        'status',
        'otp',
        'parcel_dimension',
        'parcel_weight',
        'parcel_images',
        'when_date',
        'when_time',
        'number_of_passenger',
        'comments',
        'accepted_driver_ids',
        'rejected_driver_ids',
        'position_data',
        'tax_data',
        'coupon_id',
        'coupon_data',
        'freight_vehicle_id',
        'someone_else_data',
        'admin_commission_data',
    ];

    protected $casts = [
        'source_lat' => 'decimal:10,8',
        'source_lng' => 'decimal:11,8',
        'destination_lat' => 'decimal:10,8',
        'destination_lng' => 'decimal:11,8',
        'distance' => 'decimal:8,2',
        'offer_rate' => 'decimal:8,2',
        'final_rate' => 'decimal:8,2',
        'payment_status' => 'boolean',
        'parcel_images' => 'json',
        'when_date' => 'date',
        'when_time' => 'time',
        'number_of_passenger' => 'integer',
        'accepted_driver_ids' => 'json',
        'rejected_driver_ids' => 'json',
        'position_data' => 'json',
        'tax_data' => 'json',
        'coupon_data' => 'json',
        'someone_else_data' => 'json',
        'admin_commission_data' => 'json',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function intercityService()
    {
        return $this->belongsTo(IntercityService::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function freightVehicle()
    {
        return $this->belongsTo(FreightVehicle::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'order_id');
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class, 'transaction_id', 'id');
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'order_id');
    }

    // Scopes
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'placed');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['placed', 'driver_accepted', 'driver_arrived', 'on_ride']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}

