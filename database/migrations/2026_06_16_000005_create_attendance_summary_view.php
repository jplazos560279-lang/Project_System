<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // View for attendance summary: grouped attendance per employee per date.
        DB::unprepared(<<<SQL
        CREATE OR REPLACE VIEW attendance_summary AS
        SELECT
            a.attendance_id,
            a.emp_id,
            a.date as attendance_date,
            a.time_in,
            a.time_out,
            a.late_minutes,
            a.overtime_hours,
            a.status
        FROM attendances a;
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS attendance_summary;');
    }
};

