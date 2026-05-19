<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->decimal('basic_salary', 12, 2)->nullable()->after('position_id');
            $table->decimal('daily_rate', 10, 2)->nullable()->after('basic_salary');
            $table->decimal('hourly_rate', 10, 2)->nullable()->after('daily_rate');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['basic_salary', 'daily_rate', 'hourly_rate']);
        });
    }
};
