<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $connection = 'mysql_main';
    protected $table = 'currencies';
    
    protected $fillable = [
        'name',
        'symbol',
        'code',
        'symbol_at_right',
        'is_active'
    ];
    
    protected $casts = [
        'symbol_at_right' => 'boolean',
        'is_active' => 'boolean',
    ];
}
