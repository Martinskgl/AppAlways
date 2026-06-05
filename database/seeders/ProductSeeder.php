<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Caixa de Limão',
                'price' => 10.00,
                'stock' => 100,
            ],
            [
                'name' => 'Caixa de Limão',
                'price' => 10.00,
                'stock' => 100,
            ],
            [
                'name' => 'Câmara de Limão',
                'price' => 20.00,
                'stock' => 100,
            ],
            [
                'name' => 'Caneta de Limão',
                'price' => 15.00,
                'stock' => 100,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
