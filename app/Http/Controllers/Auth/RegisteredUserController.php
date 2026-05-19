<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use App\Models\Employee;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $emailHash = hash('sha256', strtolower(trim($request->email)));

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        // Find or create a default department and position
        $department = Department::first() ?? Department::create([
            'dept_name' => 'General',
            'dept_head' => 'Admin'
        ]);

        $position = Position::first() ?? Position::create([
            'position_name' => 'Staff',
            'dept_id' => $department->dept_id,
            // Ensure required field exists (migration requires salary)
            'salary' => 0,
        ]);


        // Extract first and last name
        $nameParts = explode(' ', $request->name, 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        // Check if an employee with the same email already exists (use email_hash because email is encrypted)
        $existingEmployee = Employee::where('email_hash', $emailHash)->first();

        if ($existingEmployee) {
            // Link existing employee to this user and sync name
            $existingEmployee->update([
                'user_id' => $user->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]);
        } else {
            // Create linked employee profile
            Employee::create([
                'user_id' => $user->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $request->email,
                'email_hash' => $emailHash,
                'phone' => '0000000000',
                'dept_id' => $department->dept_id,
                'position_id' => $position->position_id,
                'hire_date' => now(),
            ]);
        }

        event(new Registered($user));

        return redirect()->route('login')->with('success', 'Account created successfully. Please login.');
    }
}
