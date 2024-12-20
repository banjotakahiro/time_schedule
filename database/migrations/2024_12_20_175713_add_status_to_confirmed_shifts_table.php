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
        Schema::table('confirmed_shifts', function (Blueprint $table) {
            // statusカラムを追加（例: varchar型で最大255文字）
            $table->string('status', 255)->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('confirmed_shifts', function (Blueprint $table) {
            // statusカラムを削除
            $table->dropColumn('status');
        });
    }
};
