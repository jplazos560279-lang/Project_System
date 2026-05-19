@extends('layouts.app')

@section('page-title', 'Leave Request Details')

@section('content')
<div class="card">
    <div class="card-header">
<h3>Leave Requests</h3>
        <a href="{{ route('leave-requests.create') }}" class="btn-primary">Request Leave</a>
    </div>

@if(session('success'))
    <div class="alert success">{{ session('success') }}</div>
@endif


{{-- Filter Bar --}}
        <form method="GET" action="{{ route('leave-requests.index') }}" class="search-form">

            <select name="status" class="search-input" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            @if($status)
                <a href="{{ route('leave-requests.index') }}" class="btn-secondary">Clear</a>
            @endif
        </form>

    <table class="table">
        <thead>
            <tr>
                <th>Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Days</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $request)
            <tr>
                <td>{{ App\Models\LeaveRequest::LEAVE_TYPES[$request->leave_type] ?? $request->leave_type }}</td>
                <td>{{ $request->start_date->format('M d, Y') }}</td>
                <td>{{ $request->end_date->format('M d, Y') }}</td>
                <td>{{ $request->total_days }}</td>
                <td>
                    <span class="badge badge-{{ $request->status }}">
                        {{ ucfirst($request->status) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('leave-requests.show', $request) }}" class="btn-view">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="empty">No leave requests found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $requests->links() }}
</div>
@endsection
