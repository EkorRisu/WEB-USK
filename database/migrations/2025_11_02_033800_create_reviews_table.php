<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            
            // ⭐️ KUNCI BARU: Terhubung ke item transaksi spesifik
            $table->foreignId('transaction_item_id')
                  ->constrained('transaction_items')
                  ->onDelete('cascade');
            
            // Data ulasan
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('review_text')->nullable();
            
            $table->timestamps();

            // ⭐️ PENTING: Satu item transaksi hanya bisa punya satu review
            $table->unique('transaction_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};