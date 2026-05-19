<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Department;
use Illuminate\Http\Request;

class PositionController extends Controller
{
public function index(Request $request)
    {
        $search = $request->get('search');
        
        $query = Position::with('department');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('position_name', 'like', "%{$search}%")
                  ->orWhere('salary', 'like', "%{$search}%")
                  ->orWhereHas('department', function($dq) use ($search) {
                      $dq->where('dept_name', 'like', "%{$search}%");
                  });
            });
        }
        
$positions = $query->paginate(5)->appends($request->query());
        return view('positions.index', compact('positions', 'search'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('positions.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'position_name' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'dept_id' => 'nullable|exists:departments,dept_id',
        ]);

        $validated['dept_id'] = $validated['dept_id'] ?: null;

        Position::create($validated);
        return redirect()->route('positions.index')->with('success', 'Position created successfully.');
    }

    public function show(Position $position)
    {
        return view('positions.show', compact('position'));
    }

    public function edit(Position $position)
    {
        $departments = Department::all();
        return view('positions.edit', compact('position', 'departments'));
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'position_name' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'dept_id' => 'nullable|exists:departments,dept_id',
        ]);

        $validated['dept_id'] = $validated['dept_id'] ?: null;

        $position->update($validated);
        return redirect()->route('positions.index')->with('success', 'Position updated successfully.');
    }

    public function destroy(Position $position)
    {
        $position->delete();
        return redirect()->route('positions.index')->with('success', 'Position deleted successfully.');
    }
}

