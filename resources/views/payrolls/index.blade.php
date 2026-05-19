@extends('layouts.app')

@section('page-title', 'Payroll')

@section('content')

@if(session('success'))
    <div class="alert success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert danger">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <h3>Payroll Records</h3>
        @if(auth()->user()->isAdmin())
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('payrolls.report', ['year' => date('Y'), 'month' => date('n')]) }}" class="btn-secondary">Monthly Report</a>
<!-- Auto-Generate button removed as requested -->
                <a href="{{ route('payrolls.create') }}" class="btn-primary">New Payroll</a>
            </div>
        @endif
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('payrolls.index') }}" class="search-form">
        <input type="text" name="search" placeholder="Search payroll..." value="{{ $search ?? '' }}">
        <select name="month" class="search-input" onchange="this.form.submit()">
            <option value="">All Months</option>
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ sprintf('%04d-%02d', date('Y'), $m) }}" {{ $month == sprintf('%04d-%02d', date('Y'), $m) ? 'selected' : '' }}>
                    {{ date('F Y', mktime(0, 0, 0, $m, 1)) }}
                </option>
            @endfor
        </select>
        <button type="submit" class="btn-primary">Search</button>
        @if(isset($search) || isset($month))
            <a href="{{ route('payrolls.index') }}" class="btn-secondary">Clear</a>
        @endif
    </form>

    @if($payrolls->count())
        <table class="table">
            <thead>
                <tr>
                    <th>Employee</th>
                    <th>Period</th>
                    <th style="text-align: right;">Gross Salary</th>
                    <th style="text-align: right;">Deductions</th>
                    <th style="text-align: right;">Net Salary</th>
                    <th style="text-align: center; width: 110px;">Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($payrolls as $pay)
                    <tr>
                        <td>
                            <strong>{{ $pay->employee?->first_name }} {{ $pay->employee?->last_name }}</strong>
                            <br><small style="color: #666;">{{ $pay->employee?->position?->position_name ?? 'N/A' }}</small>
                        </td>
                        <td>{{ $pay->payroll_month ?? $pay->pay_date?->format('M Y') }}</td>
                        <td style="text-align: right;">${{ number_format($pay->gross_salary, 2) }}</td>
                        <td style="text-align: right; color: red;">-${{ number_format($pay->total_deductions, 2) }}</td>
                        <td style="text-align: right; font-weight: bold; color: var(--color-brand); font-size: 16px;">${{ number_format($pay->net_salary, 2) }}</td>
                        <td style="text-align: center;">
                            <span class="badge badge-{{ $pay->status === 'approved' ? 'success' : ($pay->status === 'paid' ? 'primary' : 'warning') }}">
                                {{ ucfirst($pay->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('payrolls.show', $pay) }}" class="btn-view">View</a>
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('payrolls.print', $pay) }}" class="btn-view" target="_blank" style="font-size: smaller;">Print</a>
                                    @if($pay->status !== 'approved')
                                        <a href="{{ route('payrolls.edit', $pay) }}" class="btn-edit">Edit</a>
                                    @endif
                                    <form action="{{ route('payrolls.destroy', $pay) }}" method="POST" class="inline" style="display: inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-delete" onclick="return confirm('Delete this payroll record?')">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div style="margin-top: 20px; display: flex; justify-content: center;">
            {{ $payrolls->links() }}
        </div>
    @else
        <div style="padding: 40px; text-align: center; color: #666;">
            <p>No payroll records found.</p>
            @if(auth()->user()->isAdmin())
                <p><a href="{{ route('payrolls.create') }}" class="btn-primary">Create First Payroll</a></p>
            @endif
        </div>
    @endif
</div>

@endsection




