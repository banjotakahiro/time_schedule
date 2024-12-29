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
        Schema::table('shift_constraints', function (Blueprint $table) {
            // 外部キー制約を削除
            // カラムを削除
            $table->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_constraints', function (Blueprint $table) {
            // カラムを再追加
            $table->unsignedBigInteger('role')->nullable();
            // 外部キー制約を再追加
        });
    }
};
