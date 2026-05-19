<x-guest-layout>
    <div class="login-container">

        <div class="login-card">
            <h2>Reset Password</h2>
            <p class="subtitle">Enter your new password below.</p>

            <!-- BACK BUTTON -->
            <a href="{{ route('login') }}" class="btn-back">← Back to Login</a>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus>
                    @error('email')<p class="error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                    @error('password')<p class="error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="password_confirmation" required>
                    @error('password_confirmation')<p class="error">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="btn-login">Reset Password</button>
            </form>
        </div>

    </div>
</x-guest-layout>

