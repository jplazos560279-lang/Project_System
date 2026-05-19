<x-guest-layout>
    <div class="login-container">

        <div class="login-card">
            <h2>Forgot Password</h2>
            <p class="subtitle">
                Enter your email and we'll send you a reset link.
            </p>

            <!-- BACK BUTTON -->
            <a href="{{ route('login') }}" class="btn-back">← Back to Login</a>

            <!-- SUCCESS MESSAGE -->
            @if (session('status'))
                <div class="alert success">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus>
                    @error('email')<p class="error">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="btn-login">
                    Send Reset Link
                </button>
            </form>
        </div>
</x-guest-layout>
