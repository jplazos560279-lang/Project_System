<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Employee;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('email_hash', 64)->nullable()->index()->after('email');
        });

        // Populate email_hash for all existing employees
        foreach (Employee::all() as $employee) {
            $employee->email_hash = hash('sha256', strtolower($employee->email));
            $employee->saveQuietly();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex(['email_hash']);
            $table->dropColumn('email_hash');
        });
    }
};
