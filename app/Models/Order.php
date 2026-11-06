<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Order extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'orders';

    protected $fillable = [
        'order_number',
        'user_id',
        'user_name',
        'user_email',
        'items',
        'total_amount',
        'discount_amount',
        'final_amount',
        'status',
        'payment_method',
        'coupon_code',
    ];

    protected $casts = [
        'items' => 'array',
        'total_amount' => 'float',
        'discount_amount' => 'float',
        'final_amount' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
