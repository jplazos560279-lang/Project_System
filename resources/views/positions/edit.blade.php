@extends('layouts.app')

@section('page-title', 'Edit Position')

@section('content')

<a href="{{ route('positions.index') }}" class="btn-back">&larr; Back to Positions</a>

<div class="card card-form" style="margin-top:16px">
    <h3>Edit Position</h3>

    <form action="{{ route('positions.update', $position) }}" method="POST">
        @csrf @method('PUT')
        <div class="form-group">
            <label>Position Name</label>
            <input type="text" name="position_name" value="{{ old('position_name', $position->position_name) }}" required>
            @error('position_name')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
            <label>Department</label>
            <select name="dept_id">
                <option value="">-- Select Department --</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->dept_id }}" {{ old('dept_id', $position->dept_id) == $dept->dept_id ? 'selected' : '' }}>{{ $dept->dept_name }}</option>
                @endforeach
            </select>
            @error('dept_id')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
            <label>Salary</label>
            <input type="number" step="0.01" name="salary" value="{{ old('salary', $position->salary) }}" required>
            @error('salary')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="form-actions">
            <a href="{{ route('positions.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Update</button>
        </div>
    </form>
</div>

@endsection

