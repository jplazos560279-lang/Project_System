@extends('layouts.app')

@section('page-title', 'Employees')

@section('content')

@if(session('success'))
    <div class="alert success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <h3>All Employees</h3>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('employees.create') }}" class="btn-primary">+ Add Employee</a>
        @endif
    </div>

    <form method="GET" action="{{ route('employees.index') }}" class="search-form">
        <input type="text" name="search" placeholder="Search employees..." value="{{ $search ?? '' }}">
        <button type="submit" class="btn-primary">Search</button>
        @if(isset($search) && $search)
            <a href="{{ route('employees.index') }}" class="btn-secondary">Clear</a>
        @endif
    </form>

    @if($employees->count())
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($employees as $emp)
                <tr>
                    <td>{{ $emp->full_name }}</td>
                    <td>{{ $emp->email }}</td>
                    <td>{{ $emp->department?->dept_name ?? 'N/A' }}</td>
                    <td>{{ $emp->position?->position_name ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('employees.show', $emp) }}" class="btn-view">View</a>

                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('employees.edit', $emp) }}" class="btn-edit">Edit</a>

                            <form action="{{ route('employees.destroy', $emp) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete" onclick="return confirm('Are you sure you want to delete this employee?')">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="empty">No employees found.</p>
@endif

@include('partials.pagination', ['paginator' => $employees])
</div>

@endsection

