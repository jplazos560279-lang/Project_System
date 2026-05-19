<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Remove old deductions column and add detailed fields
            $table->decimal('overtime_pay', 12, 2)->nullable()->after('basic_salary');
            $table->decimal('late_deduction', 10, 2)->default(0)->after('overtime_pay');
            $table->decimal('absent_deduction', 10, 2)->default(0)->after('late_deduction');
            $table->decimal('tax_deduction', 10, 2)->default(0)->after('absent_deduction');
            $table->decimal('gross_salary', 12, 2)->nullable()->after('tax_deduction');
            $table->decimal('total_deductions', 12, 2)->default(0)->after('gross_salary');
            $table->string('payroll_month')->nullable()->after('total_deductions');
            $table->text('computation_details')->nullable()->after('payroll_month');
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'overtime_pay', 'late_deduction', 'absent_deduction',
                'tax_deduction', 'gross_salary',
                'total_deductions', 'payroll_month', 'computation_details'
            ]);
        });
    }
};
