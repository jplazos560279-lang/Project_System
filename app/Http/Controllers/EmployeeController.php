<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Employee::with(['department', 'position']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('department', function ($dq) use ($search) {
                        $dq->where('dept_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('position', function ($pq) use ($search) {
                        $pq->where('position_name', 'like', "%{$search}%");
                    });
            });
        }

        $employees = $query->paginate(5)->appends($request->query());

        return view('employees.index', compact('employees', 'search'));
    }

    public function create()
    {
        $departments = Department::all();
        $positions = Position::all();

        return view('employees.create', compact('departments', 'positions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:20',
            'dept_id' => 'required|exists:departments,dept_id',
            'position_id' => 'required|exists:positions,position_id',
            'hire_date' => 'required|date',
        ]);

        $emailHash = hash('sha256', strtolower(trim($validated['email'])));

        $existingEmployee = Employee::where('email_hash', $emailHash)->first();
        if ($existingEmployee) {
            return redirect()->route('employees.show', $existingEmployee)
                ->with('error', 'An employee with this email already exists. Showing existing record.');
        }

        $employee = Employee::create(array_merge($validated, ['email_hash' => $emailHash]));

        // Sync linked user if exists
        $user = User::where('email', $employee->email)->first();
        if ($user) {
            $user->update(['name' => $employee->full_name]);
            $employee->update(['user_id' => $user->id]);
        }

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        // Regular users can only view their own employee profile
        if (!auth()->user()->isAdmin()) {
            $userEmployee = auth()->user()->employee;

            if (!$userEmployee || $userEmployee->emp_id !== $employee->emp_id) {
                abort(403, 'You can only view your own employee profile.');
            }
        }

        $employee->load(['department', 'position', 'attendances', 'payrolls']);

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::all();
        $positions = Position::all();

        return view('employees.edit', compact('employee', 'departments', 'positions'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->emp_id . ',emp_id',
            'phone' => 'required|string|max:20',
            'dept_id' => 'required|exists:departments,dept_id',
            'position_id' => 'required|exists:positions,position_id',
            'hire_date' => 'required|date',
        ]);

        $emailHash = hash('sha256', strtolower(trim($validated['email'])));

        $existing = Employee::where('email_hash', $emailHash)
            ->where('emp_id', '!=', $employee->emp_id)
            ->first();

        if ($existing) {
            return redirect()->route('employees.index')->with('error', 'Another employee already has this email.');
        }

        $employee->update(array_merge($validated, ['email_hash' => $emailHash]));

        if ($employee->user) {
            $employee->user->update([
                'name' => $employee->full_name,
                'email' => $employee->email,
            ]);
        }

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        // If this endpoint is hit, ensure the request is authorized (admin-only).
        // This avoids cases where middleware/role checks prevent delete from working.
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Access denied. Admin only.');
        }

        \DB::beginTransaction();

        try {
            // Delete children using relationships so FK keys always match the model/schema.
            $employee->attendances()->delete();
            $employee->payrolls()->delete();
            \App\Models\LeaveRequest::where('emp_id', $employee->emp_id)->delete();

            // Delete linked user (belongsTo)
            $employee->user()->delete();

            // Finally delete employee
            $employee->delete();

            \DB::commit();

            return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
        } catch (\Throwable $e) {
            \DB::rollBack();

            \Log::error('Employee delete failed', [
                'employee_emp_id' => $employee->emp_id ?? null,
                'route_param' => request()->route('employee'),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('employees.index')
                ->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }


}


