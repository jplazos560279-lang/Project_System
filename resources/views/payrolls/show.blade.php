@extends('layouts.app')

@section('page-title', 'Payroll Details')

@section('content')
<div class="content-header">
    <a href="{{ route('payrolls.index') }}" class="btn-back">← Back</a>
</div>

<!-- Status Badge -->
<div style="margin: 16px 0;">
    <span class="badge badge-{{ $payroll->status === 'approved' ? 'success' : ($payroll->status === 'paid' ? 'primary' : 'warning') }}" style="display:inline-block; min-width: 140px; text-align:center;">
        {{ ucfirst($payroll->status) }}
    </span>
    @if($payroll->approved_by)
        <span style="margin-left: 8px; font-size: 12px; color: #666;">
            Approved by {{ $payroll->approver?->name ?? 'Admin' }} on {{ $payroll->approved_at?->format('M d, Y H:i') }}
        </span>
    @endif
</div>

<!-- Employee Information -->
<div class="card">
    <div class="detail-grid">
        <div class="detail-item">
            <label>Employee</label>
            <p>{{ $payroll->employee?->first_name }} {{ $payroll->employee?->last_name }}</p>
        </div>
        <div class="detail-item">
            <label>Position</label>
            <p>{{ $payroll->employee?->position?->position_name ?? 'N/A' }}</p>
        </div>
        <div class="detail-item">
            <label>Department</label>
            <p>{{ $payroll->employee?->department?->dept_name ?? 'N/A' }}</p>
        </div>
        <div class="detail-item">
            <label>Pay Period</label>
            <p>{{ $payroll->payroll_month ?? $payroll->pay_date?->format('M Y') }}</p>
        </div>
        <div class="detail-item">
            <label>Pay Date</label>
            <p>{{ $payroll->pay_date?->format('M d, Y') }}</p>
        </div>
    </div>
</div>

<!-- Earnings Section -->
<div class="card" style="margin-top: 16px;">
    <h3 style="margin-bottom: 20px;">Earnings</h3>
    <table class="table" style="margin-bottom: 0;">
        <tr>
            <td>Basic Salary</td>
            <td style="text-align: right; font-weight: bold;">${{ number_format($payroll->basic_salary, 2) }}</td>
        </tr>
        @if($payroll->overtime_pay > 0)
        <tr>
            <td>Overtime Pay</td>
            <td style="text-align: right; color: green;">+${{ number_format($payroll->overtime_pay, 2) }}</td>
        </tr>
        @endif
        @if($payroll->bonus > 0)
        <tr>
            <td>Bonus</td>
            <td style="text-align: right; color: green;">+${{ number_format($payroll->bonus, 2) }}</td>
        </tr>
        @endif
        <tr style="font-weight: bold; background: var(--color-overlay);">
            <td>Gross Salary</td>
            <td style="text-align: right;">${{ number_format($payroll->gross_salary, 2) }}</td>
        </tr>
    </table>
</div>

<!-- Automatic Deductions Section -->
@if($payroll->late_deduction > 0 || $payroll->absent_deduction > 0 || $payroll->tax_deduction > 0)
<div class="card" style="margin-top: 16px;">
    <h3 style="margin-bottom: 20px;">Automatic Deductions</h3>
    <table class="table" style="margin-bottom: 0;">
        @if($payroll->late_deduction > 0)
        <tr>
            <td>Late Deduction</td>
            <td style="text-align: right; color: red;">-${{ number_format($payroll->late_deduction, 2) }}</td>
        </tr>
        @endif
        @if($payroll->absent_deduction > 0)
        <tr>
            <td>Absent Deduction</td>
            <td style="text-align: right; color: red;">-${{ number_format($payroll->absent_deduction, 2) }}</td>
        </tr>
        @endif
        @if($payroll->tax_deduction > 0)
        <tr>
            <td>Automatic Tax</td>
            <td style="text-align: right; color: red;">-${{ number_format($payroll->tax_deduction, 2) }}</td>
        </tr>
        @endif
    </table>
</div>
@endif

<!-- Government Deductions Section -->
@php
    $hasGovDeductions = $payroll->sss_manual > 0 || $payroll->philhealth_manual > 0 || 
                        $payroll->pag_ibig_manual > 0 || $payroll->tax_manual > 0;
