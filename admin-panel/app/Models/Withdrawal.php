<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $connection = 'mysql_main';
    protected $table = 'withdrawals';

    protected $fillable = [
        'driver_id',
        'amount',
        'status',
        'payment_method',
        'note'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}
