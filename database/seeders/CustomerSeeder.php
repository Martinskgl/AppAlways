<?php

namespace Database\Seeders;

use App\Models\Customer;    
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'João',
                'email' => 'joao@example.com',
                'phone' => '123456789',
                'document' => '12345678901234',
            ],
            [
                'name' => 'Maria',
                'email' => 'maria@example.com',
                'phone' => '987654321',
                'document' => '98765432109876',
            ],
            [
                'name' => 'Pedro',
                'email' => 'pedro@example.com',
                'phone' => '555555555',
                'document' => '55555555555555',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
