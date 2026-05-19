<section>
    <header style="margin-bottom: 20px;">
        <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 6px;">
            {{ __('Profile Information') }}
        </h3>
        <p style="font-size: var(--text-sm); color: var(--color-text-secondary);">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="form-group">
            <label for="name">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')<p class="error">{{ $message }}</p>@enderror
        </div>

        <div class="form-group">
            <label for="email">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')<p class="error">{{ $message }}</p>@enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div style="margin-top: 12px;">
                    <p style="font-size: var(--text-sm); color: var(--color-text-secondary);">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" style="background: none; border: none; color: var(--color-brand); text-decoration: underline; cursor: pointer; font-size: var(--text-sm); padding: 0;">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p style="margin-top: 8px; font-size: var(--text-sm); color: var(--color-success); font-weight: 500;">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">{{ __('Save') }}</button>

            @if (session('status') === 'profile-updated')
                <span style="font-size: var(--text-sm); color: var(--color-text-secondary);">{{ __('Saved.') }}</span>
            @endif
        </div>
    </form>
</section>

