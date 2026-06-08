<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('preparing_at')->nullable()->after('confirmed_at');
            $table->timestamp('delivered_at')->nullable()->after('ready_at');
            $table->string('payment_method')->default('cash')->after('order_type');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['preparing_at', 'delivered_at', 'payment_method']);
        });
    }
};
