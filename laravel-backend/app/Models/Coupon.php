<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'title',
        'description',
        'discount',
        'discount_type',
        'minimum_amount',
        'maximum_discount',
        'expire_at',
        'enable',
        'user_limit',
        'used_count',
    ];

    protected $casts = [
        'discount' => 'decimal:8,2',
        'minimum_amount' => 'decimal:8,2',
        'maximum_discount' => 'decimal:8,2',
        'expire_at' => 'datetime',
        'enable' => 'boolean',
        'user_limit' => 'integer',
        'used_count' => 'integer',
    ];

    // Relationships
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function intercityOrders()
    {
        return $this->hasMany(IntercityOrder::class);
    }

    // Scopes
    public function scopeEnabled($query)
    {
        return $query->where('enable', true);
    }

    public function scopeValid($query)
    {
        return $query->where('enable', true)
                    ->where('expire_at', '>', now())
                    ->whereRaw('used_count < user_limit');
    }

    // Helper methods
    public function isValid()
    {
        return $this->enable 
            && $this->expire_at > now() 
            && $this->used_count < $this->user_limit;
    }

    public function canBeUsedBy($userId, $orderAmount)
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($orderAmount < $this->minimum_amount) {
            return false;
        }

        // Check if user has already used this coupon
        $userUsageCount = Order::where('user_id', $userId)
            ->where('coupon_id', $this->id)
            ->count();

        $intercityUsageCount = IntercityOrder::where('user_id', $userId)
            ->where('coupon_id', $this->id)
            ->count();

        return ($userUsageCount + $intercityUsageCount) == 0;
    }

    public function calculateDiscount($amount)
    {
        if ($this->discount_type === 'percentage') {
            $discount = ($amount * $this->discount) / 100;
            return min($discount, $this->maximum_discount);
        } else {
            return min($this->discount, $amount);
        }
    }

    public function incrementUsage()
    {
        $this->increment('used_count');
    }
}

