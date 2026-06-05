<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $guarded = [];

    protected $casts = [
        'document' => 'string:14',
    ];

    public function orders(): hasMany
    {
        return $this->hasMany(Order::class);
    }
}
