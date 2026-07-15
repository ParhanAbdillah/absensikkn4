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
        Schema::table('attendances', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['schedule_id']);
            // Make schedule_id nullable
            $table->unsignedBigInteger('schedule_id')->nullable()->change();
            // Re-add foreign key constraint
            $table->foreign('schedule_id')->references('id')->on('schedules')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['schedule_id']);
            $table->unsignedBigInteger('schedule_id')->change();
            $table->foreign('schedule_id')->references('id')->on('schedules')->cascadeOnDelete();
        });
    }
};
