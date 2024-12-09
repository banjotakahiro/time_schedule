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
            $table->date('date')->unique(); // シフトの日付
            $table->time('start_time'); // 勤務開始時刻
            $table->time('end_time'); // 勤務終了時刻
            $table->string('location', 255)->nullable(); // 勤務場所 (nullable)
            $table->string('color', 7)->default('#ffffff'); // カレンダーの着色用（デフォルトは白）
            $table->unsignedBigInteger('role1')->nullable(); // 必要スキル1 (nullable)
            $table->integer('required_staff_role1')->nullable(); // スキル1の必要人数 (nullable)
            $table->unsignedBigInteger('role2')->nullable(); // 必要スキル2 (nullable)
            $table->integer('required_staff_role2')->nullable(); // スキル2の必要人数 (nullable)
            $table->unsignedBigInteger('role3')->nullable(); // 必要スキル3 (nullable)
            $table->integer('required_staff_role3')->nullable(); // スキル3の必要人数 (nullable)

            // 外部キー制約 (roleテーブルを参照)
            $table->foreign('role1')->references('id')->on('roles')->onDelete('set null');
            $table->foreign('role2')->references('id')->on('roles')->onDelete('set null');
            $table->foreign('role3')->references('id')->on('roles')->onDelete('set null');

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
