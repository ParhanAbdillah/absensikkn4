<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_reports', function (Blueprint $table) {
            $table->string('person_in_charge')->nullable()->after('deadline');
        });
    }

    public function down(): void
    {
        Schema::table('activity_reports', function (Blueprint $table) {
            $table->dropColumn('person_in_charge');
        });
    }
};
