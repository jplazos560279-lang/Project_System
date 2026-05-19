<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // BEFORE INSERT validation trigger for attendances
        // Enforces basic data integrity rules at database level.
        // Note: Requires MySQL.

        DB::unprepared(<<<SQL
        DROP TRIGGER IF EXISTS attendance_before_insert_validate;
        SQL);

        DB::unprepared(<<<SQL
        CREATE TRIGGER attendance_after_insert_validate
        AFTER INSERT ON attendances
        FOR EACH ROW
        BEGIN

          IF NEW.time_out IS NOT NULL AND NEW.time_in > NEW.time_out THEN
            SIGNAL SQLSTATE '45000'
              SET MESSAGE_TEXT = 'Invalid attendance: time_in must be <= time_out.';
          END IF;

          IF NEW.late_minutes < 0 THEN
            SIGNAL SQLSTATE '45000'
              SET MESSAGE_TEXT = 'Invalid attendance: late_minutes cannot be negative.';
          END IF;

          IF NEW.overtime_hours < 0 THEN
            SIGNAL SQLSTATE '45000'
              SET MESSAGE_TEXT = 'Invalid attendance: overtime_hours cannot be negative.';
          END IF;

          IF NEW.has_excuse = TRUE AND NEW.status <> 'excused' THEN
            SIGNAL SQLSTATE '45000'
              SET MESSAGE_TEXT = 'Invalid attendance: when has_excuse is true, status must be excused.';
          END IF;

        END;
        SQL);
    }

    public function down(): void
    {
        DB::unprepared(<<<SQL
        DROP TRIGGER IF EXISTS attendance_before_insert_validate;
        SQL);
    }
};

