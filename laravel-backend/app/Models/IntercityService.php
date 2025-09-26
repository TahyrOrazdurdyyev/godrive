<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntercityService extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'image',
        'enable',
        'km_charge',
        'admin_commission_data',
    ];

    protected $casts = [
        'enable' => 'boolean',
        'km_charge' => 'decimal:8,2',
        'admin_commission_data' => 'json',
    ];

    // Relationships
    public function intercityOrders()
    {
        return $this->hasMany(IntercityOrder::class);
    }

    // Scopes
    public function scopeEnabled($query)
    {
        return $query->where('enable', true);
    }
}

