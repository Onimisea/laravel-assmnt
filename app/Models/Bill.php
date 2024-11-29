<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $table = 'bills';

    protected $fillable = [
        'purchase_id',
        'name',
        'amount',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transactions::class);
    }
}
