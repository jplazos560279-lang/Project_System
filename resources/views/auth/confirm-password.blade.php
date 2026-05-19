<x-guest-layout>
    <div class="login-container">

        <div class="login-card">
            <h2>Confirm Password</h2>
            <p class="subtitle">
                This is a secure area. Please confirm your password before continuing.
            </p>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required autocomplete="current-password">
                    @error('password')<p class="error">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="btn-login">Confirm</button>
            </form>
        </div>
</x-guest-layout>
