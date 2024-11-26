<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('requested_shifts', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned()->nullable()->first(); // 一時的にNULL許可
        });

        // 既存データに連番を割り振る
        DB::statement('SET @count = 0;');
        DB::statement('UPDATE requested_shifts SET id = (@count := @count + 1);');

        // idカラムを主キーに設定
        Schema::table('requested_shifts', function (Blueprint $table) {
            $table->bigIncrements('id')->change(); // 自動インクリメントの主キーに変更
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requested_shifts', function (Blueprint $table) {
            //
        });
    }
};
