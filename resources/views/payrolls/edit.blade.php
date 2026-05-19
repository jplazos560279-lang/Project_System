@extends('layouts.app')

@section('page-title', 'Edit Payroll')

@section('content')

<a href="{{ route('payrolls.index') }}" class="btn-back">&larr; Back to Payrolls</a>

<div class="card card-form" style="margin-top:0; padding-top:0; margin-left:auto; margin-right:auto; max-width: 900px;">
    <h3>Edit Payroll</h3>

    <script>
        function toNumber(v) {
            const n = parseFloat(v);
            return Number.isFinite(n) ? n : 0;
        }

        function formatCurrency(amount) {
            const n = Number.isFinite(amount) ? amount : 0;
            return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(n);
        }

        function calculateGross() {
            const basic = toNumber(document.getElementById('basic_salary')?.value);
            const overtime = toNumber(document.getElementById('overtime_pay')?.value);
            const bonus = toNumber(document.getElementById('bonus')?.value);

            const gross = basic + overtime + bonus;

            const grossDisplay = document.getElementById('grossDisplay');
            if (grossDisplay) {
                grossDisplay.textContent = `$${formatCurrency(gross)}`;
            }

            calculateDeductions();
        }

        function calculateDeductions() {
            const late = toNumber(document.getElementById('late_deduction')?.value);
            const absent = toNumber(document.getElementById('absent_deduction')?.value);
            const autoTax = toNumber(document.getElementById('tax_deduction')?.value);

            const sss = toNumber(document.getElementById('sss_manual')?.value);
            const philhealth = toNumber(document.getElementById('philhealth_manual')?.value);
            const pagibig = toNumber(document.getElementById('pag_ibig_manual')?.value);
            const taxManual = toNumber(document.getElementById('tax_manual')?.value);

            const totalDeductions = late + absent + autoTax + sss + philhealth + pagibig + taxManual;

            const summaryDeductions = document.getElementById('summaryDeductions');
            if (summaryDeductions) {
                summaryDeductions.textContent = `$${formatCurrency(totalDeductions)}`;
            }

            const basic = toNumber(document.getElementById('basic_salary')?.value);
            const overtime = toNumber(document.getElementById('overtime_pay')?.value);
            const bonus = toNumber(document.getElementById('bonus')?.value);
            const gross = basic + overtime + bonus;
            const net = gross - totalDeductions;

            const summaryNet = document.getElementById('summaryNet');
            if (summaryNet) {
                summaryNet.textContent = `$${formatCurrency(net)}`;
            }

            const summaryGross = document.getElementById('summaryGross');
            if (summaryGross) {
                summaryGross.textContent = `$${formatCurrency(gross)}`;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            calculateGross();
            calculateDeductions();
        });
    </script>

    <form action="{{ route('payrolls.update', $payroll) }}" method="POST" id="payrollForm">
        @csrf
        @method('PUT')
        
        <!-- Basic Information -->
        <fieldset class="form-section">
            <legend>Employee Information</legend>
            <div class="form-group">
                <label for="emp_id">Employee</label>
                <select name="emp_id" id="emp_id" required>
                    <option value="">Select an Employee</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->emp_id }}" data-salary="{{ $emp->basic_salary ?? 0 }}" {{ $payroll->emp_id == $emp->emp_id ? 'selected' : '' }}>
                            {{ $emp->first_name }} {{ $emp->last_name }} - {{ $emp->position?->position_name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
                @error('emp_id')<p class="error">{{ $message }}</p>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="pay_date">Pay Date</label>
                    <input type="date" name="pay_date" id="pay_date" value="{{ $payroll->pay_date?->format('Y-m-d') ?? old('pay_date') }}" required>
                    @error('pay_date')<p class="error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label for="payroll_month">Payroll Month</label>
                    <input type="text" name="payroll_month" id="payroll_month" value="{{ $payroll->payroll_month }}" readonly style="background: #f5f5f5;">
                </div>
            </div>
        </fieldset>

        <!-- Earnings Section -->
        <fieldset class="form-section">
            <legend>Earnings</legend>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="basic_salary">Basic Salary</label>
                    <input type="number" step="0.01" name="basic_salary" id="basic_salary" value="{{ $payroll->basic_salary }}" required onchange="calculateGross()">
                    @error('basic_salary')<p class="error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label for="overtime_pay">Overtime Pay</label>
                    <input type="number" step="0.01" name="overtime_pay" id="overtime_pay" value="{{ $payroll->overtime_pay ?? 0 }}" min="0" onchange="calculateGross()">
                    @error('overtime_pay')<p class="error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label for="bonus">Bonus</label>
                    <input type="number" step="0.01" name="bonus" id="bonus" value="{{ $payroll->bonus ?? 0 }}" min="0" onchange="calculateGross()">
                    @error('bonus')<p class="error">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="form-row" style="background: #f9f9f9; padding: 12px; border-radius: 4px; margin: 12px 0;">
                <div class="form-group" style="flex: 1;">
                    <label style="font-weight: bold; color: var(--color-brand);">Gross Salary</label>
                    <div id="grossDisplay" style="font-size: 18px; font-weight: bold; padding: 8px;">$0.00</div>
                </div>
            </div>
        </fieldset>

        <!-- Automatic Deductions Section -->
        <fieldset class="form-section">
            <legend>Automatic Deductions</legend>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="late_deduction">Late Deduction</label>
                    <input type="number" step="0.01" name="late_deduction" id="late_deduction" value="{{ $payroll->late_deduction ?? 0 }}" min="0" onchange="calculateDeductions()">
                </div>
                <div class="form-group">
                    <label for="absent_deduction">Absent Deduction</label>
                    <input type="number" step="0.01" name="absent_deduction" id="absent_deduction" value="{{ $payroll->absent_deduction ?? 0 }}" min="0" onchange="calculateDeductions()">
                </div>
                <div class="form-group">
                    <label for="tax_deduction">Automatic Tax</label>
                    <input type="number" step="0.01" name="tax_deduction" id="tax_deduction" value="{{ $payroll->tax_deduction ?? 0 }}" min="0" onchange="calculateDeductions()">
                </div>
            </div>
        </fieldset>

        <!-- Government Deductions Section (MANUAL ENTRY) -->
        <fieldset class="form-section">
            <legend>Government Deductions (Manual Entry)</legend>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="sss_manual">SSS (Social Security System)</label>
                    <input type="number" step="0.01" name="sss_manual" id="sss_manual" value="{{ $payroll->sss_manual ?? 0 }}" min="0" onchange="calculateDeductions()">
                </div>
                <div class="form-group">
                    <label for="philhealth_manual">PhilHealth</label>
                    <input type="number" step="0.01" name="philhealth_manual" id="philhealth_manual" value="{{ $payroll->philhealth_manual ?? 0 }}" min="0" onchange="calculateDeductions()">
                </div>
                <div class="form-group">
                    <label for="pag_ibig_manual">Pag-IBIG</label>
                    <input type="number" step="0.01" name="pag_ibig_manual" id="pag_ibig_manual" value="{{ $payroll->pag_ibig_manual ?? 0 }}" min="0" onchange="calculateDeductions()">
                </div>
                <div class="form-group">
                    <label for="tax_manual">Income Tax (BIR)</label>
                    <input type="number" step="0.01" name="tax_manual" id="tax_manual" value="{{ $payroll->tax_manual ?? 0 }}" min="0" onchange="calculateDeductions()">
                </div>
            </div>
        </fieldset>



        <!-- Summary -->
        <div style="background: #f0f4ff; padding: 16px; border-radius: 4px; margin: 16px 0;">
            <div class="summary-row">
                <span>Gross Salary:</span>
                <strong id="summaryGross">$0.00</strong>
            </div>
            <div class="summary-row">
                <span>Total Deductions:</span>
                <strong style="color: #d9534f;" id="summaryDeductions">$0.00</strong>
            </div>
            <div class="summary-row" style="border-top: 2px solid #333; padding-top: 12px; margin-top: 12px;">
                <span style="font-weight: bold;">Net Salary:</span>
                <strong style="font-size: 18px; color: var(--color-brand);" id="summaryNet">$0.00</strong>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="form-actions">
            <a href="{{ route('payrolls.show', $payroll) }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Update Payroll</button>
        </div>
    </form>
</div>
@endsection





