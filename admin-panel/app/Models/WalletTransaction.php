<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $connection = 'mysql_main';
    protected $table = 'wallet_transactions';

    protected $fillable = [
        'user_id',
        'user_type',
        'amount',
        'type',
        'payment_type',
        'order_type',
        'note',
        'transaction_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'user_id');
    }
}
