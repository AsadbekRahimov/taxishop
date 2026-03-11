<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_type', 20)->default('pickup')->after('order_number');
            $table->string('customer_name')->nullable()->change();
            $table->string('customer_phone', 20)->nullable()->change();
        });

        // Update status enum: add 'confirmed' status
        // SQLite doesn't support modifying enums, so we handle it via the model
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('order_type');
            $table->string('customer_name')->nullable(false)->change();
            $table->string('customer_phone', 20)->nullable(false)->change();
        });
    }
};
