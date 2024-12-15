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
       Schema::create('confirmed_shifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->cascadeOnUpdate();  // 紐付け先が更新された場合の動作;
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade')->cascadeOnUpdate();  // 紐付け先が更新された場合の動作;
            $table->date('date')->comment('シフトの日付');
            $table->time('start_time')->comment('シフト開始時刻');
            $table->time('end_time')->comment('シフト終了時刻');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('confirmed_shifts');
    }
};
