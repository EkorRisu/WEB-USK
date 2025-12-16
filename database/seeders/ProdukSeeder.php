<?php

namespace Database\Seeders;

use App\Models\Produk;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    public function run()
    {
        // Get categories
        $coffeeCategory = Kategori::where('nama', 'Coffee')->first();
        $nonCoffeeCategory = Kategori::where('nama', 'Non-Coffee')->first();
        $snackCategory = Kategori::where('nama', 'Makanan Ringan')->first();
        $dessertCategory = Kategori::where('nama', 'Dessert')->first();

        $products = [
            // Coffee Products
            [
                'nama' => 'Espresso',
                'deskripsi' => 'Shot espresso murni dengan crema yang sempurna',
                'harga' => 15000,
                'stok' => 100,
                'kategori_id' => $coffeeCategory->id,
                'foto' => null
            ],
            [
                'nama' => 'Americano',
                'deskripsi' => 'Espresso yang dicampur dengan air panas',
                'harga' => 18000,
                'stok' => 100,
                'kategori_id' => $coffeeCategory->id,
                'foto' => null
            ],
            [
                'nama' => 'Cappuccino',
                'deskripsi' => 'Espresso dengan steamed milk dan foam yang creamy',
                'harga' => 25000,
                'stok' => 100,
                'kategori_id' => $coffeeCategory->id,
                'foto' => null
            ],
            [
                'nama' => 'Latte',
                'deskripsi' => 'Espresso dengan steamed milk dan sedikit foam',
                'harga' => 26000,
                'stok' => 100,
                'kategori_id' => $coffeeCategory->id,
                'foto' => null
            ],
            [
                'nama' => 'Mocha',
                'deskripsi' => 'Espresso dengan coklat, steamed milk dan whipped cream',
                'harga' => 30000,
                'stok' => 100,
                'kategori_id' => $coffeeCategory->id,
                'foto' => null
            ],
            [
                'nama' => 'Macchiato',
                'deskripsi' => 'Espresso dengan sedikit steamed milk',
                'harga' => 22000,
                'stok' => 100,
                'kategori_id' => $coffeeCategory->id,
                'foto' => null
            ],

            // Non-Coffee Products
            [
                'nama' => 'Hot Chocolate',
                'deskripsi' => 'Coklat panas dengan whipped cream',
                'harga' => 20000,
                'stok' => 100,
                'kategori_id' => $nonCoffeeCategory->id,
                'foto' => null
            ],
            [
                'nama' => 'Green Tea Latte',
                'deskripsi' => 'Matcha latte dengan steamed milk',
                'harga' => 24000,
                'stok' => 100,
                'kategori_id' => $nonCoffeeCategory->id,
                'foto' => null
            ],
            [
                'nama' => 'Chai Tea Latte',
                'deskripsi' => 'Teh rempah dengan steamed milk',
                'harga' => 23000,
                'stok' => 100,
                'kategori_id' => $nonCoffeeCategory->id,
                'foto' => null
            ],
            [
                'nama' => 'Fresh Orange Juice',
                'deskripsi' => 'Jus jeruk segar tanpa pengawet',
                'harga' => 15000,
                'stok' => 50,
                'kategori_id' => $nonCoffeeCategory->id,
                'foto' => null
            ],

            // Snacks
            [
                'nama' => 'Croissant',
                'deskripsi' => 'Pastry Prancis yang renyah dan buttery',
                'harga' => 12000,
                'stok' => 30,
                'kategori_id' => $snackCategory->id,
                'foto' => null
            ],
            [
                'nama' => 'Blueberry Muffin',
                'deskripsi' => 'Muffin lembut dengan blueberry segar',
                'harga' => 15000,
                'stok' => 25,
                'kategori_id' => $snackCategory->id,
                'foto' => null
            ],
            [
                'nama' => 'Sandwich Club',
                'deskripsi' => 'Sandwich dengan ayam, selada, tomat dan mayo',
                'harga' => 28000,
                'stok' => 20,
                'kategori_id' => $snackCategory->id,
                'foto' => null
            ],

            // Desserts
            [
                'nama' => 'Tiramisu',
                'deskripsi' => 'Dessert Italia dengan kopi dan mascarpone',
                'harga' => 35000,
                'stok' => 15,
                'kategori_id' => $dessertCategory->id,
                'foto' => null
            ],
            [
                'nama' => 'Cheesecake',
                'deskripsi' => 'New York style cheesecake dengan berry sauce',
                'harga' => 32000,
                'stok' => 12,
                'kategori_id' => $dessertCategory->id,
                'foto' => null
            ],
            [
                'nama' => 'Chocolate Brownies',
                'deskripsi' => 'Brownies coklat dengan kacang walnut',
                'harga' => 18000,
                'stok' => 20,
                'kategori_id' => $dessertCategory->id,
                'foto' => null
            ]
        ];

        foreach ($products as $product) {
            Produk::create($product);
        }

        // Assign toppings to coffee products
        $this->assignToppingsToProducts();
    }

    private function assignToppingsToProducts()
    {
        $coffeeProducts = Produk::whereHas('kategori', function($query) {
            $query->where('nama', 'Coffee');
        })->get();

        $nonCoffeeProducts = Produk::whereHas('kategori', function($query) {
            $query->whereIn('nama', ['Non-Coffee']);
        })->get();

        // Get all toppings
        $toppings = \App\Models\Topping::all();

        // Assign all toppings to coffee products
        foreach ($coffeeProducts as $product) {
            foreach ($toppings as $topping) {
                $product->toppings()->attach($topping->id, [
                    'price' => $topping->price,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // Assign selected toppings to non-coffee drinks
        $sweetToppings = $toppings->whereIn('name', ['Whipped Cream', 'Vanilla Syrup', 'Caramel Syrup', 'Chocolate Chips', 'Marshmallow']);
        foreach ($nonCoffeeProducts as $product) {
            foreach ($sweetToppings as $topping) {
                $product->toppings()->attach($topping->id, [
                    'price' => $topping->price,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}