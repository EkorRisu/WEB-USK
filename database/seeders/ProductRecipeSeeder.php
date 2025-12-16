<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductRecipe;
use App\Models\Produk;
use App\Models\Inventory;

class ProductRecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil sample produk dan inventory
        $espresso = Produk::where('nama', 'like', '%espresso%')->first();
        $cappuccino = Produk::where('nama', 'like', '%cappuccino%')->first();
        $americano = Produk::where('nama', 'like', '%americano%')->first();
        
        // Bahan-bahan dari inventory
        $coffeeBeans = Inventory::where('nama_bahan', 'like', '%coffee%beans%')->first();
        $milk = Inventory::where('nama_bahan', 'like', '%milk%')->first();
        $sugar = Inventory::where('nama_bahan', 'like', '%sugar%')->first();
        $vanillaSyrup = Inventory::where('nama_bahan', 'like', '%vanilla%syrup%')->first();
        $cups = Inventory::where('nama_bahan', 'like', '%cups%')->first();

        $recipes = [];

        // Resep Espresso
        if ($espresso) {
            if ($coffeeBeans) {
                $recipes[] = [
                    'product_id' => $espresso->id,
                    'inventory_id' => $coffeeBeans->id,
                    'quantity_needed' => 18.0, // 18 gram coffee beans
                    'unit' => 'gram',
                    'notes' => 'Gunakan coffee beans berkualitas tinggi untuk espresso yang sempurna',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if ($sugar) {
                $recipes[] = [
                    'product_id' => $espresso->id,
                    'inventory_id' => $sugar->id,
                    'quantity_needed' => 5.0, // 5 gram sugar (optional)
                    'unit' => 'gram',
                    'notes' => 'Opsional, sesuai selera pelanggan',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if ($cups) {
                $recipes[] = [
                    'product_id' => $espresso->id,
                    'inventory_id' => $cups->id,
                    'quantity_needed' => 1.0, // 1 cup
                    'unit' => 'pcs',
                    'notes' => 'Gunakan espresso cup ukuran kecil',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Resep Cappuccino
        if ($cappuccino) {
            if ($coffeeBeans) {
                $recipes[] = [
                    'product_id' => $cappuccino->id,
                    'inventory_id' => $coffeeBeans->id,
                    'quantity_needed' => 20.0, // 20 gram coffee beans
                    'unit' => 'gram',
                    'notes' => 'Base espresso untuk cappuccino',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if ($milk) {
                $recipes[] = [
                    'product_id' => $cappuccino->id,
                    'inventory_id' => $milk->id,
                    'quantity_needed' => 150.0, // 150ml milk
                    'unit' => 'ml',
                    'notes' => 'Steam milk hingga mengembang untuk foam yang sempurna',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if ($sugar) {
                $recipes[] = [
                    'product_id' => $cappuccino->id,
                    'inventory_id' => $sugar->id,
                    'quantity_needed' => 8.0, // 8 gram sugar
                    'unit' => 'gram',
                    'notes' => 'Sesuaikan dengan selera pelanggan',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if ($cups) {
                $recipes[] = [
                    'product_id' => $cappuccino->id,
                    'inventory_id' => $cups->id,
                    'quantity_needed' => 1.0, // 1 cup
                    'unit' => 'pcs',
                    'notes' => 'Gunakan cappuccino cup ukuran sedang',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Resep Americano
        if ($americano) {
            if ($coffeeBeans) {
                $recipes[] = [
                    'product_id' => $americano->id,
                    'inventory_id' => $coffeeBeans->id,
                    'quantity_needed' => 16.0, // 16 gram coffee beans
                    'unit' => 'gram',
                    'notes' => 'Double shot espresso untuk americano',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if ($sugar) {
                $recipes[] = [
                    'product_id' => $americano->id,
                    'inventory_id' => $sugar->id,
                    'quantity_needed' => 6.0, // 6 gram sugar
                    'unit' => 'gram',
                    'notes' => 'Opsional untuk americano',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if ($vanillaSyrup) {
                $recipes[] = [
                    'product_id' => $americano->id,
                    'inventory_id' => $vanillaSyrup->id,
                    'quantity_needed' => 10.0, // 10ml vanilla syrup
                    'unit' => 'ml',
                    'notes' => 'Tambahan rasa vanilla untuk varian rasa',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            if ($cups) {
                $recipes[] = [
                    'product_id' => $americano->id,
                    'inventory_id' => $cups->id,
                    'quantity_needed' => 1.0, // 1 cup
                    'unit' => 'pcs',
                    'notes' => 'Gunakan cup ukuran besar untuk americano',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert semua resep yang valid
        if (!empty($recipes)) {
            ProductRecipe::insert($recipes);
            
            $this->command->info('Sample product recipes created successfully!');
            $this->command->info('Total recipes: ' . count($recipes));
        } else {
            $this->command->warn('No matching products or inventory found for creating sample recipes.');
            $this->command->info('Make sure you have products and inventory data before running this seeder.');
        }
    }
}
