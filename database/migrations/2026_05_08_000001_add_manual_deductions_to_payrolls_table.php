<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to add manual deduction fields and bonus field
     */
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Add bonus field
            if (!Schema::hasColumn('payrolls', 'bonus')) {
                $table->decimal('bonus', 12, 2)->default(0)->after('overtime_pay');
            }

            // Add manual deduction fields
            if (!Schema::hasColumn('payrolls', 'sss_manual')) {
                $table->decimal('sss_manual', 12, 2)->default(0)->after('gross_salary');
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

            // Add status and approval fields
            if (!Schema::hasColumn('payrolls', 'status')) {
                $table->string('status')->default('draft')->after('computation_details'); // draft, approved, paid
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
                'bonus',
                'sss_manual',
                'philhealth_manual',
                'pag_ibig_manual',
                'tax_manual',
                'status',
                'approved_by',
                'approved_at'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('payrolls', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
