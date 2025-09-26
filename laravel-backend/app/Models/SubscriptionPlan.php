<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'amount',
        'duration_days',
        'total_orders',
        'enable',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:8,2',
        'duration_days' => 'integer',
        'total_orders' => 'integer',
        'enable' => 'boolean',
    ];

    // Relationships
    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    // Scopes
    public function scopeEnabled($query)
    {
        return $query->where('enable', true);
    }
}

