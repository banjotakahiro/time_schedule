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
        Schema::table('confirmed_shifts', function (Blueprint $table) {
            $table->dropColumn('shift_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('confirmed_shifts', function (Blueprint $table) {
            $table->integer('shift_number')->nullable(); // 必要に応じて元の型に合わせて修正
        });
    }
};
