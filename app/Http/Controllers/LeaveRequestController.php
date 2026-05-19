<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    /**
     * Display leave requests (for employees - their own requests)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        
        if (!$employee) {
            return back()->with('error', 'Employee profile not found.');
        }

        $status = $request->get('status');
        
        $query = LeaveRequest::where('emp_id', $employee->emp_id);
        
        if ($status) {
            $query->where('status', $status);
        }
        
$requests = $query->latest()->paginate(20);
        
        return view('leave-requests.index', compact('requests', 'status'));
    }

    /**
     * Display all leave requests (for admin)
     */
    public function all(Request $request)
    {
        $status = $request->get('status');
        
        $query = LeaveRequest::with('employee');
        
        if ($status) {
            $query->where('status', $status);
        }
        
$requests = $query->latest()->paginate(20);
        
        return view('leave-requests.all', compact('requests', 'status'));
    }

    /**
     * Show leave request form
     */
    public function create()
    {
        return view('leave-requests.create');
    }

    /**
     * Submit leave request
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        
        if (!$employee) {
            return back()->with('error', 'Employee profile not found.');
        }

        $validated = $request->validate([
            'leave_type' => 'required|in:sick,vacation,emergency,bereavement,maternity,paternity,other',
            'reason' => 'required|string|min:10',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Calculate total days
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        // Subtract weekends
        $days = $totalDays;
        for ($d = $startDate; $d->lte($endDate); $d->addDay()) {
            if ($d->isWeekend()) {
                $days--;
            }
        }

        $validated['emp_id'] = $employee->emp_id;
        $validated['total_days'] = max(1, $days);

        LeaveRequest::create($validated);

        return redirect()->route('leave-requests.index')->with('success', 'Leave request submitted successfully.');
    }

    /**
     * Show leave request details
     */
    public function show(LeaveRequest $leaveRequest, Request $request)
    {
        $leaveRequest->load('employee');

        // Preserve the admin list URL so the "Back" button works correctly
        $from = $request->query('from');

        return view('leave-requests.show', [
            'leaveRequest' => $leaveRequest,
            'from' => $from,
        ]);
    }


    /**
     * Approve leave request (admin)
     */
    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        $admin = Auth::user();
        
        $notes = $request->get('admin_notes');
        
        $leaveRequest->approve($admin, $notes);

        return redirect()->back()->with('success', 'Leave request approved.');
    }

    /**
     * Reject leave request (admin)
     */
    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $request->validate([
            'admin_notes' => 'required|string|min:5',
        ]);

        $admin = Auth::user();
        
        $leaveRequest->reject($admin, $request->admin_notes);

        return redirect()->back()->with('success', 'Leave request rejected.');
    }

    /**
     * Cancel own request (if still pending)
     */
    public function cancel(LeaveRequest $leaveRequest)
    {
        if (!$leaveRequest->isPending()) {
            return back()->with('error', 'Only pending requests can be cancelled.');
        }

        $leaveRequest->delete();

        return redirect()->route('leave-requests.index')->with('success', 'Leave request cancelled.');
    }

    /**
     * Pending requests count (for navbar badge)
     */
    public function pendingCount()
    {
        return LeaveRequest::pending()->count();
    }
}
