<header class="topbar">
    <div class="topbar-left">
        <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
    </div>
    <div class="topbar-right">
        <div class="datetime" style="margin-right: 16px; color: var(--color-text-secondary); font-size: 14px;">
            <span id="ph-time">Loading...</span>
        </div>
        <script>
            function updatePHTime() {
                const now = new Date().toLocaleString('en-US', {
                    timeZone: 'Asia/Manila',
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true,
                    second: '2-digit'
                });
                document.getElementById('ph-time').textContent = now;
            }
            updatePHTime();
            setInterval(updatePHTime, 1000 * 30); // Update every 30s
        </script>
        <div class="profile-logo" style="width: 32px; height: 32px; border-radius: 50%; background: var(--color-brand); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px; margin-right: 8px;">
            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 2)) }}
        </div>
    </div>
</header>
