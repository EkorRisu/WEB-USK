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
        // Kita gunakan Schema::table() karena 'mengubah' tabel yang sudah ada
        Schema::table('transactions', function (Blueprint $table) {
            
            // Menambahkan kolom pesan_admin
            // 'text' agar bisa panjang, 'nullable' berarti boleh kosong
            $table->text('pesan_admin')->nullable()->after('status');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Ini untuk 'rollback', jika kita ingin membatalkan migrasi
            $table->dropColumn('pesan_admin');
        });
    }
};