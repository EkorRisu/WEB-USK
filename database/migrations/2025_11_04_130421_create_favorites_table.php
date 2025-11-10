<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            // Kunci asing ke tabel 'users'
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Kunci asing ke tabel 'produks'
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->timestamps();

            // User hanya bisa like satu buku satu kali
            $table->unique(['user_id', 'produk_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};