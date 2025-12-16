<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed categories, toppings, and products
        $this->call([
            KategoriSeeder::class,
            ToppingSeeder::class,
            ProdukSeeder::class,
            InventorySeeder::class,
        ]);

        // Create admin user
        User::factory()->create([
            'name' => 'Admin Coffee Shop',
            'email' => 'admin@coffeeshop.com',
            'role' => 'admin',
            'password' => bcrypt('admin123'),
        ]);

        // Create sample customer
        User::factory()->create([
            'name' => 'Customer',
            'email' => 'customer@coffeeshop.com',
            'role' => 'user',
            'password' => bcrypt('customer123'),
        ]);
    }
}
