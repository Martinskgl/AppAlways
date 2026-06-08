<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'status'           => $this->status,
            'total_amount'     => $this->total_amount,
            'transaction_id'   => $this->transaction_id,
            'card_last_digits' => $this->card_last_digits,
            'customer'         => [
                'id'       => $this->customer->id,
                'name'     => $this->customer->name,
                'email'    => $this->customer->email,
                'phone'    => $this->customer->phone,
                'document' => $this->customer->document,
            ],
            'items' => $this->orderItems->map(fn ($item) => [
                'product_id'   => $item->product_id,
                'product_name' => $item->product->name,
                'quantity'     => $item->quantity,
                'unit_price'   => $item->unit_price,
                'subtotal'     => $item->quantity * $item->unit_price,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}