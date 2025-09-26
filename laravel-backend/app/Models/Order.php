<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'driver_id',
        'service_id',
        'zone_id',
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
        'status',
        'otp',
        'is_ac_selected',
        'ac_non_ac_charges',
        'total_holding_charges',
        'ride_hold_time_minutes',
        'accepted_driver_ids',
        'rejected_driver_ids',
        'position_data',
        'accept_hold_time',
        'tax_data',
        'coupon_id',
        'coupon_data',
        'someone_else_data',
        'admin_commission_data',
        'vehicle_information_data',
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
        'is_ac_selected' => 'boolean',
        'ac_non_ac_charges' => 'decimal:8,2',
        'total_holding_charges' => 'decimal:8,2',
        'ride_hold_time_minutes' => 'integer',
        'accepted_driver_ids' => 'json',
        'rejected_driver_ids' => 'json',
        'position_data' => 'json',
        'accept_hold_time' => 'datetime',
        'tax_data' => 'json',
        'coupon_data' => 'json',
        'someone_else_data' => 'json',
        'admin_commission_data' => 'json',
        'vehicle_information_data' => 'json',
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

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class, 'transaction_id', 'id');
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
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

    public function scopeInZone($query, $zoneId)
    {
        return $query->where('zone_id', $zoneId);
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

    // Helper methods
    public function addAcceptedDriver($driverId)
    {
        $acceptedDrivers = $this->accepted_driver_ids ?? [];
        if (!in_array($driverId, $acceptedDrivers)) {
            $acceptedDrivers[] = $driverId;
            $this->update(['accepted_driver_ids' => $acceptedDrivers]);
        }
    }

    public function addRejectedDriver($driverId)
    {
        $rejectedDrivers = $this->rejected_driver_ids ?? [];
        if (!in_array($driverId, $rejectedDrivers)) {
            $rejectedDrivers[] = $driverId;
            $this->update(['rejected_driver_ids' => $rejectedDrivers]);
        }
    }

    public function calculateTotal()
    {
        $total = $this->final_rate;
        
        // Add holding charges
        if ($this->total_holding_charges) {
            $total += $this->total_holding_charges;
        }

        // Add taxes
        if ($this->tax_data) {
            foreach ($this->tax_data as $tax) {
                if ($tax['type'] === 'percentage') {
                    $total += ($total * $tax['value'] / 100);
                } else {
                    $total += $tax['value'];
                }
            }
        }

        // Apply coupon discount
        if ($this->coupon_data && isset($this->coupon_data['discount'])) {
            $discount = $this->coupon_data['discount'];
            if ($this->coupon_data['type'] === 'percentage') {
                $total -= ($total * $discount / 100);
            } else {
                $total -= $discount;
            }
        }

        return max(0, $total);
    }
}

