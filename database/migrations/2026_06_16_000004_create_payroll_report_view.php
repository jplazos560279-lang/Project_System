<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // View for payroll report: payrolls + employee names.
        // Useful for phpMyAdmin (Views) and simplified reporting.
        DB::unprepared(<<<SQL
        CREATE OR REPLACE VIEW payroll_report AS
        SELECT
            p.payroll_id,
            p.emp_id,
            e.first_name,
            e.last_name,
            p.payroll_month,
            p.basic_salary,
            p.gross_salary,
            p.total_deductions,
            p.net_salary,
            p.overtime_pay,
            p.status,
            p.created_at,
            p.updated_at
        FROM payrolls p
        INNER JOIN employees e ON e.emp_id = p.emp_id;
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS payroll_report;');
    }
};

