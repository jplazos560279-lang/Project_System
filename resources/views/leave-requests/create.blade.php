@extends('layouts.app')

@section('page-title', 'Request Leave')

@section('content')
<div class="content-header">

    <a href="{{ route('leave-requests.index') }}" class="btn-back">← Back</a>
</div>

<div class="card card-form">
    <form method="POST" action="{{ route('leave-requests.store') }}">
        @csrf

        <div class="form-group">
            <label for="leave_type">Leave Type</label>
            <select name="leave_type" id="leave_type" required>
                <option value="">Select leave type</option>
                @foreach(App\Models\LeaveRequest::LEAVE_TYPES as $value => $label)
                <option value="{{ $value }}" {{ old('leave_type') == $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
                @endforeach
            </select>
            @error('leave_type')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required min="{{ date('Y-m-d') }}">
                @error('start_date')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required min="{{ date('Y-m-d') }}">
                @error('end_date')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="reason">Reason</label>
            <textarea name="reason" id="reason" rows="4" required placeholder="Please provide details for your leave request...">{{ old('reason') }}</textarea>
            @error('reason')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Submit Request</button>
        </div>
    </form>
</div>
@endsection
