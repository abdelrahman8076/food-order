<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_areas', function (Blueprint $table) {
            $table->decimal('delivery_fee', 8, 2)->default(3.99)->after('is_active');
        });

        $defaultFee = DB::table('store_settings')->value('delivery_fee') ?? 3.99;
        DB::table('delivery_areas')->update(['delivery_fee' => $defaultFee]);
    }

    public function down(): void
    {
        Schema::table('delivery_areas', function (Blueprint $table) {
            $table->dropColumn('delivery_fee');
        });
    }
};
