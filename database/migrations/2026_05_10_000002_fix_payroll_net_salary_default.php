<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Some existing DBs are missing default values for computed columns.
        // Make net_salary and total_deductions default to 0 so insert works.
        Schema::table('payrolls', function (Blueprint $table) {
            if (Schema::hasColumn('payrolls', 'net_salary')) {
                $table->decimal('net_salary', 12, 2)->default(0)->change();
            }
            if (Schema::hasColumn('payrolls', 'total_deductions')) {
                $table->decimal('total_deductions', 12, 2)->default(0)->change();
            }
        });
    }

    public function down(): void
    {
        // Best-effort rollback: remove defaults (may not be supported depending on MySQL mode).
        Schema::table('payrolls', function (Blueprint $table) {
            if (Schema::hasColumn('payrolls', 'net_salary')) {
                $table->decimal('net_salary', 12, 2)->change();
            }
            if (Schema::hasColumn('payrolls', 'total_deductions')) {
                $table->decimal('total_deductions', 12, 2)->change();
            }
        });
    }
};