@endphp

@if($hasGovDeductions)
<div class="card" style="margin-top: 16px;">
    <h3 style="margin-bottom: 20px;">Government Deductions (Manual)</h3>
    <table class="table" style="margin-bottom: 0;">
        @if($payroll->sss_manual > 0)
        <tr>
            <td>SSS (Social Security System)</td>
            <td style="text-align: right; color: red;">-${{ number_format($payroll->sss_manual, 2) }}</td>
        </tr>
        @endif
        @if($payroll->philhealth_manual > 0)
        <tr>
            <td>PhilHealth</td>
            <td style="text-align: right; color: red;">-${{ number_format($payroll->philhealth_manual, 2) }}</td>
        </tr>
        @endif
        @if($payroll->pag_ibig_manual > 0)
        <tr>
            <td>Pag-IBIG</td>
            <td style="text-align: right; color: red;">-${{ number_format($payroll->pag_ibig_manual, 2) }}</td>
        </tr>
        @endif
        @if($payroll->tax_manual > 0)
        <tr>
            <td>Income Tax (BIR)</td>
            <td style="text-align: right; color: red;">-${{ number_format($payroll->tax_manual, 2) }}</td>
        </tr>
        @endif
    </table>
</div>
@endif

<!-- Additional Deductions Section -->
@if(is_object($payroll->deductions) && method_exists($payroll->deductions, 'count') && $payroll->deductions->count() > 0)
<div class="card" style="margin-top: 16px;">
    <h3 style="margin-bottom: 20px;">Additional Deductions</h3>
    <table class="table" style="margin-bottom: 0;">
        @foreach($payroll->deductions as $deduction)
        <tr>
            <td>
                <strong>{{ $deduction->deduction_name }}</strong>
                @if($deduction->description)
                    <br><small style="color: #666;">{{ $deduction->description }}</small>
                @endif
            </td>
            <td style="text-align: right; color: red;">-${{ number_format($deduction->amount, 2) }}</td>
        </tr>
        @endforeach
    </table>
</div>
@endif

<!-- Total Deductions -->
<div class="card" style="margin-top: 16px;">
    <h3 style="margin-bottom: 20px;">Summary of All Deductions</h3>
    <table class="table" style="margin-bottom: 0;">
        <tr style="font-weight: bold; background: var(--color-overlay);">
            <td>Total Deductions</td>
            <td style="text-align: right; color: red;">-${{ number_format($payroll->total_deductions, 2) }}</td>
        </tr>
    </table>
</div>

<!-- Net Salary -->
<div class="card" style="margin-top: 16px; background: var(--color-brand-light); border: 2px solid var(--color-brand);">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0; color: var(--color-brand);">Net Salary</h3>
        <span style="font-size: 28px; font-weight: bold; color: var(--color-brand);">${{ number_format($payroll->net_salary, 2) }}</span>
    </div>
</div>

@if($payroll->computation_details)
<div class="card" style="margin-top: 16px;">
    <h3>Computation Details</h3>
    <pre style="background: var(--color-canvas); padding: 16px; border-radius: var(--radius-sm); font-size: 12px; white-space: pre-wrap;">{{ $payroll->computation_details }}</pre>
</div>
@endif

<!-- Action Buttons -->
<div class="form-actions" style="margin-top: 24px;">
<a href="{{ route('payrolls.print', $payroll) }}" class="btn-primary" target="_blank">Print Payslip</a>
    @if(auth()->user()->isAdmin())
        @if($payroll->status !== 'approved')
            <form action="{{ route('payrolls.approve', $payroll) }}" method="POST" class="inline" style="display: inline;">
                @csrf
                <button type="submit" class="btn-primary" onclick="return confirm('Approve this payroll?')">Approve</button>
            </form>
        @endif
        <a href="{{ route('payrolls.edit', $payroll) }}" class="btn-secondary">Edit</a>
        <form action="{{ route('payrolls.destroy', $payroll) }}" method="POST" class="inline" style="display: inline;">
            @csrf @method('DELETE')
            <button type="submit" class="btn-delete" onclick="return confirm('Delete this payroll record?')">Delete</button>
        </form>
    @endif
</div>


@endsection
