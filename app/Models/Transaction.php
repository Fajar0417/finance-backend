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
      'user_id',
];

    public function wallet()
{
    return $this->belongsTo(Wallet::class);
}
public function user()
{
    return $this->belongsTo(User::class);
}
public function goal()
{
    return $this->belongsTo(Goal::class);
}
}
