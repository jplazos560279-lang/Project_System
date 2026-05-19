@extends('layouts.app')

@section('page-title', 'Payroll Report')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        
        <div class="text-muted">
            <strong>{{ $month }}/{{ $year }}</strong>
        </div>
    </div>

    @if($payrolls->isEmpty())
        <div class="alert alert-info">
            No payroll records found for <strong>{{ $month }}/{{ $year }}</strong>.
        </div>
    @else
        <div class="table-responsive mb-4">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th class="text-end">Basic</th>
                        <th class="text-end">Overtime</th>
                        <th class="text-end">Bonus</th>
                        <th class="text-end">Gross</th>
                        <th class="text-end">Deductions</th>
                        <th class="text-end">Net</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payrolls as $payroll)
                        <tr>
                            <td>
                                {{ $payroll->employee?->first_name }} {{ $payroll->employee?->last_name }}
                            </td>
                            <td class="text-end">{{ number_format($payroll->basic_salary, 2) }}</td>
                            <td class="text-end">{{ number_format($payroll->overtime_pay, 2) }}</td>
                            <td class="text-end">{{ number_format($payroll->bonus, 2) }}</td>
                            <td class="text-end">{{ number_format($payroll->gross_salary, 2) }}</td>
                            <td class="text-end">{{ number_format($payroll->total_deductions, 2) }}</td>
                            <td class="text-end">{{ number_format($payroll->net_salary, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card">
            <div class="card-header">Totals</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <div><strong>Basic:</strong> {{ number_format($totals['basic'] ?? 0, 2) }}</div>
                        <div><strong>Overtime:</strong> {{ number_format($totals['overtime'] ?? 0, 2) }}</div>
                        <div><strong>Bonus:</strong> {{ number_format($totals['bonus'] ?? 0, 2) }}</div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div><strong>Gross:</strong> {{ number_format($totals['gross'] ?? 0, 2) }}</div>
                        <div><strong>Deductions:</strong> {{ number_format($totals['deductions'] ?? 0, 2) }}</div>
                        <div><strong>Net:</strong> {{ number_format($totals['net'] ?? 0, 2) }}</div>
                    </div>
                    <div class="col-12 col-md-4 text-md-end">
                        <a href="{{ route('payrolls.index') }}" class="btn btn-secondary">Back to Payrolls</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

