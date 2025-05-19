<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item',
        'price',
        'quantity',
        'size',
        'image',
        'special_instructions',
        'phone_number',
        'status',
        'order_number'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}