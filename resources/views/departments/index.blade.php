@extends('layouts.app')

@section('page-title', 'Departments')

@section('content')

@if(session('success'))
    <div class="alert success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-header">
        <h3>All Departments</h3>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('departments.create') }}" class="btn-primary">+ Add Department</a>
        @endif
    </div>

    <form method="GET" action="{{ route('departments.index') }}" class="search-form">
        <input type="text" name="search" placeholder="Search departments..." value="{{ $search ?? '' }}">
        <button type="submit" class="btn-primary">Search</button>
        @if(isset($search) && $search)
            <a href="{{ route('departments.index') }}" class="btn-secondary">Clear</a>
        @endif
    </form>

    @if($departments->count())
        @if(auth()->user()->isAdmin())
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Head</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($departments as $dept)
                    <tr>
                        <td>{{ $dept->dept_name }}</td>
                        <td>{{ $dept->dept_head ?? 'N/A' }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('departments.edit', $dept) }}" class="btn-edit">Edit</a>

                                <form action="{{ route('departments.destroy', $dept) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-delete" onclick="return confirm('Delete this department?')">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="dashboard-grid">
                @foreach($departments as $dept)
                <a href="{{ route('departments.show', $dept) }}" style="display: block; text-decoration: none; color: inherit;">
                    <div class="card">
                        <h3>{{ $dept->dept_name }}</h3>
                        <p style="margin-bottom: 4px;"><strong>Head:</strong> {{ $dept->dept_head ?? 'N/A' }}</p>
                        <p style="color: var(--color-text-muted); font-size: var(--text-sm);">{{ $dept->employees->count() }} Employee{{ $dept->employees->count() !== 1 ? 's' : '' }}</p>
                    </div>
                </a>
                @endforeach
            </div>
        @endif
    @else
        <p class="empty">No departments found.</p>
@endif

@include('partials.pagination', ['paginator' => $departments])
</div>

@endsection

