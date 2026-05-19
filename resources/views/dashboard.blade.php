@extends('layouts.app')

@section('page-title', auth()->user()->isAdmin() ? 'Admin Dashboard' : 'Employee Dashboard')

@section('content')

<!-- TOP KPIs (at-a-glance) -->
<div class="stats-grid">
    <div class="stat-card kpi-employees">
        <span>Total Employees</span>
        <h2>{{ \App\Models\Employee::count() }}</h2>
    </div>
    <div class="stat-card kpi-attendance">
        <span>Present Today</span>
        <h2>{{ \App\Models\Attendance::whereDate('date', now())->count() }}</h2>
    </div>
    <div class="stat-card kpi-departments">
        <span>Departments</span>
        <h2>{{ \App\Models\Department::count() }}</h2>
    </div>
    <div class="stat-card kpi-payroll">
        <span>Payroll (This Month)</span>
        <h2>${{ number_format(\App\Models\Payroll::whereMonth('pay_date', now()->month)->sum('net_salary'), 0) }}</h2>
    </div>
</div>

<!-- CHARTS ROW -->
<div class="charts-grid">

    <div class="card">
        <h3>Attendance Trend</h3>
        <canvas id="attendanceChart"></canvas>
    </div>

    <div class="card">
        <h3>Employees by Department</h3>
        <canvas id="deptChart"></canvas>
    </div>

</div>

<!-- LOWER GRID (lightweight lists only) -->
<div class="dashboard-grid">

<!-- EMPLOYEE STATUS -->
    <div class="card">
        <h3>Employee Status</h3>
        <ul class="status-list">
            <li><span>Active</span> <strong>{{ \App\Models\Attendance::whereDate('date', now())->count() }}</strong></li>
            <li><span>Total Employees</span> <strong>{{ \App\Models\Employee::count() }}</strong></li>
            <li><span>Departments</span> <strong>{{ \App\Models\Department::count() }}</strong></li>
        </ul>
    </div>

    <!-- SCHEDULE -->
    <div class="card">
        <h3>Today's Schedule</h3>
        <div class="list-item">
            <span>Team Meeting</span>
            <small>10:00 AM</small>
        </div>
        <div class="list-item">
            <span>Payroll Review</span>
            <small>2:00 PM</small>
        </div>
    </div>

    <!-- PAYROLL SNAPSHOT -->
    <div class="card">
        <h3>Recent Payroll</h3>
        @foreach(\App\Models\Payroll::latest()->take(5)->get() as $pay)
            <div class="list-item">
                <span>{{ $pay->pay_date?->format('M d, Y') }}</span>
                <strong>${{ number_format($pay->net_salary, 2) }}</strong>
            </div>
        @endforeach
    </div>

</div>

<!-- CHART SCRIPT -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const attendanceData = @json(
    \App\Models\Attendance::selectRaw('DATE(date) as day, COUNT(*) as total')
        ->groupBy('day')->orderBy('day')->take(7)->get()
);

const deptData = @json(
    \App\Models\Department::withCount('employees')->get()
);

// Attendance Chart
new Chart(document.getElementById('attendanceChart'), {
    type: 'line',
    data: {
        labels: attendanceData.map(d => d.day),
        datasets: [{
            label: 'Attendance',
            data: attendanceData.map(d => d.total),
            borderWidth: 2,
            tension: 0.4
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});

// Department Chart
new Chart(document.getElementById('deptChart'), {
    type: 'bar',
    data: {
        labels: deptData.map(d => d.dept_name),
        datasets: [{
            label: 'Employees',
            data: deptData.map(d => d.employees_count),
            borderWidth: 1
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});
</script>

@endsection

