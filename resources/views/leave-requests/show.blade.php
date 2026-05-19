@extends('layouts.app')

@section('page-title', 'Leave Request Details')

@section('content')
<div class="content-header">
    <a href="{{ request('from') ? request('from') : route('leave-requests.index') }}" class="btn-back">← Back</a>
</div>


<div class="card">
    <div class="detail-grid">
        <div class="detail-item">
            <label>Leave Type</label>
            <p>{{ App\Models\LeaveRequest::LEAVE_TYPES[$leaveRequest->leave_type] ?? $leaveRequest->leave_type }}</p>
        </div>
        <div class="detail-item">
            <label>Start Date</label>
            <p>{{ $leaveRequest->start_date->format('M d, Y') }}</p>
        </div>
        <div class="detail-item">
            <label>End Date</label>
            <p>{{ $leaveRequest->end_date->format('M d, Y') }}</p>
        </div>
        <div class="detail-item">
            <label>Total Days</label>
            <p>{{ $leaveRequest->total_days }} day(s)</p>
        </div>
        <div class="detail-item">
            <label>Status</label>
            <p>
                <span class="badge badge-{{ $leaveRequest->status }}">
                    {{ ucfirst($leaveRequest->status) }}
                </span>
            </p>
        </div>
        <div class="detail-item">
            <label>Reason</label>
            <p>{{ $leaveRequest->reason }}</p>
        </div>
        
        @if($leaveRequest->admin_notes)
        <div class="detail-item">
            <label>Admin Notes</label>
            <p>{{ $leaveRequest->admin_notes }}</p>
        </div>
        @endif
        
        @if($leaveRequest->reviewed_at)
        <div class="detail-item">
            <label>Reviewed At</label>
            <p>{{ $leaveRequest->reviewed_at->format('M d, Y g:i A') }}</p>
        </div>
        @endif
    </div>

    @if($leaveRequest->isPending() && auth()->user()->isAdmin())
    <div class="form-actions" style="margin-top: 24px;">
        <form method="POST" action="{{ route('leave-requests.approve', $leaveRequest) }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn-primary">Approve</button>
        </form>
        
        <form method="POST" action="{{ route('leave-requests.reject', $leaveRequest) }}" style="display: inline;">
            @csrf
            <div class="form-group" style="display: inline-block; margin-bottom: 0; margin-left: 10px;">
                <input type="text" name="admin_notes" placeholder="Rejection reason..." required>
            </div>
            <button type="submit" class="btn-delete">Reject</button>
        </form>
    </div>
    @endif
</div>
@endsection
