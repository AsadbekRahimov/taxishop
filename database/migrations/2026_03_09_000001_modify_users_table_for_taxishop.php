<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email_verified_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('email', 'login');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('login', 100)->change();
            $table->string('phone', 20)->nullable()->after('password');
            $table->enum('role', ['admin', 'driver'])->default('admin')->after('phone');
            $table->string('car_number', 20)->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('car_number');
        });

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->renameColumn('email', 'login');
        });
    }

    public function down(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->renameColumn('login', 'email');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role', 'car_number', 'is_active']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('login', 'email');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->change();
            $table->timestamp('email_verified_at')->nullable()->after('email');
        });
    }
};
