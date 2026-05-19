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
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['emp_id']);
            $table->foreign('emp_id')->references('emp_id')->on('employees')->onDelete('cascade');
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeign(['emp_id']);
            $table->foreign('emp_id')->references('emp_id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['emp_id']);
            $table->foreign('emp_id')->references('emp_id')->on('employees');
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeign(['emp_id']);
            $table->foreign('emp_id')->references('emp_id')->on('employees');
        });
    }
};
