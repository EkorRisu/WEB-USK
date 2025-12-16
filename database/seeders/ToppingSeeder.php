<?php

namespace Database\Seeders;

use App\Models\Topping;
use Illuminate\Database\Seeder;

class ToppingSeeder extends Seeder
{
    public function run()
    {
        $toppings = [
            [
                'name' => 'Extra Shot',
                'price' => 8000,
            ],
            [
                'name' => 'Whipped Cream',
                'price' => 5000,
            ],
            [
                'name' => 'Vanilla Syrup',
                'price' => 3000,
            ],
            [
                'name' => 'Caramel Syrup',
                'price' => 3000,
            ],
            [
                'name' => 'Hazelnut Syrup',
                'price' => 3000,
            ],
            [
                'name' => 'Chocolate Chips',
                'price' => 4000,
            ],
            [
                'name' => 'Cinnamon Powder',
                'price' => 2000,
            ],
            [
                'name' => 'Marshmallow',
                'price' => 3500,
            ]
        ];

        foreach ($toppings as $topping) {
            Topping::create($topping);
        }
    }
}