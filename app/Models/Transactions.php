<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $fillable = [
        'user_id',
        'wallet_id',
        'purchase_id',
        'bill_id',
        'amount',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
