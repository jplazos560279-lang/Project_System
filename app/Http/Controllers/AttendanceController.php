<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
public function index(Request $request)
    {
        $search = $request->get('search');
        
        if (auth()->user()->isAdmin()) {
            $query = Attendance::with('employee');
            
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('date', 'like', "%{$search}%")
                      ->orWhere('time_in', 'like', "%{$search}%")
                      ->orWhere('time_out', 'like', "%{$search}%")
                      ->orWhereHas('employee', function($eq) use ($search) {
                          $eq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                      });
                });
            }
            
            $attendances = $query->latest()->paginate(5)->appends($request->query());
        } else {
            $employee = auth()->user()->employee;

            if (!$employee) {
                $user = auth()->user();
                $emailHash = hash('sha256', strtolower(trim($user->email)));

                // Check if an employee with the same email already exists (use email_hash because email is encrypted)
                $existingEmployee = Employee::where('email_hash', $emailHash)->first();

                if ($existingEmployee) {
                    // Link existing employee to this user
                    $existingEmployee->update(['user_id' => $user->id]);
                    $employee = $existingEmployee;
                } else {
                    $nameParts = explode(' ', $user->name, 2);
                    $firstName = $nameParts[0];
                    $lastName = $nameParts[1] ?? '';

                    $department = \App\Models\Department::first() ?? \App\Models\Department::create([
                        'dept_name' => 'General',
                        'dept_head' => 'Admin'
                    ]);

                    $position = \App\Models\Position::first() ?? \App\Models\Position::create([
                        'position_name' => 'Staff',
                        'dept_id' => $department->dept_id
                    ]);

                    $employee = Employee::create([
                        'user_id' => $user->id,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $user->email,
                        'email_hash' => $emailHash,
                        'phone' => '0000000000',
                        'dept_id' => $department->dept_id,
                        'position_id' => $position->position_id,
                        'hire_date' => now(),
                    ]);
                }
            }

            $attendances = Attendance::with('employee')->where('emp_id', $employee->emp_id)->latest()->paginate(5);
        }
        return view('attendances.index', compact('attendances', 'search'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('attendances.create', compact('employees'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'emp_id' => 'required|exists:employees,emp_id',
            'date' => 'required|date',
            'time_in' => 'required',
            'time_out' => 'nullable',
            'has_excuse' => 'nullable|boolean',
        ]);

        $timeIn = \Carbon\Carbon::parse($validated['time_in']);
        $timeOut = $validated['time_out'] ? \Carbon\Carbon::parse($validated['time_out']) : null;
        
        $expectedIn = \Carbon\Carbon::parse('08:00:00');
        $expectedOut = \Carbon\Carbon::parse('20:00:00');
        
        $lateMinutes = $timeIn->greaterThan($expectedIn) ? $timeIn->diffInMinutes($expectedIn) : 0;
        $overtimeHours = $timeOut && $timeOut->greaterThan($expectedOut) ? $timeOut->diffInHours($expectedOut, false) + ($timeOut->diffInMinutes($expectedOut) / 60) : 0;
        
        $validated['late_minutes'] = $lateMinutes;
        $validated['overtime_hours'] = $overtimeHours;
        $validated['has_excuse'] = $request->has('has_excuse') ? 1 : 0;
        
        // Set status
        if ($lateMinutes > 0 && !$validated['has_excuse']) {
            $validated['status'] = 'late';
        } elseif ($timeOut && $timeOut->lessThan($expectedOut->subHour(4))) {
            $validated['status'] = 'absent';
        } else {
            $validated['status'] = 'present';
        }
        
        Attendance::create($validated);
        return redirect()->route('attendances.index')->with('success', 'Attendance recorded successfully.');
    }


    public function show(Attendance $attendance)
    {
        return view('attendances.show', compact('attendance'));
    }

    public function edit(Attendance $attendance)
    {
        $employees = Employee::all();
        return view('attendances.edit', compact('attendance', 'employees'));
    }


    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'emp_id' => 'required|exists:employees,emp_id',
            'date' => 'required|date',
            'time_in' => 'required',
            'time_out' => 'nullable',
            'late_minutes' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'has_excuse' => 'nullable|boolean',
        ]);

        $timeIn = \Carbon\Carbon::parse($validated['time_in']);
        $timeOut = isset($validated['time_out']) ? \Carbon\Carbon::parse($validated['time_out']) : null;
        
        $expectedIn = \Carbon\Carbon::parse('08:00:00');
        $expectedOut = \Carbon\Carbon::parse('20:00:00');
        
        $lateMinutes = $timeIn->greaterThan($expectedIn) ? $timeIn->diffInMinutes($expectedIn) : 0;
        $overtimeHours = $timeOut && $timeOut->greaterThan($expectedOut) ? $timeOut->diffInHours($expectedOut, false) + ($timeOut->diffInMinutes($expectedOut) / 60) : 0;
        
        $validated['late_minutes'] = $request->filled('late_minutes') ? $request->late_minutes : $lateMinutes;
        $validated['overtime_hours'] = $request->filled('overtime_hours') ? $request->overtime_hours : $overtimeHours;
        $validated['has_excuse'] = $request->has('has_excuse') ? 1 : 0;
        
        // Set status
        if ($validated['late_minutes'] > 0 && !$validated['has_excuse']) {
            $validated['status'] = 'late';
        } elseif ($timeOut && $timeOut->lessThan($expectedOut->subHour(4))) {
            $validated['status'] = 'absent';
        } else {
            $validated['status'] = 'present';
        }
        
        $attendance->update($validated);
        return redirect()->route('attendances.index')->with('success', 'Attendance updated successfully.');
    }


    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('attendances.index')->with('success', 'Attendance deleted successfully.');
    }
}
