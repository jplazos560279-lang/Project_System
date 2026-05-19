<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = Department::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('dept_name', 'like', "%{$search}%")
                  ->orWhere('dept_head', 'like', "%{$search}%");
            });
        }
        
$departments = $query->paginate(6)->appends($request->query());
        return view('departments.index', compact('departments', 'search'));
    }

    public function create()
    {
        return view('departments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dept_name' => 'required|string|max:255',
            'dept_head' => 'nullable|string|max:255',
        ]);

        Department::create($validated);
        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
    }

    public function show(Department $department)
    {
        $department->load('employees.position');
        return view('departments.show', compact('department'));
    }

    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'dept_name' => 'required|string|max:255',
            'dept_head' => 'nullable|string|max:255',
        ]);

        $department->update($validated);
        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
    }
}
