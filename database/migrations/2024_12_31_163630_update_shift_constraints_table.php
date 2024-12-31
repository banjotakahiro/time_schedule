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
        Schema::table('shift_constraints', function (Blueprint $table) {
            $table->date('date')->nullable(false)->change();
            // カラムの名前を変更
            $table->renameColumn('date', 'start_date');

            // 新しいカラムを start_date の次に追加
            $table->date('end_date')->nullable(false)->after('date');

            // start_date を必須に設定
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_constraints', function (Blueprint $table) {
            // start_date の必須設定を解除（nullableに変更）
            $table->date('start_date')->nullable()->change();

            // end_date カラムを削除
            $table->dropColumn('end_date');

            // カラム名を元に戻す
            $table->renameColumn('start_date', 'date');
        });
    }
};
