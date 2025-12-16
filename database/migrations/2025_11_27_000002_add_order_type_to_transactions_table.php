<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'order_type')) {
                $table->enum('order_type', ['takeaway', 'dine_in'])->default('takeaway');
            }
            if (!Schema::hasColumn('transactions', 'table_number')) {
                $table->string('table_number')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'customer_name')) {
                $table->string('customer_name')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'order_type')) {
                $table->dropColumn('order_type');
            }
            if (Schema::hasColumn('transactions', 'table_number')) {
                $table->dropColumn('table_number');
            }
            if (Schema::hasColumn('transactions', 'customer_name')) {
                $table->dropColumn('customer_name');
            }
        });
    }
};
