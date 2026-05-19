<x-guest-layout>
    <div class="login-container">

        <div class="login-card">
            <h2>Verify Email</h2>
            <p class="subtitle">
                Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
            </p>

            @if (session('status') == 'verification-link-sent')
                <div class="alert success">
                    A new verification link has been sent to the email address you provided during registration.
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <button type="submit" class="btn-login">Resend Verification Email</button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf

                <button type="submit" class="btn-login btn-muted">
                    Log Out
                </button>
            </form>
        </div>
</x-guest-layout>
