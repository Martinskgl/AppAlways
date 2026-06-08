<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Enums\StatusOrder;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function __construct(
        private readonly PaymentGatewayService $paymentGatewayService,
    ) {}

    public function process(array $payload): Order
    {
        return DB::transaction(function () use ($payload) {
            $items = [];
            $total = 0;

            foreach ($payload['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                abort_if(
                    $product->stock < $item['quantity'],
                    422,
                    "Estoque insuficiente para este produto"
                );

                $items[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ];

                $total += $item['quantity'] * $product->price;
            }
                $order = Order::create([
                    'customer_id' => $payload['customer_id'],
                    'total_amount' => $total,
                    'status' => StatusOrder::PENDING,
                ]);

                $order->orderItems()->createMany($items);

                $payment = $this->paymentGatewayService->charge($order, $payload['credit_card']);

                $order->update([
                    'transaction_id' => $payment['transaction_id'],
                    'card_last_digits' => $payment['card_last_digits'],
                ]);



                return $order;
            }
        );
    }
}
