<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('produks')->onDelete('cascade');
            $table->foreignId('inventory_id')->constrained('inventories')->onDelete('cascade');
            $table->decimal('quantity_needed', 8, 2); // Jumlah bahan yang dibutuhkan
            $table->string('unit'); // Satuan (gram, ml, pcs, etc)
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamps();
            
            // Kombinasi product_id dan inventory_id harus unik
            $table->unique(['product_id', 'inventory_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_recipes');
    }
};
