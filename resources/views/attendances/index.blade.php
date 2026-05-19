@extends('layouts.app')

@section('page-title', 'Attendance')

@section('content')

@if(session('success'))
    <div class="alert success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <h3>Attendance Records</h3>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('attendances.create') }}" class="btn-primary">+ Record Attendance</a>
        @endif
    </div>

    @if(auth()->user()->role === 'admin')
    <form method="GET" action="{{ route('attendances.index') }}" class="search-form">
        <input type="text" name="search" placeholder="Search attendance..." value="{{ $search ?? '' }}">
        <button type="submit" class="btn-primary">Search</button>
        @if(isset($search) && $search)
            <a href="{{ route('attendances.index') }}" class="btn-secondary">Clear</a>
        @endif
    </form>
    @endif

    @if($attendances->count())
        <table class="table">
            <thead>
                <tr>
                    <th>Employee</th>

                    <th>Date</th>
                    <th>Time In</th>
                    <th>Time Out</th>
                    <th>Late Min</th>
                    <th>OT Hrs</th>
                    <th style="text-align:center; width: 160px;">Status</th>
@if(auth()->user()->role === 'admin')
                    <th>Actions</th>
@endif

                </tr>
            </thead>

            <tbody>
                @foreach($attendances as $att)
                <tr>

                    <td>{{ $att->employee?->full_name ?? 'N/A' }}</td>
                    <td>{{ $att->date?->format('M d, Y') }}</td>
                    <td>{{ $att->time_in }}</td>
                    <td>{{ $att->time_out ?? 'Pending' }}</td>
                    <td>{{ $att->late_minutes ?? 0 }} min</td>
                    <td>{{ number_format($att->overtime_hours ?? 0, 1) }} hrs</td>
                    <td style="text-align: center;">
                        <span class="badge {{ $att->status === 'present' ? 'success' : ($att->status === 'late' ? 'warning' : 'danger') }}" style="display:inline-block; min-width: 110px; text-align:center;">
                            {{ ucfirst($att->status ?? 'pending') }}
                        </span>
                    </td>
@if(auth()->user()->role === 'admin')
                    <td>
                        <div class="actions">
                            <a href="{{ route('attendances.edit', $att) }}" class="btn-edit">Edit</a>

                            <form action="{{ route('attendances.destroy', $att) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete" onclick="return confirm('Delete this attendance record?')">Delete</button>
                            </form>
                        </div>
                    </td>
@endif

                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="empty">No attendance records found.</p>
@endif

@include('partials.pagination', ['paginator' => $attendances])
</div>

@endsection

