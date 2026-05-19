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
        // Add missing columns expected by PayrollController/Payroll model/views.
        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'overtime_pay')) {
                $table->decimal('overtime_pay', 12, 2)->default(0)->after('basic_salary');
            }
            if (!Schema::hasColumn('payrolls', 'bonus')) {
                $table->decimal('bonus', 12, 2)->default(0)->after('overtime_pay');
            }

            if (!Schema::hasColumn('payrolls', 'late_deduction')) {
                $table->decimal('late_deduction', 12, 2)->default(0)->after('bonus');
            }
            if (!Schema::hasColumn('payrolls', 'absent_deduction')) {
                $table->decimal('absent_deduction', 12, 2)->default(0)->after('late_deduction');
            }
            if (!Schema::hasColumn('payrolls', 'tax_deduction')) {
                $table->decimal('tax_deduction', 12, 2)->default(0)->after('absent_deduction');
            }

            if (!Schema::hasColumn('payrolls', 'gross_salary')) {
                $table->decimal('gross_salary', 12, 2)->default(0)->after('tax_deduction');
            }
            if (!Schema::hasColumn('payrolls', 'total_deductions')) {
                $table->decimal('total_deductions', 12, 2)->default(0)->after('gross_salary');
            }

            if (!Schema::hasColumn('payrolls', 'sss_manual')) {
                $table->decimal('sss_manual', 12, 2)->default(0)->after('total_deductions');
            }
            if (!Schema::hasColumn('payrolls', 'philhealth_manual')) {
                $table->decimal('philhealth_manual', 12, 2)->default(0)->after('sss_manual');
            }
            if (!Schema::hasColumn('payrolls', 'pag_ibig_manual')) {
                $table->decimal('pag_ibig_manual', 12, 2)->default(0)->after('philhealth_manual');
            }
            if (!Schema::hasColumn('payrolls', 'tax_manual')) {
                $table->decimal('tax_manual', 12, 2)->default(0)->after('pag_ibig_manual');
            }

            if (!Schema::hasColumn('payrolls', 'payroll_month')) {
                $table->string('payroll_month')->nullable()->after('net_salary');
            }

            if (!Schema::hasColumn('payrolls', 'computation_details')) {
                $table->text('computation_details')->nullable()->after('payroll_month');
            }

            if (!Schema::hasColumn('payrolls', 'status')) {
                $table->string('status')->default('draft')->after('computation_details');
            }
            if (!Schema::hasColumn('payrolls', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            }
            if (!Schema::hasColumn('payrolls', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $columns = [
                'overtime_pay',
                'bonus',
                'late_deduction',
                'absent_deduction',
                'tax_deduction',
                'gross_salary',
                'total_deductions',
                'sss_manual',
                'philhealth_manual',
                'pag_ibig_manual',
                'tax_manual',
                'payroll_month',
                'computation_details',
                'status',
                'approved_by',
                'approved_at',
            ];

            foreach ($columns as $col) {
                if (Schema::hasColumn('payrolls', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

