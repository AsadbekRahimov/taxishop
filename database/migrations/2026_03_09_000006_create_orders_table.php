<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique();
            $table->foreignId('driver_id')->constrained('users')->restrictOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone', 20);
            $table->text('delivery_address')->nullable();
            $table->enum('payment_method', ['cash', 'qr', 'delivery']);
            $table->enum('status', ['new', 'paid', 'delivered', 'cancelled'])->default('new');
            $table->decimal('total', 12, 2);
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
