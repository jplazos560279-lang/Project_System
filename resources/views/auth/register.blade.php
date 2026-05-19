<x-guest-layout>
    <div class="login-container">

        <div class="login-card">
            <h2>Register</h2>
            <p class="subtitle">Create your account.</p>

<form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required>
                    @error('name')<p class="error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
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

                <button type="submit" class="btn-login">Register</button>
            </form>

            <p class="register-link">
                Already have an account?
                <a href="{{ route('login') }}">Log in</a>
            </p>

            <div class="back-link">
                <a href="{{ url('/') }}" class="btn-back">← Back to Home</a>
            </div>
        </div>

    </div>
</x-guest-layout>
