<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // AFTER INSERT validation trigger for payrolls
        // Prevent invalid numeric values from being stored.

        DB::unprepared(<<<SQL
        DROP TRIGGER IF EXISTS payroll_after_insert_validate;
        SQL);

        DB::unprepared(<<<SQL
        CREATE TRIGGER payroll_after_insert_validate
        AFTER INSERT ON payrolls
        FOR EACH ROW
        BEGIN
            -- net_salary cannot be negative
            IF NEW.net_salary < 0 THEN
                SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Invalid payroll: net_salary cannot be negative.';
            END IF;

            -- total_deductions cannot be negative
            IF NEW.total_deductions < 0 THEN
                SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Invalid payroll: total_deductions cannot be negative.';
            END IF;

            -- basic_salary cannot be negative
            IF NEW.basic_salary < 0 THEN
                SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Invalid payroll: basic_salary cannot be negative.';
            END IF;

            -- gross_salary (if provided) cannot be negative
            IF NEW.gross_salary IS NOT NULL AND NEW.gross_salary < 0 THEN
                SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Invalid payroll: gross_salary cannot be negative.';
            END IF;

            -- payroll_month should not be empty when present (basic validation)
            IF NEW.payroll_month IS NOT NULL AND CHAR_LENGTH(NEW.payroll_month) = 0 THEN
                SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Invalid payroll: payroll_month cannot be empty.';
            END IF;
        END;
        SQL);
    }

    public function down(): void
    {
        DB::unprepared(<<<SQL
        DROP TRIGGER IF EXISTS payroll_after_insert_validate;
        SQL);
    }
};

