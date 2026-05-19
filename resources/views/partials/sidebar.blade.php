<aside class="sidebar">
    <!-- Brand / Logo -->
    <div class="sidebar-brand">
        <div class="logo-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
            </svg>
        </div>
        <span class="brand-text">HRMS</span>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <ul class="menu">

            <li>
                <a href="{{ route('dashboard') }}"
                   class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2 7-7 7 7 2 2"></path>
                    </svg>
                    Dashboard
                </a>
            </li>

            <li>
                <a href="{{ route('employees.index') }}"
                   class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Employees
                </a>
            </li>

            <li>
                <a href="{{ route('attendances.index') }}"
                   class="{{ request()->routeIs('attendances.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Attendance
                </a>
            </li>

            <li>
                <a href="{{ route('departments.index') }}"
                   class="{{ request()->routeIs('departments.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4m14 0h-6"></path>
                    </svg>
                    Departments
                </a>
            </li>

@if(auth()->user()->isAdmin())

<li>
                <a href="{{ route('positions.index') }}"
                   class="{{ request()->routeIs('positions.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Positions
                </a>
            </li>

<li>
                <a href="{{ route('payrolls.index') }}"
                   class="{{ request()->routeIs('payrolls.index') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Payroll
                </a>
            </li>

            @endif

@if(!auth()->user()->isAdmin())
            <li>
                <a href="{{ route('payrolls.index') }}"
                   class="{{ request()->routeIs('payrolls.index') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Payroll
                </a>
            </li>
            <li>
                <a href="{{ route('leave-requests.index') }}"
                   class="{{ request()->routeIs('leave-requests.index') || request()->routeIs('leave-requests.create') || request()->routeIs('leave-requests.show') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    My Leave Requests
                </a>
            </li>
@endif

@if(auth()->user()->isAdmin())

            <li>
                <a href="{{ route('leave-requests.all') }}"
                   class="{{ request()->routeIs('leave-requests.all') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    All Leave Requests
                </a>
            </li>

@endif

            <li>
                <a href="{{ route('profile.edit') }}"
                   class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 12c2.5 0 4-1.5 4-3s-1.5-3-4-3-4 1.5-4 3 1.5 3 4 3z"/>
                    </svg>
                    Profile
                </a>
            </li>

        </ul>
    </nav>

    <!-- Footer -->
    <div class="sidebar-footer">

        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr(Auth::user()->name ?? '', 0, 2)) }}
            </div>

            <div class="user-details">
                <span class="user-name">
                    {{ Auth::user()->name ?? 'User' }}
                </span>

                <span class="user-role">
                    {{ auth()->user()->isAdmin() ? 'Admin' : 'Employee' }}
                </span>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="logout-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7"></path>
                </svg>

                Logout
            </button>
        </form>

    </div>
</aside>
