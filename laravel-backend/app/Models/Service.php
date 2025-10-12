<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

   

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
        'admin_commission_data'
    ];

    protected $casts = [
        'enable' => 'boolean',
        'offer_rate' => 'boolean',
        'intercity_type' => 'boolean',
        'is_ac_non_ac' => 'boolean',
        'km_charge' => 'decimal:2',
        'basic_fare' => 'decimal:2',
        'basic_fare_charge' => 'decimal:2',
        'per_minute_charge' => 'decimal:2',
        'ac_charge' => 'decimal:2',
        'non_ac_charge' => 'decimal:2',
        'night_charge' => 'decimal:2',
        'holding_minute_charge' => 'decimal:2',
        'admin_commission_data' => 'array',
        'deleted_at' => 'datetime',
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

    public function scopeCity($query)
    {
        return $query->where('intercity_type', false);
    }

    public function scopeIntercity($query)
    {
        return $query->where('intercity_type', true);
    }

    // Accessors and Mutators are commented out to return title as raw JSON string
    // Mobile app will handle JSON parsing
    
    // public function getTitleAttribute($value)
    // {
    //     $titles = json_decode($value, true);
    //     if (is_array($titles)) {
    //         return $titles['en'] ?? reset($titles);
    //     }
    //     return $value;
    // }

    // public function setTitleAttribute($value)
    // {
    //     if (is_string($value)) {
    //         $this->attributes['title'] = json_encode(['en' => $value]);
    //     } else {
    //         $this->attributes['title'] = json_encode($value);
    //     }
    // }
}
