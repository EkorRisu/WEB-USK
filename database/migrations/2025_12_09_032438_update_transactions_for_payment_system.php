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
        Schema::table('transactions', function (Blueprint $table) {
            // Tambah kolom untuk sistem pembayaran baru
            if (!Schema::hasColumn('transactions', 'customer_name')) {
                $table->string('customer_name')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'bank_tujuan')) {
                $table->string('bank_tujuan')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'nama_pengirim')) {
                $table->string('nama_pengirim')->nullable();
            }
            
            // Hapus kolom yang tidak diperlukan
            if (Schema::hasColumn('transactions', 'order_type')) {
                $table->dropColumn('order_type');
            }
            if (Schema::hasColumn('transactions', 'table_number')) {
                $table->dropColumn('table_number');
            }
            if (Schema::hasColumn('transactions', 'alamat')) {
                $table->dropColumn('alamat');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Kembalikan kolom lama
            if (!Schema::hasColumn('transactions', 'order_type')) {
                $table->enum('order_type', ['takeaway', 'dine_in'])->default('takeaway');
            }
            if (!Schema::hasColumn('transactions', 'table_number')) {
                $table->string('table_number')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'alamat')) {
                $table->string('alamat')->nullable();
            }
            
            // Hapus kolom baru
            if (Schema::hasColumn('transactions', 'bank_tujuan')) {
                $table->dropColumn('bank_tujuan');
            }
            if (Schema::hasColumn('transactions', 'nama_pengirim')) {
                $table->dropColumn('nama_pengirim');
            }
        });
    }
};
