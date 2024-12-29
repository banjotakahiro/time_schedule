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
            // max_shiftsの次にpriorityカラムを追加 
            $table->integer('priority')->nullable()->after('max_shifts');
            // max_shiftsの次にroleカラムを追加し、rolesテーブルと紐付ける 
            $table->foreignId('role')
                ->nullable()
                ->after('priority')
                ->constrained('roles') // 外部キーをrolesテーブルに設定 
                ->cascadeOnUpdate() // 参照先が更新された場合、このカラムも更新 
                ->cascadeOnDelete(); // 参照先が削除された場合、この行も削除 
        });
    }
    /** 
     * Reverse the migrations. 
     */
    public function down(): void
    {
        Schema::table('shift_constraints', function (Blueprint $table) {
            // 外部キー制約を削除してからカラムを削除 
            $table->dropForeign(['role']);
            $table->dropColumn('role');
            // priorityカラムを削除 
            $table->dropColumn('priority');
        });
    }
};
