<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $connection = 'mysql_main';
    protected $table = 'users';
    
    protected $fillable = [
        'uid',
        'full_name',
        'email',
        'phone_number',
        'country_code',
        'profile_pic',
        'is_active',
        'wallet_amount',
        'created_at',
        'updated_at'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'wallet_amount' => 'decimal:2',
    ];
}
