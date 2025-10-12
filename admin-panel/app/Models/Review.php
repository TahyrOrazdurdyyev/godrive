<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $connection = 'mysql_main';
    protected $table = 'reviews';

    protected $fillable = [
        'user_id',
        'driver_id',
        'order_id',
        'rating',
        'comment'
    ];

    protected $casts = [
        'rating' => 'decimal:1',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
