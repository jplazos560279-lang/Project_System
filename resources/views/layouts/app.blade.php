<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="dashboard">
        @include('partials.sidebar')

        <div class="main-content">
            @include('partials.topbar')

            <div class="content-body">
                @hasSection('content')
                    @yield('content')
                @else
                    {{-- $slot was causing errors; keep empty fallback --}}
                @endif
            </div>
        </div>
    </div>      

    {{-- Custom Confirmation Modal --}}
    <div class="modal-overlay" id="confirmModal" style="display:none;">
        <div class="modal-card">
            <h3 class="modal-title">Confirm Action</h3>
            <p class="modal-message" id="confirmMessage">Are you sure?</p>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" id="confirmCancel">Cancel</button>
                <button type="button" class="btn-primary" id="confirmOk" style="background:var(--color-danger);">Delete</button>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const modal = document.getElementById('confirmModal');
            const messageEl = document.getElementById('confirmMessage');
            const cancelBtn = document.getElementById('confirmCancel');
            const okBtn = document.getElementById('confirmOk');
            let currentForm = null;

            document.querySelectorAll('[data-confirm]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    // If already confirmed, allow submit
                    if (this.dataset.confirmed === 'true') {
                        return;
                    }

                    // Block initial submit and show modal
                    e.preventDefault();
                    currentForm = this;
                    messageEl.textContent = this.dataset.confirm || 'Are you sure?';
                    modal.style.display = 'flex';
                });
            });


            cancelBtn.addEventListener('click', function() {
                modal.style.display = 'none';
                currentForm = null;
            });

            okBtn.addEventListener('click', function() {
                if (!currentForm) {
                    modal.style.display = 'none';
                    return;
                }

                currentForm.dataset.confirmed = 'true';

                // Make sure the click actually triggers a native form submit
                // (submit handlers on the same form may rely on a real submit event)
                if (typeof currentForm.requestSubmit === 'function') {
                    currentForm.requestSubmit();
                } else {
                    currentForm.submit();
                }

                modal.style.display = 'none';
                currentForm = null;
            });

            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                    currentForm = null;
                }
            });
        })();
    </script>
    <script>
        document.addEventListener('click', function (e) {
            const a = e.target.closest('a');
            if (!a) return;
            const href = a.getAttribute('href');
            if (!href) return;

            // If user clicks the payroll create link, ensure browser navigation happens normally.
            // This prevents any custom form-confirm modal logic from interfering.
            if (href.includes('/payrolls/create')) {
                modal.style.display = 'none';
            }
        });
    </script>
</body>
</html>

