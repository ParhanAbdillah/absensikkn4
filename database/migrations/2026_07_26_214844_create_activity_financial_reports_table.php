<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_financial_reports', function (Blueprint $table) {
            $table->id();
            $table->string('activity_name');
            $table->date('date');
            $table->string('city')->default('Tasikmalaya');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_financial_reports');
    }
};
