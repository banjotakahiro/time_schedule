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
            $table->dropColumn('month'); // monthカラムを削除
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shift_constraints', function (Blueprint $table) {
            $table->integer('month')->nullable(); // monthカラムを復元（nullableに設定）
        });
    }
};
