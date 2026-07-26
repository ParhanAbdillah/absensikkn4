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
        Schema::create('activity_proposals', function (Blueprint $table) {
            $table->id();
            $table->string('activity_name');
            $table->date('date');
            $table->decimal('budget_requested', 15, 2);
            $table->decimal('budget_approved', 15, 2)->default(0);
            $table->text('description');
            $table->string('file_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->boolean('is_posted_to_cash')->default(false);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_proposals');
    }
};
