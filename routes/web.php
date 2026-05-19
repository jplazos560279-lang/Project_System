<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\LeaveRequestController;
use App\Models\Position;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $totalEmployees = \App\Models\Employee::count();
    $todayAttendance = \App\Models\Attendance::whereDate('date', today())->count();
    $pendingPayrolls = \App\Models\Payroll::whereMonth('pay_date', now()->month)->whereYear('pay_date', now()->year)->count();
    $departments = \App\Models\Department::count();
    return view('dashboard', compact('totalEmployees', 'todayAttendance', 'pendingPayrolls', 'departments'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Read-only routes for all authenticated users
Route::middleware('auth')->group(function () {
    Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    
    // Leave requests (employees)
    Route::get('leave-requests', [LeaveRequestController::class, 'index'])->name('leave-requests.index');
    Route::get('leave-requests/create', [LeaveRequestController::class, 'create'])->name('leave-requests.create');
    Route::post('leave-requests', [LeaveRequestController::class, 'store'])->name('leave-requests.store');
    Route::get('leave-requests/all', [LeaveRequestController::class, 'all'])->middleware('admin')->name('leave-requests.all');
    Route::get('leave-requests/{leaveRequest}', [LeaveRequestController::class, 'show'])->name('leave-requests.show');
    Route::delete('leave-requests/{leaveRequest}', [LeaveRequestController::class, 'cancel'])->name('leave-requests.cancel');
});

// Payroll routes (all authenticated users can view)
Route::middleware('auth')->group(function () {
    Route::get('payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
    Route::get('payrolls/create', [PayrollController::class, 'create'])->middleware('admin')->name('payrolls.create');
    Route::get('payrolls/report', [PayrollController::class, 'report'])->name('payrolls.report');
    Route::get('payrolls/{payroll}', [PayrollController::class, 'show'])->name('payrolls.show');
    Route::get('payrolls/{payroll}/print', [PayrollController::class, 'print'])->name('payrolls.print');
});

// Admin-only routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('positions', PositionController::class);
    
    // Payroll routes (store, edit, update, delete)
    Route::post('payrolls', [PayrollController::class, 'store'])->name('payrolls.store');
    Route::get('payrolls/{payroll}/edit', [PayrollController::class, 'edit'])->name('payrolls.edit');
    Route::put('payrolls/{payroll}', [PayrollController::class, 'update'])->name('payrolls.update');
    Route::delete('payrolls/{payroll}', [PayrollController::class, 'destroy'])->name('payrolls.destroy');
    
    // Payroll approval only
    Route::post('payrolls/{payroll}/approve', [PayrollController::class, 'approve'])->name('payrolls.approve');
    
    // Leave requests actions (approve/reject)
    Route::post('leave-requests/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::post('leave-requests/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');

    Route::get('attendances/create', [AttendanceController::class, 'create'])->name('attendances.create');
    Route::post('attendances', [AttendanceController::class, 'store'])->name('attendances.store');
    Route::get('attendances/{attendance}', [AttendanceController::class, 'show'])->name('attendances.show');
    Route::get('attendances/{attendance}/edit', [AttendanceController::class, 'edit'])->name('attendances.edit');
    Route::put('attendances/{attendance}', [AttendanceController::class, 'update'])->name('attendances.update');
    Route::delete('attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy');

    Route::get('employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    Route::get('departments/create', [DepartmentController::class, 'create'])->name('departments.create');
    Route::post('departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::get('departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
    Route::put('departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
});

// Show routes defined after create routes to avoid route conflicts
Route::middleware('auth')->group(function () {
    Route::get('employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('departments/{department}', [DepartmentController::class, 'show'])->name('departments.show');
});

Route::get('/api/positions-by-dept/{dept_id}', function ($dept_id) {
    return Position::where('dept_id', $dept_id)->get(['position_id', 'position_name']);
})->middleware('auth')->name('api.positions.by-dept');

require __DIR__.'/auth.php';
