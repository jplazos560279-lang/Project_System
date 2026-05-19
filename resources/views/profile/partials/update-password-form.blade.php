<section>
    <header style="margin-bottom: 20px;">
        <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 6px;">
            {{ __('Update Password') }}
        </h3>
        <p style="font-size: var(--text-sm); color: var(--color-text-secondary);">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="form-group">
            <label for="update_password_current_password">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password">
            @error('current_password', 'updatePassword')<p class="error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label for="update_password_password">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password">
            @error('password', 'updatePassword')<p class="error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label for="update_password_password_confirmation">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')<p class="error">{{ $message }}</p>@enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">{{ __('Save') }}</button>

            @if (session('status') === 'password-updated')
                <span style="font-size: var(--text-sm); color: var(--color-text-secondary);">{{ __('Saved.') }}</span>
            @endif
        </div>
    </form>
</section>

