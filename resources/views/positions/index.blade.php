@extends('layouts.app')

@section('page-title', 'Positions')

@section('content')

@if(session('success'))
    <div class="alert success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <h3>All Positions</h3>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('positions.create') }}" class="btn-primary">+ Add Position</a>
        @endif
    </div>

    <form method="GET" action="{{ route('positions.index') }}" class="search-form">
        <input type="text" name="search" placeholder="Search positions..." value="{{ $search ?? '' }}">
        <button type="submit" class="btn-primary">Search</button>
        @if(isset($search) && $search)
            <a href="{{ route('positions.index') }}" class="btn-secondary">Clear</a>
        @endif
    </form>

    @if($positions->count() > 0)
        <div style="overflow-x:auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Position Name</th>
                        <th>Department</th>
                        <th>Salary</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($positions as $pos)
                    <tr>
                        <td>{{ ($positions->currentPage() - 1) * $positions->perPage() + $loop->iteration }}</td>
                        <td>{{ $pos->position_name }}</td>
                        <td>{{ $pos->department?->dept_name ?? 'Unassigned' }}</td>
                        <td>${{ number_format($pos->salary, 2) }}</td>
                        <td>
                            @if(auth()->user()->role === 'admin')
                            <div class="actions">
                                <a href="{{ route('positions.edit', $pos) }}" class="btn-edit">Edit</a>
                                <form action="{{ route('positions.destroy', $pos) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-delete" onclick="return confirm('Delete this position?')">Delete</button>
                                    </form>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty">No positions yet.</div>
@endif

@include('partials.pagination', ['paginator' => $positions])
</div>

@endsection

