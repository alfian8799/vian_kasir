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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['now', 'processing', 'completed', 'cancelled'])->default('now')->after('total_price');
            $table->integer('discount')->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('total_payment', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
   public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
