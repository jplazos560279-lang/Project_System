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
        Schema::create('payroll_deductions', function (Blueprint $table) {
            $table->id('deduction_id');
            $table->foreignId('payroll_id')->constrained('payrolls', 'payroll_id')->cascadeOnDelete();
            $table->string('deduction_type'); // sss, philhealth, pag_ibig, tax, loan, penalty, other
            $table->string('deduction_name');
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('payroll_id');
            $table->index('deduction_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_deductions');
    }
};
