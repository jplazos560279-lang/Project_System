<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // View for leave request details: leave_requests + employees.
        // This view is useful for phpMyAdmin (Views section) and can simplify UI queries.

        DB::unprepared(<<<SQL
        CREATE OR REPLACE VIEW leave_request_details AS
        SELECT
            lr.leave_id,
            lr.emp_id,
            e.emp_id AS employee_id,
            e.first_name,
            e.last_name,
            lr.leave_type,
            lr.reason,
            lr.start_date,
            lr.end_date,
            lr.total_days,
            lr.status,
            lr.admin_notes,
            lr.reviewed_by,
            lr.reviewed_at,
            lr.created_at,
            lr.updated_at
        FROM leave_requests lr
        INNER JOIN employees e ON e.emp_id = lr.emp_id;
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS leave_request_details;');
    }
};

