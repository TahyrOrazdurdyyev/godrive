<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'image',
        'redirect_url',
        'enable',
        'position',
    ];

    protected $casts = [
        'enable' => 'boolean',
        'position' => 'integer',
    ];

    // Scopes
    public function scopeEnabled($query)
    {
        return $query->where('enable', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('position');
    }
}

