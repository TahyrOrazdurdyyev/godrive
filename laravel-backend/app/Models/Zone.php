<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'coordinates',
        'enable',
    ];

    protected $casts = [
        'coordinates' => 'json',
        'enable' => 'boolean',
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

    // Helper methods
    public function isPointInZone($lat, $lng)
    {
        if (!$this->coordinates || !is_array($this->coordinates)) {
            return false;
        }

        $polygon = $this->coordinates;
        $x = $lng;
        $y = $lat;

        $inside = false;
        $j = count($polygon) - 1;

        for ($i = 0; $i < count($polygon); $i++) {
            $xi = $polygon[$i]['lng'];
            $yi = $polygon[$i]['lat'];
            $xj = $polygon[$j]['lng'];
            $yj = $polygon[$j]['lat'];

            if ((($yi > $y) != ($yj > $y)) && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi)) {
                $inside = !$inside;
            }
            $j = $i;
        }

        return $inside;
    }
}

