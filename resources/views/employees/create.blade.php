@extends('layouts.app')

@section('page-title', 'Add Employee')

@section('content')

<a href="{{ route('employees.index') }}" class="btn-back">&larr; Back to Employees</a>

<div class="card card-form" style="margin-top:16px">
    <h3>New Employee</h3>

    <form action="{{ route('employees.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" value="{{ old('first_name') }}" required>
            @error('first_name')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" value="{{ old('last_name') }}" required>
            @error('last_name')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required>
            @error('email')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" required>
            @error('phone')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
            <label>Department</label>
            <select name="dept_id" id="dept_id" required>
                <option value="">-- Select Department --</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->dept_id }}" {{ old('dept_id') == $dept->dept_id ? 'selected' : '' }}>{{ $dept->dept_name }}</option>
                @endforeach
            </select>
            @error('dept_id')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
            <label>Position</label>
            <select name="position_id" id="position_id" required>
                <option value="">-- Select Position --</option>
                @foreach($positions as $pos)
                    <option value="{{ $pos->position_id }}" data-dept="{{ $pos->dept_id }}" {{ old('position_id') == $pos->position_id ? 'selected' : '' }}>{{ $pos->position_name }}</option>
                @endforeach
            </select>
            @error('position_id')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
            <label>Hire Date</label>
            <input type="date" name="hire_date" value="{{ old('hire_date') }}" required>
            @error('hire_date')<p class="error">{{ $message }}</p>@enderror
        </div>
        <div class="form-actions">
            <a href="{{ route('employees.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Create</button>
        </div>
    </form>
</div>

<script>
document.getElementById('dept_id').addEventListener('change', function() {
    let deptId = this.value;
    let posSelect = document.getElementById('position_id');
    posSelect.innerHTML = '<option value="">-- Select Position --</option>';

    if (!deptId) return;

    fetch('/api/positions-by-dept/' + deptId, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(r => r.json())
    .then(data => {
        data.forEach(pos => {
            let opt = document.createElement('option');
            opt.value = pos.position_id;
            opt.textContent = pos.position_name;
            posSelect.appendChild(opt);
        });
    });
});
</script>

@endsection
