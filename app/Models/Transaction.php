<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
    'type',
    'amount',
    'category',
    'date',
    'description',
    'wallet_id', // ⚠️ WAJIB ADA
];

    public function wallet()
{
    return $this->belongsTo(Wallet::class);
}
}
