<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Enums\StatusGateway;
use App\Models\Order;

class PaymentGatewayService
{
    public function charge(Order $order, array $creditCard): array
    {
        sleep(rand(1, 5));

        $lastDigit = (int) substr($creditCard['number'], -1);
        $approved = $lastDigit % 2 == 0;

        return [
            'status' => $approved ? StatusGateway::APPROVED->value : StatusGateway::DECLINED->value,
            'transaction_id' => 'txn_fake_' . Str::random(8),
            'card_last_digits' => substr($creditCard['number'], -4),
        ];
    }
}
