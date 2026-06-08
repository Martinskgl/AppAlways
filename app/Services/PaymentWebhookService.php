<?php

namespace App\Services;

use App\Models\Order;
use App\Enums\StatusOrder;
use App\Http\Requests\WebhookRequest;
use App\Enums\StatusGateway;
use Illuminate\Support\Facades\DB;

class PaymentWebhookService
{
    
    public function paymentProcess(Order $order, string $status): void
    {
        abort_if(
            in_array($order->status, [StatusOrder::PAID, StatusOrder::FAILED]),
            400,
            'Este pedido já foi processado.'
        );        

        DB::transaction(function () use ($order, $status) {
            if ($status === StatusGateway::APPROVED->value) {
                $order->update(['status' => StatusOrder::PAID]);

                foreach ($order->orderItems as $item) {
                      $item->product()->lockForUpdate()->first()->decrement('stock', $item->quantity);
                }
            } else {
                $order->update(['status' => StatusOrder::FAILED]);
            }
        });
    }
}
