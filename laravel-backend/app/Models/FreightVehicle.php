<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FreightVehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'image',
        'enable',
    ];

    protected $casts = [
        'enable' => 'boolean',
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

