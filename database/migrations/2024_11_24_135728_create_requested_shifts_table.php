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
        Schema::create('requested_shifts', function (Blueprint $table) {
            $table->id(); // PK
            $table->foreignId('user_id')
                ->constrained() // References the company_memberships table automatically
                ->cascadeOnUpdate() // Update cascade
                ->cascadeOnDelete(); // Delete cascade
            $table->date('work_date'); // Work date
            $table->time('start_time'); // Start time
            $table->time('end_time'); // End time
            $table->text('notes')->nullable(); // Notes, nullable
            $table->timestamps(); // Created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requested_shifts');
    }
};
