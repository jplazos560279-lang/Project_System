@extends('layouts.app')

@section('title', 'All Leave Requests')

@section('content')
<div class="content-header">
    <h1 class="title">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        All Leave Requests
    </h1>
</div>

@if(session('success'))
<div class="alert success">{{ session('success') }}</div>
@endif

{{-- Stats Summary --}}
<div class="leave-stats">
    <div class="leave-stat-card">
        <span>Total</span>
        <div class="count">{{ $requests->total() }}</div>
    </div>
    <div class="leave-stat-card pending">
        <span>Pending</span>
        <div class="count">{{ $requests->where('status', 'pending')->count() }}</div>
    </div>
    <div class="leave-stat-card approved">
        <span>Approved</span>
        <div class="count">{{ $requests->where('status', 'approved')->count() }}</div>
    </div>
    <div class="leave-stat-card rejected">
        <span>Rejected</span>
        <div class="count">{{ $requests->where('status', 'rejected')->count() }}</div>
    </div>
</div>

<div class="card">
    {{-- Filter Bar --}}
    <div class="filter-bar">
        <form method="GET" action="{{ route('leave-requests.all') }}" class="search-form">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by employee or reason...">
            <select name="status">
                <option value="">All Status</option>
                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <button type="submit" class="btn-secondary">Filter</button>
        </form>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('leave-requests.all') }}" class="btn-secondary">Clear</a>
        @endif
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th><input type="checkbox" class="bulk-checkbox" id="select-all"></th>
                    <th>Employee</th>
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
                    <td><input type="checkbox" class="bulk-checkbox" value="{{ $request->id }}"></td>
                    <td>
                        <strong>{{ $request->employee->full_name ?? 'N/A' }}</strong>
                        @if($request->employee->position)
                            <br><small>{{ $request->employee->position->name }}</small>
                        @endif
                    </td>
                    <td>{{ App\Models\LeaveRequest::LEAVE_TYPES[$request->leave_type] ?? $request->leave_type }}</td>
                    <td>{{ $request->start_date->format('M d, Y') }}</td>
                    <td>{{ $request->end_date->format('M d, Y') }}</td>
                    <td><strong>{{ $request->total_days }}</strong></td>
                    <td>
                        <span class="badge badge-{{ $request->status }}">
                            {{ ucfirst($request->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('leave-requests.show', ['leaveRequest' => $request, 'from' => url()->current()]) }}" class="btn-view">View</a>
                        @if($request->isPending())
                        <div class="actions-dropdown">
                            <button class="btn-edit">Actions</button>
                            <div class="dropdown-menu">
                                <form method="POST" action="{{ route('leave-requests.approve', $request) }}" style="display: block;">
                                    @csrf
                                    <button type="submit" class="dropdown-item btn-edit">Approve</button>
                                </form>
                                <a href="#" class="dropdown-item" onclick="rejectRequest({{ $request->id }})">Reject</a>
                            </div>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty">No leave requests found matching your filters.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $requests->links() }}
</div>

<script>
function rejectRequest(id) {
    const notes = prompt('Rejection reason:');
    if (notes) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/leave-requests/${id}/reject`;
        form.innerHTML = `@csrf`<input type="hidden" name="admin_notes" value="${notes}">';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection

