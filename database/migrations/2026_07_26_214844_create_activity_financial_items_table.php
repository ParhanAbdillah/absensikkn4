<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_financial_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_financial_report_id')
                  ->constrained('activity_financial_reports')
                  ->onDelete('cascade')
                  ->name('fk_act_fin_items_report_id'); // Shorten FK name to prevent length issues
            $table->enum('type', ['income', 'expense']);
            $table->string('description');
            $table->integer('qty')->default(1);
            $table->decimal('price', 15, 2);
            $table->decimal('total', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_financial_items');
    }
};
