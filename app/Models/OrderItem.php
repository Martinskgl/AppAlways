<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $guarded = [];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:10,2',
    ];

    public function order(): belongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): belongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
