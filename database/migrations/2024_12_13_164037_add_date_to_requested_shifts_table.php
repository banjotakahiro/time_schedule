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
        Schema::table('requested_shifts', function (Blueprint $table) {
            $table->date('date')->after('user_id')->nullable()->comment('シフトの日付');
            $table->dropColumn('title'); // titleカラムを削除
            $table->dropColumn('body');  // bodyカラムを削除
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requested_shifts', function (Blueprint $table) {
            $table->dropColumn('date'); // dateカラムを削除
            $table->string('title')->nullable()->comment('削除したtitleカラムを再追加');
            $table->string('body')->nullable()->comment('削除したbodyカラムを再追加');
        });
    }
};
