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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('nama_bahan');
            $table->text('deskripsi')->nullable();
            $table->decimal('stok_tersedia', 10, 2); // dalam kg, liter, atau unit
            $table->decimal('stok_minimum', 10, 2); // threshold untuk alert
            $table->string('satuan'); // kg, liter, gram, ml, pcs, dll
            $table->decimal('harga_per_satuan', 10, 2); // harga per satuan
            $table->enum('kategori_bahan', ['biji_kopi', 'susu', 'sirup', 'topping', 'kemasan', 'lainnya']);
            $table->enum('status', ['tersedia', 'menipis', 'habis'])->default('tersedia');
            $table->date('tanggal_kadaluarsa')->nullable();
            $table->string('supplier')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
