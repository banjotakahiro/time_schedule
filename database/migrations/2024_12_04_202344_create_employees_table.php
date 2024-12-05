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
        Schema::create('employees', function (Blueprint $table) {
            $table->id(); // 従業員ID (Primary Key)

            // user_id を外部キーとして設定
            $table->foreignId('user_id')  // 外部キー用のカラム
                ->constrained()          // 紐付け先が "users" テーブルの場合 (デフォルト)
                ->cascadeOnDelete()      // 紐付け先が削除されたら従業員情報も削除
                ->cascadeOnUpdate();     // 紐付け先が更新された場合の動作

            // skill1 を外部キーとして設定
            $table->foreignId('skill1')->nullable()  // 外部キー用のカラム (NULL 許可)
                ->constrained('roles')              // 紐付け先が "roles" テーブル
                ->nullOnDelete();                   // 紐付け先が削除されたら NULL に設定

            // skill2 を外部キーとして設定
            $table->foreignId('skill2')->nullable()
                ->constrained('roles')
                ->nullOnDelete();

            // skill3 を外部キーとして設定
            $table->foreignId('skill3')->nullable()
                ->constrained('roles')
                ->nullOnDelete();

            $table->text('notes')->nullable(); // 従業員に関する自由記述の情報
            $table->timestamps(); // 作成日時・更新日時
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
