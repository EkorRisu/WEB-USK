<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProductRecipe;
use App\Models\Produk;
use App\Models\Inventory;

class TestRecipeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Testing ProductRecipe...');
        
        try {
            // Test model access
            $productsCount = Produk::count();
            $inventoriesCount = Inventory::count();
            
            $this->command->info("Found {$productsCount} products and {$inventoriesCount} inventories");
            
            if ($productsCount > 0 && $inventoriesCount > 0) {
                $product = Produk::first();
                $inventory = Inventory::first();
                
                $this->command->info("Creating recipe for: {$product->nama} with {$inventory->nama_bahan}");
                
                // Create recipe
                $recipe = ProductRecipe::create([
                    'product_id' => $product->id,
                    'inventory_id' => $inventory->id,
                    'quantity_needed' => 10.0,
                    'unit' => 'gram',
                    'notes' => 'Test recipe from seeder'
                ]);
                
                $this->command->info('Recipe created successfully with ID: ' . $recipe->id);
            } else {
                $this->command->warn('Not enough data to create recipe');
            }
            
        } catch (\Exception $e) {
            $this->command->error('Error: ' . $e->getMessage());
            $this->command->error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
        }
    }
}
