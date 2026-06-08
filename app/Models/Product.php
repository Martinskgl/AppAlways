<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'stock'];

    protected $casts = [
        'stock' => 'integer',
        'price' => 'decimal:2',
    ];

    public function orderItems(): HasMany
    {
        return $this->HasMany(OrderItem::class);
    }
}
