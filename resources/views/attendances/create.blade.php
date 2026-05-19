@extends('layouts.app')

@section('page-title', 'Record Attendance')

@section('content')

<a href="{{ route('attendances.index') }}" class="btn-back">&larr; Back to Attendance</a>

<div class="card card-form" style="margin-top:16px">
    <h3>Record Attendance</h3>

    <form action="{{ route('attendances.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Employee</label>
            <select name="emp_id" required>
                <option value="">Select</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->emp_id }}" {{ old('emp_id') == $emp->emp_id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                @endforeach
            </select>
            @error('emp_id')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required>
            @error('date')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Time In</label>
                <input type="time" name="time_in" value="{{ old('time_in') }}" required>
                @error('time_in')<p class="error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label>Time Out</label>
                <input type="time" name="time_out" value="{{ old('time_out') }}">
                @error('time_out')<p class="error">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="form-actions">
            <a href="{{ route('attendances.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Save</button>
        </div>
    </form>
</div>

@endsection

