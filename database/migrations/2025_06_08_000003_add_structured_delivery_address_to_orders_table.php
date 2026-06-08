<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_city')->nullable()->after('delivery_address');
            $table->string('delivery_area')->nullable()->after('delivery_city');
            $table->string('delivery_street')->nullable()->after('delivery_area');
            $table->string('delivery_building')->nullable()->after('delivery_street');
            $table->string('delivery_floor')->nullable()->after('delivery_building');
            $table->string('delivery_apartment')->nullable()->after('delivery_floor');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_city',
                'delivery_area',
                'delivery_street',
                'delivery_building',
                'delivery_floor',
                'delivery_apartment',
            ]);
        });
    }
};
