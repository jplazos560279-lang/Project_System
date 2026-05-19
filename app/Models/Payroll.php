<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payroll extends Model
{
    protected $primaryKey = 'payroll_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'emp_id',
        'basic_salary',
        'overtime_pay',
        'bonus',
        'late_deduction',
        'absent_deduction',
        'tax_deduction',
        'sss_manual',
        'philhealth_manual',
        'pag_ibig_manual',
        'tax_manual',
        'gross_salary',
        'total_deductions',
        'net_salary',
        'pay_date',
        'payroll_month',
        'computation_details',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'pay_date' => 'date',
        'approved_at' => 'datetime',
        'bonus' => 'decimal:2',
        'sss_manual' => 'decimal:2',
        'philhealth_manual' => 'decimal:2',
        'pag_ibig_manual' => 'decimal:2',
        'tax_manual' => 'decimal:2',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAID = 'paid';

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'emp_id');
    }

    public function deductions(): HasMany
    {
        return $this->hasMany(PayrollDeduction::class, 'payroll_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Calculate gross salary = basic + overtime + bonus
     */
    public function calculateGrossSalary(): float
    {
        return ($this->basic_salary ?? 0) + ($this->overtime_pay ?? 0) + ($this->bonus ?? 0);
    }

    /**
     * Calculate total manual deductions
     */
    public function calculateManualDeductions(): float
    {
        // Sum from payroll_deductions table
        $deductionsFromTable = $this->deductions()->sum('amount');
        
        // Sum from manual fields in payroll table
        $manualDeductions = ($this->sss_manual ?? 0) + 
                           ($this->philhealth_manual ?? 0) + 
                           ($this->pag_ibig_manual ?? 0) + 
                           ($this->tax_manual ?? 0);
        
        return $deductionsFromTable + $manualDeductions;
    }

    /**
     * Calculate total deductions (automatic + manual)
     */
    public function calculateTotalDeductions(): float
    {
        $automatic = ($this->late_deduction ?? 0) + ($this->absent_deduction ?? 0) + ($this->tax_deduction ?? 0);
        $manual = $this->calculateManualDeductions();
        return $automatic + $manual;
    }

    /**
     * Calculate net salary = gross - total deductions
     */
    public function calculateNetSalary(): float
    {
        return $this->calculateGrossSalary() - $this->calculateTotalDeductions();
    }

    /**
     * Recalculate and update payroll totals
     */
    public function recalculateTotals(): void
    {
        $this->gross_salary = $this->calculateGrossSalary();
        $this->total_deductions = $this->calculateTotalDeductions();
        $this->net_salary = $this->calculateNetSalary();
        $this->save();
    }

    /**
     * Calculate payroll from attendance records
     */
    public static function computeFromAttendance(
        Employee $employee,
        int $year,
        int $month
    ): array {
        $startDate = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Get attendance records for the month
        $attendances = $employee->attendances()
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        // Get approved leave requests for the period
        $approvedLeaves = \App\Models\LeaveRequest::where('emp_id', $employee->emp_id)
            ->where('status', 'approved')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate]);
            })->get();

        // Calculate working days in month (excluding weekends)
        $workingDays = 0;
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            if (!$current->isWeekend()) {
                $workingDays++;
            }
            $current->addDay();
        }

        // Calculate totals
        $presentDays = 0;
        $lateMinutes = 0;
        $overtimeHours = 0;
        $absentDays = 0;

        foreach ($attendances as $attendance) {
            // Skip if has approved leave
            if ($approvedLeaves->some(fn($leave) => $leave->coversDate($attendance->date))) {
                continue;
            }

            switch ($attendance->status) {
                case 'present':
                case 'late':
                    $presentDays++;
                    break;
                case 'absent':
                    $absentDays++;
                    break;
            }


        $lateMinutes += ($attendance->late_minutes ?? 0) * (!$attendance->has_excuse ? 1 : 0);

            $overtimeHours += $attendance->overtime_hours ?? 0;
        }

        // Deduct absent days from working days for approved leaves
        foreach ($approvedLeaves as $leave) {
            $leaveStart = $leave->start_date->max($startDate);
            $leaveEnd = $leave->end_date->min($endDate);
            $days = $leaveStart->diffInDays($leaveEnd) + 1;
            // Subtract weekend days
            for ($d = $leaveStart; $d->lte($leaveEnd); $d->addDay()) {
                if ($d->isWeekend()) {
                    $days--;
                }
            }
            $absentDays = max(0, $absentDays - $days);
        }

        $basicSalary = $employee->basic_salary ?? 0;
        $dailyRate = $employee->daily_rate ?? ($basicSalary / 22);
        $hourlyRate = $employee->hourly_rate ?? ($dailyRate / 8);

        // Calculate late deduction
        $minuteRate = $hourlyRate / 60;
        $lateDeduction = $lateMinutes * $minuteRate;

        // Calculate absent deduction
        $absentDaysInMonth = $workingDays - $presentDays;
        $absentDeduction = $absentDaysInMonth * $dailyRate;

        // Calculate overtime pay
        $overtimeMultiplier = match ($attendances->first()?->overtime_type) {
            'rest_day' => Employee::OT_REST_DAY,
            'holiday' => Employee::OT_HOLIDAY,
            default => Employee::OT_REGULAR,
        };
        $overtimePay = $overtimeHours * $hourlyRate * $overtimeMultiplier;

        // Calculate tax deduction
        $taxDeduction = $employee->calculateTax();

        // Calculate totals
        $grossSalary = $basicSalary + $overtimePay;
        $totalDeductions = $lateDeduction + $absentDeduction + $taxDeduction;
        $netSalary = $grossSalary - $totalDeductions;

        // Build computation details
        $details = sprintf(
            "Present: %d days | Late: %d min | Overtime: %.2f hrs | Absent: %d days\n" .
            "Basic: %.2f | OT Pay: %.2f | Late Ded: %.2f | Absent Ded: %.2f\n" .
"Tax: %.2f",
            $presentDays,
            $lateMinutes,
            $overtimeHours,
            $absentDaysInMonth,
            $basicSalary,
            $overtimePay,
            $lateDeduction,
            $absentDeduction,
            $taxDeduction
        );

        return [
            'basic_salary' => $basicSalary,
            'overtime_pay' => $overtimePay,
            'late_deduction' => $lateDeduction,
            'absent_deduction' => $absentDeduction,
'sss_deduction' => 0,
'philhealth_deduction' => 0,
'pagibig_deduction' => 0,
'tax_deduction' => $taxDeduction,
            'other_deductions' => 0,
            'gross_salary' => $grossSalary,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
            'payroll_month' => sprintf('%04d-%02d', $year, $month),
            'computation_details' => $details,
        ];
    }
}

