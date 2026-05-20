<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
   protected $fillable = [
        'user_id',
        'goal_name',
        'target_amount',
        'current_amount',
        'deadline',
        'status',
        'icon',
        'image',
        'color',
        'description',
        'product_link',
    ];
    public function transactions()
{
    return $this->hasMany(
        Transaction::class
    );
}
}
