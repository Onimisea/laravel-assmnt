<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = 'purchases';

    protected $fillable = [
        'name',
    ];

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transactions::class);
    }

    // Additional functionality, such as scopes, events, etc., can be added here if needed.
}
