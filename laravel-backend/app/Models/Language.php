<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'is_default',
        'enable',
        'is_deleted',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'enable' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    // Scopes
    public function scopeEnabled($query)
    {
        return $query->where('enable', true)->where('is_deleted', false);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
