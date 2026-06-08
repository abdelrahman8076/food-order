<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('customer_phone_normalized', 32)->nullable()->after('customer_phone');
            $table->index('customer_phone_normalized');
        });

        Order::query()->each(function (Order $order) {
            $order->update([
                'customer_phone_normalized' => Order::normalizePhone($order->customer_phone),
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['customer_phone_normalized']);
            $table->dropColumn('customer_phone_normalized');
        });
    }
};
