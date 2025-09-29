<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image',
        'km_charge',
        'basic_fare',
        'basic_fare_charge',
        'per_minute_charge',
        'ac_charge',
        'non_ac_charge',
        'is_ac_non_ac',
        'night_charge',
        'start_night_time',
        'end_night_time',
        'enable',
        'service_type',
        'max_persons',
        'vehicle_type',
        'intercity_per_km_charge',
        'intercity_base_fare',
        'intercity_per_minute_charge',
        'position'
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'enable' => 'boolean',
        'is_ac_non_ac' => 'boolean',
        'km_charge' => 'decimal:2',
        'basic_fare' => 'decimal:2',
        'basic_fare_charge' => 'decimal:2',
        'per_minute_charge' => 'decimal:2',
        'ac_charge' => 'decimal:2',
        'non_ac_charge' => 'decimal:2',
        'night_charge' => 'decimal:2',
        'intercity_per_km_charge' => 'decimal:2',
        'intercity_base_fare' => 'decimal:2',
        'intercity_per_minute_charge' => 'decimal:2',
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
        return $query->where('service_type', 'city');
    }

    public function scopeIntercity($query)
    {
        return $query->where('service_type', 'intercity');
    }

    // Accessors
    public function getTitleAttribute($value)
    {
        $titles = json_decode($value, true);
        if (is_array($titles)) {
            // Return English title by default, or first available
            return $titles['en'] ?? reset($titles);
        }
        return $value;
    }

    public function getDescriptionAttribute($value)
    {
        $descriptions = json_decode($value, true);
        if (is_array($descriptions)) {
            // Return English description by default, or first available
            return $descriptions['en'] ?? reset($descriptions);
        }
        return $value;
    }

    // Mutators
    public function setTitleAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['title'] = json_encode(['en' => $value]);
        } else {
            $this->attributes['title'] = json_encode($value);
        }
    }

    public function setDescriptionAttribute($value)
    {
        if (is_string($value)) {
            $this->attributes['description'] = json_encode(['en' => $value]);
        } else {
            $this->attributes['description'] = json_encode($value);
        }
    }
}
