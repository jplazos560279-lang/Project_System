<x-guest-layout>
    <div class="login-container">

        <div class="login-card">
            <h2>Log in</h2>
            <p class="subtitle">Welcome back! Please enter your credentials.</p>

            @if(session('status'))
                <div class="alert success">{{ session('status') }}</div>
            @endif

            @if(session('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif

<form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="example@email.com" required autofocus>
                    @error('email')<p class="error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Password" required>
                    @error('password')<p class="error">{{ $message }}</p>@enderror
                </div>

                <div class="form-options">
                    <label>
                        <input type="checkbox" name="remember">
                        Remember me
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}">Forgot password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-login">Log in</button>
            </form>

            <p class="register-link">
                Don't have an account?
                <a href="{{ route('register') }}">Register</a>
            </p>

            <div class="back-link">
                <a href="{{ url('/') }}" class="btn-back">← Back to Home</a>
            </div>
        </div>

    </div>
</x-guest-layout>

