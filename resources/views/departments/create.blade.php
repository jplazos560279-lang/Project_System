@extends('layouts.app')

@section('page-title', 'Add Department')

@section('content')

<a href="{{ route('departments.index') }}" class="btn-back">&larr; Back to Departments</a>

<div class="card card-form" style="margin-top:16px">
    <h3>Create Department</h3>

    <form action="{{ route('departments.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Department Name</label>
            <input type="text" name="dept_name" value="{{ old('dept_name') }}" required>
            @error('dept_name')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
            <label>Department Head</label>
            <input type="text" name="dept_head" value="{{ old('dept_head') }}">
            @error('dept_head')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="form-actions">
            <a href="{{ route('departments.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Create</button>
        </div>
    </form>
</div>

@endsection

