<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HRMS</title>

@vite(['resources/css/welcome.css'])
</head>
<body class="welcome-page">

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="container nav-content">

            <div class="logo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">

                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>

                </svg>

                HRMS
            </div>

            <div class="nav-links">
                @if (Route::has('login'))

                    @auth
                        <a href="{{ url('/dashboard') }}">Dashboard</a>

                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="register-btn">
                                Register
                            </a>
                        @endif

                    @endauth
                @endif
            </div>

        </div>
    </nav>

    <!-- HERO -->
    <section class="hero">

        <div class="container hero-wrapper">

            <!-- LEFT -->
            <div class="hero-content">

                <span class="hero-badge">
                    Smart HR Management
                </span>

                <h1>
                     Human Resource Management System
                </h1>

<p>
                    Manage employees, attendance, payroll, schedules,
                    departments, and projects in one clean and organized platform.
                </p>

                <div class="hero-buttons">
                    <a href="{{ route('login') }}" class="btn-primary">
                        Get Started
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-secondary">
                            Create Account
                        </a>
                    @endif
                </div>

            </div>

            <!-- RIGHT -->
            <div class="hero-image">

                <div class="dashboard-preview">

                    <div class="preview-top">
                        <div class="preview-circle"></div>
                        <div class="preview-circle"></div>
                        <div class="preview-circle"></div>
                    </div>

                    <div class="preview-content">

                        <div class="preview-sidebar"></div>

                        <div class="preview-main">

                            <div class="preview-cards">
                                <div class="mini-card"></div>
                                <div class="mini-card"></div>
                                <div class="mini-card"></div>
                            </div>

                            <div class="preview-chart"></div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <!-- FEATURES -->
    <section class="features-section">

        <div class="container">

            <div class="section-title">
                <h2>HRMS Features</h2>
                <p>
                    Everything you need to manage your employees and organization efficiently.
                </p>
            </div>

            <div class="features">

                <div class="card">
                    <div class="card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="32" height="32">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>

                    <h3>Employee Management</h3>

                    <p>
                        Manage employee records, departments,
                        job positions, and profiles efficiently.
                    </p>
                </div>

                <div class="card">
                    <div class="card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="32" height="32">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                            <path d="M8 14h.01"/>
                            <path d="M12 14h.01"/>
                            <path d="M16 14h.01"/>
                            <path d="M8 18h.01"/>
                            <path d="M12 18h.01"/>
                        </svg>
                    </div>

                    <h3>Attendance Tracking</h3>

                    <p>
                        Monitor attendance, working hours,
                        schedules, and employee activity in real time.
                    </p>
                </div>

                <div class="card">
                    <div class="card-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="32" height="32">
                            <rect x="2" y="5" width="20" height="14" rx="2" ry="2"/>
                            <line x1="2" y1="10" x2="22" y2="10"/>
                            <path d="M6 15h2"/>
                        </svg>
                    </div>

                    <h3>Payroll Processing</h3>

                    <p>
                        Handle salaries, deductions,
                        payroll reports, and employee compensation.
                    </p>
                </div>

            </div>

        </div>

    </section>

    <!-- FOOTER -->
    <footer class="footer">

        <div class="container">
            © {{ date('Y') }} HRMS. All rights reserved.
        </div>

    </footer>

</body>
</html>

