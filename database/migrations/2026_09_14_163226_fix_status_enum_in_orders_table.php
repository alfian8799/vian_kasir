<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix typo: ENUM status 'now' → 'new'
     */
    public function up(): void
    {
        // Step 1: Ubah ke VARCHAR sementara agar bisa update bebas
        DB::statement("ALTER TABLE `orders` MODIFY `status` VARCHAR(20) NOT NULL DEFAULT 'new'");

        // Step 2: Update data lama 'now' → 'new'
        DB::table('orders')->where('status', 'now')->update(['status' => 'new']);

        // Step 3: Ubah kembali ke ENUM dengan nilai yang benar
        DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('new','processing','completed','cancelled') NOT NULL DEFAULT 'new'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `orders` MODIFY `status` VARCHAR(20) NOT NULL DEFAULT 'now'");
        DB::table('orders')->where('status', 'new')->update(['status' => 'now']);
        DB::statement("ALTER TABLE `orders` MODIFY `status` ENUM('now','processing','completed','cancelled') NOT NULL DEFAULT 'now'");
    }
};
