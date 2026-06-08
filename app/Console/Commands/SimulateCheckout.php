<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Product;
use App\Services\CheckoutService;
use Illuminate\Console\Command;

class SimulateCheckout extends Command
{
    protected $signature = 'app:simulate-checkout
                            {--approved : Usar cartão com último dígito par (aprovado)}
                            {--declined : Usar cartão com último dígito ímpar (recusado)}';

    protected $description = 'Simula um checkout completo com dados aleatórios';

    public function handle(CheckoutService $checkoutService): void
    {
        $customer = Customer::inRandomOrder()->firstOrFail();
        $products = Product::where('stock', '>', 0)->inRandomOrder()->take(rand(1, 3))->get();

        if ($products->isEmpty()) {
            $this->error('Nenhum produto com estoque disponível.');
            return;
        }

        $lastDigit = match (true) {
            $this->option('approved') => '2',
            $this->option('declined') => '1',
            default => (string) rand(0, 9),
        };

        $payload = [
            'customer_id' => $customer->id,
            'items' => $products->map(fn ($p) => [
                'product_id' => $p->id,
                'quantity'   => 1,
            ])->toArray(),
            'credit_card' => [
                'holder_name'  => $customer->name,
                'number'       => '123456789012345' . $lastDigit,
                'expiry_month' => 12,
                'expiry_year'  => 2027,
                'cvv'          => '123',
            ],
        ];

        $this->info("Cliente: {$customer->name}");
        $this->info("Produtos: " . $products->pluck('name')->join(', '));
        $this->info("Último dígito do cartão: {$lastDigit}");
        $this->newLine();

        $order = $checkoutService->process($payload);

        $this->info("✅ Pedido #{$order->id} criado com sucesso!");
        $this->line("   Status:         {$order->status->value}");
        $this->line("   Total:          R$ {$order->total_amount}");
        $this->line("   Transaction ID: {$order->transaction_id}");
        $this->line("   Card digits:    **** {$order->card_last_digits}");
    }
}
