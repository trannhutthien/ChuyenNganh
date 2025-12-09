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
        Schema::table('BaiHoc', function (Blueprint $table) {
            // Thêm các cột còn thiếu cho bài học
            $table->text('MoTa')->nullable()->after('TieuDe');
            $table->text('NoiDung')->nullable()->after('MoTa');
            $table->string('LoaiBaiHoc', 50)->default('video')->after('NoiDung');
            $table->integer('ThoiLuong')->default(0)->after('ThuTu');
            $table->string('VideoUrl', 500)->nullable()->after('ThoiLuong');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('BaiHoc', function (Blueprint $table) {
            $table->dropColumn(['MoTa', 'NoiDung', 'LoaiBaiHoc', 'ThoiLuong', 'VideoUrl']);
        });
    }
};
