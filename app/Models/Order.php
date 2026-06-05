<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    protected $casts = [
        'total_amount' => 'decimal:10,2',
    ];

    public function customer(): belongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderItems(): hasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
