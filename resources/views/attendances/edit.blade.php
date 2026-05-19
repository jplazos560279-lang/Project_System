@extends('layouts.app')

@section('page-title', 'Edit Attendance')

@section('content')

<a href="{{ route('attendances.index') }}" class="btn-back">&larr; Back to Attendance</a>

<div class="card card-form" style="margin-top:16px">
    <h3>Edit Attendance</h3>

    <form action="{{ route('attendances.update', $attendance) }}" method="POST">
        @csrf @method('PUT')
        <div class="form-group">
            <label>Employee</label>
            <select name="emp_id" required>
                <option value="">Select</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->emp_id }}" {{ old('emp_id', $attendance->emp_id) == $emp->emp_id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                @endforeach
            </select>
            @error('emp_id')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="date" value="{{ old('date', $attendance->date?->format('Y-m-d')) }}" required>
            @error('date')<p class="error">{{ $message }}</p>@enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Time In</label>
                <input type="time" name="time_in" value="{{ old('time_in', $attendance->time_in) }}" required>
                @error('time_in')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label>Time Out</label>
                <input type="time" name="time_out" value="{{ old('time_out', $attendance->time_out) }}">
                @error('time_out')<p class="error">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="form-group">
            <label>Late Minutes</label>
            <input type="number" name="late_minutes" value="{{ old('late_minutes', $attendance->late_minutes) }}" readonly>
        </div>
        <div class="form-group">
            <label>Overtime Hours</label>
            <input type="number" step="0.01" name="overtime_hours" value="{{ old('overtime_hours', $attendance->overtime_hours) }}" readonly>
        </div>
        @if(auth()->user()->isAdmin())
        <div class="form-group">
            <label>
                <input type="checkbox" name="has_excuse" {{ old('has_excuse', $attendance->has_excuse) ? 'checked' : '' }}>
                Excuse Approved (No late deduction)
            </label>
        </div>
        @endif
        <div class="form-actions">
            <a href="{{ route('attendances.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Update</button>
        </div>

    </form>
</div>

@endsection

