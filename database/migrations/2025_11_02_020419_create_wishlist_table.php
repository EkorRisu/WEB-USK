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
        Schema::create('wishlist', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke pengguna
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Relasi ke produk (buku)
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            
            $table->timestamps(); // Berguna untuk tau kapan dia menambahkannya

            // PENTING: Satu pengguna hanya bisa memasukkan satu produk yang sama ke wishlist
            $table->unique(['user_id', 'produk_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlist');
    }
};