<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'amount',
    ];

    public function transactions()
{
    return $this->hasMany(
        Transaction::class,
        'category',
        'category'
    );
}
}
