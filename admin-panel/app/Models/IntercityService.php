<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntercityService extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mysql_main';
    protected $table = 'intercity_services';

    protected $fillable = [
        'title',
        'image',
        'enable',
        'price_per_seat',
        'price_full_vehicle',
        'admin_commission_data',
    ];

    protected $casts = [
        'enable' => 'boolean',
        'price_per_seat' => 'decimal:2',
        'price_full_vehicle' => 'decimal:2',
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
