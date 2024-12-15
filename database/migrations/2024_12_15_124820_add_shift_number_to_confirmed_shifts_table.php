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
            // shift_numberカラムを追加
            $table->integer('shift_number')
                ->default(0)
                ->after('end_time')
                ->comment('シフト番号'); // end_timeの後に追加
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('confirmed_shifts', function (Blueprint $table) {
            // ロールバック時にshift_numberカラムを削除
            $table->dropColumn('shift_number');
        });
    }
};
