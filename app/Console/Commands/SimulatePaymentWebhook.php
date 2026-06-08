<?php

namespace App\Console\Commands;

use App\Enums\StatusOrder;
use App\Models\Order;
use Illuminate\Console\Command;
use App\Services\PaymentWebhookService;
use App\Enums\StatusGateway;

class SimulatePaymentWebhook extends Command
{
    protected $signature = 'app:simulate-payment-webhook';
    protected $description = 'Simula o gateway enviando confirmações de pagamento para pedidos pendentes';

    public function __construct(
        private readonly PaymentWebhookService $webhookService,
    ) {
        parent::__construct();
    }

    public function handle(): void
    {
        $orders = Order::where('status', StatusOrder::PENDING)
            ->whereNotNull('transaction_id')
            ->whereNotNull('card_last_digits')
            ->with('orderItems.product')
            ->get();

        if ($orders->isEmpty()) {
            $this->info('Nenhum pedido pendente encontrado.');
            return;
        }

        $this->info("Processando {$orders->count()} pedido(s) pendente(s)...");

        foreach ($orders as $order) {
            $lastDigit = (int) substr($order->card_last_digits, -1);
            $approved  = $lastDigit % 2 === 0;

            $status = $approved ? StatusGateway::APPROVED->value : StatusGateway::DECLINED->value;
            $this->webhookService->paymentProcess($order, $status);

            $status = $approved ? 'aprovado ✅' : 'recusado ❌';
            $this->line("Pedido #{$order->id} — {$status}");
        }

        $this->info('Processamento concluído.');
    }
}
