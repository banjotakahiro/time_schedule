<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('shift_constraints', function (Blueprint $table) {
            $table->id(); // 主キー
            $table->string('month', 7); // 制約が適用される月 (例: '2024-12')
            $table->date('date')->nullable(); // 制約が適用される具体的な日付 (null可)
            $table->unsignedBigInteger('user_id')->nullable(); // 関連するユーザーID (null可)
            $table->enum('status', [
                'day_off',          // ユーザーが休みの日
                'mandatory_shift',  // ユーザーを必ず出勤させる制約
                'pairing',          // ユーザー同士のペアリング制約
                'shift_limit'       // ユーザーのシフト回数制限
            ]); // 制約の種類
            $table->unsignedBigInteger('paired_user_id')->nullable(); // ペアリング対象のユーザーID (null可)
            $table->integer('max_shifts')->nullable(); // シフトの最大回数制限 (null可)
            $table->json('extra_info')->nullable(); // その他の情報を保存するJSONカラム
            $table->timestamps(); // 作成日時・更新日時
        });

        // 外部キー制約を追加 (必要に応じて)
        Schema::table('shift_constraints', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('paired_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shift_constraints');
    }
};
