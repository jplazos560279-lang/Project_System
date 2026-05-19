@extends('layouts.app')

@section('page-title', 'Department Details')

@section('content')

<a href="{{ route('departments.index') }}" class="btn-back">&larr; Back to Departments</a>

<div class="profile-grid mt-5">
    <div class="card profile-card-col">
        <div class="profile-card">
            <div class="profile-avatar">
                {{ strtoupper(substr($department->dept_name, 0, 1)) }}
            </div>
            <h3 class="profile-name">{{ $department->dept_name }}</h3>
            <p class="profile-role">{{ $department->dept_head ?? 'No Head Assigned' }}</p>
        </div>
        <div class="profile-details">
            <div class="profile-row"><span class="label">Department Name</span><span>{{ $department->dept_name }}</span></div>
            <div class="profile-row"><span class="label">Department Head</span><span>{{ $department->dept_head ?? 'N/A' }}</span></div>
            <div class="profile-row"><span class="label">Total Employees</span><span>{{ $department->employees->count() }}</span></div>
        </div>
        <div class="profile-actions">
            @if(auth()->user()->isAdmin())
            <a href="{{ route('departments.edit', $department) }}" class="btn-primary btn-block">Edit Department</a>

            <form action="{{ route('departments.destroy', $department) }}" method="POST" class="mt-4" data-confirm="Are you sure you want to delete this department?">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-outline-danger">Delete Department</button>
            </form>
            @endif
        </div>
    </div>

    <div class="card profile-card-col wide">
        <h3>Employees in this Department</h3>
        @if($department->employees->count() > 0)
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr><th>Name</th><th>Email</th><th>Position</th></tr>
                    </thead>
                    <tbody>
                        @foreach($department->employees as $emp)
                        <tr>
                            <td>{{ $emp->full_name }}</td>
<td>{{ $emp->email }}</td>
                            <td>{{ $emp->position?->position_name ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="empty">No employees in this department.</p>
        @endif
    </div>
</div>

@endsection

