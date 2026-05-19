<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->enum('status', ['present', 'late', 'absent', 'excused'])->nullable()->after('time_out');
            $table->integer('late_minutes')->default(0)->after('status');
            $table->decimal('overtime_hours', 5, 2)->default(0)->after('late_minutes');
            $table->enum('overtime_type', ['regular', 'rest_day', 'holiday'])->nullable()->after('overtime_hours');
            $table->boolean('has_excuse')->default(false)->after('overtime_type');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['status', 'late_minutes', 'overtime_hours', 'overtime_type', 'has_excuse']);
        });
    }
};
