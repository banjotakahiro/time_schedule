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
        Schema::create('information_shifts', function (Blueprint $table) {
            $table->id(); // シフトID (自動生成)
            $table->date('date'); // シフトの日付
            $table->time('start_time'); // 勤務開始時刻
            $table->time('end_time'); // 勤務終了時刻
            $table->string('location', 255)->nullable(); // 勤務場所 (nullable)
            $table->unsignedBigInteger('skill1')->nullable(); // 必要スキル1 (nullable)
            $table->integer('required_staff_skill1')->nullable(); // スキル1の必要人数 (nullable)
            $table->unsignedBigInteger('skill2')->nullable(); // 必要スキル2 (nullable)
            $table->integer('required_staff_skill2')->nullable(); // スキル2の必要人数 (nullable)
            $table->unsignedBigInteger('skill3')->nullable(); // 必要スキル3 (nullable)
            $table->integer('required_staff_skill3')->nullable(); // スキル3の必要人数 (nullable)

            // 外部キー制約 (roleテーブルを参照)
            $table->foreign('skill1')->references('id')->on('roles')->onDelete('set null');
            $table->foreign('skill2')->references('id')->on('roles')->onDelete('set null');
            $table->foreign('skill3')->references('id')->on('roles')->onDelete('set null');

            $table->timestamps(); // 作成日・更新日
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('information_shifts');
    }
};
