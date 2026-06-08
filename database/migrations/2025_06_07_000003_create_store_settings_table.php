<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('EATSHUB');
            $table->string('store_phone')->nullable();
            $table->text('store_address')->nullable();
            $table->string('store_hours')->nullable();
            $table->decimal('tax_rate', 5, 4)->default(0.10);
            $table->decimal('delivery_fee', 8, 2)->default(3.99);
            $table->decimal('free_delivery_min', 8, 2)->default(25.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
