<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // AFTER INSERT validation trigger for leave_requests
        // Enforces basic date integrity and prevents invalid leave records.

        DB::unprepared(<<<SQL
        DROP TRIGGER IF EXISTS leave_request_after_insert_validate;
        SQL);

        DB::unprepared(<<<SQL
        CREATE TRIGGER leave_request_after_insert_validate
        AFTER INSERT ON leave_requests
        FOR EACH ROW
        BEGIN
            -- start_date must be <= end_date
            IF NEW.start_date IS NOT NULL AND NEW.end_date IS NOT NULL
               AND NEW.start_date > NEW.end_date THEN
                SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Invalid leave request: start_date must be <= end_date.';
            END IF;

            -- total_days cannot be negative
            IF NEW.total_days < 0 THEN
                SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Invalid leave request: total_days cannot be negative.';
            END IF;

            -- status should be a known value when provided
            IF NEW.status IS NOT NULL
               AND NEW.status NOT IN ('pending', 'approved', 'rejected') THEN
                SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Invalid leave request: status must be pending, approved, or rejected.';
            END IF;

        END;
        SQL);
    }

    public function down(): void
    {
        DB::unprepared(<<<SQL
        DROP TRIGGER IF EXISTS leave_request_after_insert_validate;
        SQL);
    }
};

