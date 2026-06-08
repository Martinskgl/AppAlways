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
                'name' => 'Caixa de kiwi',
                'price' => 10.00,
                'stock' => 100,
            ],
            [
                'name' => 'Caixa de laranja',
                'price' => 10.00,
                'stock' => 100,
            ],
            [
                'name' => 'Câmara de abobora',
                'price' => 20.00,
                'stock' => 100,
            ],
            [
                'name' => 'Caneta de abacate',
                'price' => 15.00,
                'stock' => 100,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
