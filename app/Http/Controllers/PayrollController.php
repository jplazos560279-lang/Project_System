<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\PayrollDeduction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    /**
     * Display payroll list with search
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $month = $request->get('month');

        $query = Payroll::with('employee');

        if (!auth()->user()->isAdmin()) {
            $employee = auth()->user()->employee;
            if ($employee) {
                $query->where('emp_id', $employee->emp_id);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('basic_salary', 'like', "%{$search}%")
                    ->orWhere('net_salary', 'like', "%{$search}%")
                    ->orWhere('payroll_month', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($eq) use ($search) {
                        $eq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($month) {
            $query->where('payroll_month', $month);
        }

        $payrolls = $query->latest()->paginate(10)->appends($request->query());

        return view('payrolls.index', compact('payrolls', 'search', 'month'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $employees = Employee::all();
        $deductionTypes = PayrollDeduction::getDeductionTypes();
        return view('payrolls.create', compact('employees', 'deductionTypes'));
    }

    /**
     * Store manual payroll with government deductions
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'emp_id' => 'required|exists:employees,emp_id',
            'basic_salary' => 'required|numeric|min:0',
            'overtime_pay' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'late_deduction' => 'nullable|numeric|min:0',
            'absent_deduction' => 'nullable|numeric|min:0',
            'tax_deduction' => 'nullable|numeric|min:0',
            'sss_manual' => 'nullable|numeric|min:0',
            'philhealth_manual' => 'nullable|numeric|min:0',
            'pag_ibig_manual' => 'nullable|numeric|min:0',
            'tax_manual' => 'nullable|numeric|min:0',
            'pay_date' => 'required|date',
            'payroll_month' => 'nullable|string',

            // Additional deductions from payroll_deductions table
            'additional_deductions' => 'nullable|array',
            'additional_deductions.*.type' => 'required_with:additional_deductions|string',
            'additional_deductions.*.name' => 'required_with:additional_deductions|string',
            'additional_deductions.*.amount' => 'required_with:additional_deductions|numeric|min:0',
            'additional_deductions.*.description' => 'nullable|string',
        ]);

        // Set default values for numeric fields
        $validated['overtime_pay'] = $validated['overtime_pay'] ?? 0;
        $validated['bonus'] = $validated['bonus'] ?? 0;
        $validated['late_deduction'] = $validated['late_deduction'] ?? 0;
        $validated['absent_deduction'] = $validated['absent_deduction'] ?? 0;
        $validated['tax_deduction'] = $validated['tax_deduction'] ?? 0;
        $validated['sss_manual'] = $validated['sss_manual'] ?? 0;
        $validated['philhealth_manual'] = $validated['philhealth_manual'] ?? 0;
        $validated['pag_ibig_manual'] = $validated['pag_ibig_manual'] ?? 0;
        $validated['tax_manual'] = $validated['tax_manual'] ?? 0;

        // Set payroll month if not provided
        // Note: $validated['pay_date'] is a string (Y-m-d) because validation uses 'date'
        // so we must format it as a Carbon instance.
        if (empty($validated['payroll_month'])) {
            $validated['payroll_month'] = Carbon::parse($validated['pay_date'])->format('Y-m');
        }

        // Set initial status
        $validated['status'] = Payroll::STATUS_DRAFT;

        // Create payroll
        $payroll = Payroll::create($validated);

        // Save additional deductions
        if (!empty($request->input('additional_deductions'))) {
            foreach ($request->input('additional_deductions') as $deduction) {
                if (($deduction['amount'] ?? 0) > 0) {
                    PayrollDeduction::create([
                        'payroll_id' => $payroll->payroll_id,
                        'deduction_type' => $deduction['type'],
                        'deduction_name' => $deduction['name'],
                        'amount' => $deduction['amount'],
                        'description' => $deduction['description'] ?? null,
                    ]);
                }
            }
        }

        // Calculate totals
        $payroll->recalculateTotals();

        return redirect()->route('payrolls.show', $payroll)->with('success', 'Payroll created successfully.');
    }

    /**
     * Show payroll details
     */
    public function show(Payroll $payroll)
    {
        if (!auth()->user()->isAdmin()) {
            $employee = auth()->user()->employee;
            if (!$employee || $payroll->emp_id !== $employee->emp_id) {
                abort(403, 'Access denied');
            }
        }

        $payroll->load(['employee', 'deductions', 'approver']);
        return view('payrolls.show', compact('payroll'));
    }

    /**
     * Show edit form
     */
    public function edit(Payroll $payroll)
    {
        $employees = Employee::all();
        $deductionTypes = PayrollDeduction::getDeductionTypes();
        $payroll->load('deductions');
        return view('payrolls.edit', compact('payroll', 'employees', 'deductionTypes'));
    }

    /**
     * Update payroll
     */
    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'emp_id' => 'required|exists:employees,emp_id',
            'basic_salary' => 'required|numeric|min:0',
            'overtime_pay' => 'nullable|numeric|min:0',
            'bonus' => 'nullable|numeric|min:0',
            'late_deduction' => 'nullable|numeric|min:0',
            'absent_deduction' => 'nullable|numeric|min:0',
            'tax_deduction' => 'nullable|numeric|min:0',
            'sss_manual' => 'nullable|numeric|min:0',
            'philhealth_manual' => 'nullable|numeric|min:0',
            'pag_ibig_manual' => 'nullable|numeric|min:0',
            'tax_manual' => 'nullable|numeric|min:0',
            'pay_date' => 'required|date',
            'payroll_month' => 'nullable|string',

            // Additional deductions
            'additional_deductions' => 'nullable|array',
            'additional_deductions.*.type' => 'required_with:additional_deductions|string',
            'additional_deductions.*.name' => 'required_with:additional_deductions|string',
            'additional_deductions.*.amount' => 'required_with:additional_deductions|numeric|min:0',
            'additional_deductions.*.description' => 'nullable|string',
        ]);

        // Set default values
        $validated['overtime_pay'] = $validated['overtime_pay'] ?? 0;
        $validated['bonus'] = $validated['bonus'] ?? 0;
        $validated['late_deduction'] = $validated['late_deduction'] ?? 0;
        $validated['absent_deduction'] = $validated['absent_deduction'] ?? 0;
        $validated['tax_deduction'] = $validated['tax_deduction'] ?? 0;
        $validated['sss_manual'] = $validated['sss_manual'] ?? 0;
        $validated['philhealth_manual'] = $validated['philhealth_manual'] ?? 0;
        $validated['pag_ibig_manual'] = $validated['pag_ibig_manual'] ?? 0;
        $validated['tax_manual'] = $validated['tax_manual'] ?? 0;

        if (empty($validated['payroll_month'])) {
            $validated['payroll_month'] = $validated['pay_date']->format('Y-m');
        }

        // Update payroll
        $payroll->update($validated);

        // Clear and re-save additional deductions
        $payroll->deductions()->delete();
        if (!empty($request->input('additional_deductions'))) {
            foreach ($request->input('additional_deductions') as $deduction) {
                if (($deduction['amount'] ?? 0) > 0) {
                    PayrollDeduction::create([
                        'payroll_id' => $payroll->payroll_id,
                        'deduction_type' => $deduction['type'],
                        'deduction_name' => $deduction['name'],
                        'amount' => $deduction['amount'],
                        'description' => $deduction['description'] ?? null,
                    ]);
                }
            }
        }

        // Recalculate totals
        $payroll->recalculateTotals();

        return redirect()->route('payrolls.show', $payroll)->with('success', 'Payroll updated successfully.');
    }

    /**
     * Approve payroll
     */
    public function approve(Payroll $payroll)
    {
        $payroll->update([
            'status' => Payroll::STATUS_APPROVED,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->route('payrolls.show', $payroll)->with('success', 'Payroll approved successfully.');
    }

    /**
     * Delete payroll
     */
    public function destroy(Payroll $payroll)
    {
        $payroll->deductions()->delete();
        $payroll->delete();
        return redirect()->route('payrolls.index')->with('success', 'Payroll deleted successfully.');
    }

    /**
     * Print payroll slip
     */
    public function print(Payroll $payroll)
    {
        if (!auth()->user()->isAdmin()) {
            $employee = auth()->user()->employee;
            if (!$employee || $payroll->emp_id !== $employee->emp_id) {
                abort(403, 'Access denied');
            }
        }

        $payroll->load(['employee.user', 'employee.department', 'employee.position', 'deductions']);
        return view('payrolls.print', compact('payroll'));
    }

    /**
     * Monthly report
     */
    public function report(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $month = $request->get('month', date('n'));
        $payrollMonth = sprintf('%04d-%02d', $year, $month);

        $payrolls = Payroll::where('payroll_month', $payrollMonth)
            ->with('employee')
            ->get();

        $totals = [
            'basic' => $payrolls->sum('basic_salary'),
            'overtime' => $payrolls->sum('overtime_pay'),
            'bonus' => $payrolls->sum('bonus'),
            'gross' => $payrolls->sum('gross_salary'),
            'deductions' => $payrolls->sum('total_deductions'),
            'net' => $payrolls->sum('net_salary'),
        ];

        return view('payrolls.report', compact('payrolls', 'year', 'month', 'totals'));
    }
}

