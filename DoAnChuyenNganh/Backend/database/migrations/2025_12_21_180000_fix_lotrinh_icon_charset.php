<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Sửa cột Icon để hỗ trợ emoji (utf8mb4)
     */
    public function up(): void
    {
        // Sửa charset của cột Icon để hỗ trợ emoji
        DB::statement('ALTER TABLE `LoTrinh` MODIFY `Icon` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `LoTrinh` MODIFY `Icon` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL');
    }
};
