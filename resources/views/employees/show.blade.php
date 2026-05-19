@extends('layouts.app')

@section('page-title', 'Employee Profile')

@section('content')

<a href="{{ route('employees.index') }}" class="btn-back">&larr; Back to Employees</a>

@if(session('success'))
    <div class="alert success">{{ session('success') }}</div>
@endif

<div class="profile-grid mt-5">
    <div class="card profile-card-col">
        <div class="profile-card">
            <div class="profile-avatar">
                {{ strtoupper(substr($employee->first_name, 0, 1)) }}{{ strtoupper(substr($employee->last_name, 0, 1)) }}
            </div>
            <h3 class="profile-name">{{ $employee->full_name }}</h3>
            <p class="profile-role">{{ $employee->position?->position_name ?? 'No Position' }}</p>
        </div>
        <div class="profile-details">
<span class="label">Email</span><span>{{ $employee->email }}</span>
            <div class="profile-row"><span class="label">Phone</span><span>{{ $employee->masked_phone }}</span></div>
            <div class="profile-row"><span class="label">Department</span><span>{{ $employee->department?->dept_name ?? 'N/A' }}</span></div>
            <div class="profile-row"><span class="label">Hire Date</span><span>{{ $employee->hire_date?->format('M d, Y') }}</span></div>
        </div>
        <div class="profile-actions">
            {{-- Edit/Delete buttons removed from Employee module --}}
        </div>
    </div>

    <div class="card profile-card-col wide">
        <h3>Recent Attendance</h3>
        @if($employee->attendances->count() > 0)
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr><th>Date</th><th>Time In</th><th>Time Out</th></tr>
                    </thead>
                    <tbody>
                        @foreach($employee->attendances->take(10) as $att)
                        <tr>
                            <td>{{ $att->date?->format('M d, Y') }}</td>
                            <td>{{ $att->time_in }}</td>
                            <td>{{ $att->time_out ?? 'Pending' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="empty">No attendance records.</p>
        @endif
    </div>
</div>

<div class="card mt-5">
    <h3>Payroll History</h3>
    @if($employee->payrolls->count() > 0)
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr><th>Pay Date</th><th>Basic</th><th>Deductions</th><th>Net</th></tr>
                </thead>
                <tbody>
                    @foreach($employee->payrolls as $pay)
                    <tr>
                        <td>{{ $pay->pay_date?->format('M d, Y') }}</td>
                        <td>${{ number_format($pay->basic_salary, 2) }}</td>
                        <td class="text-danger">${{ number_format($pay->deductions, 2) }}</td>
                        <td class="font-bold">${{ number_format($pay->net_salary, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="empty">No payroll records.</p>
    @endif
</div>

@endsection
