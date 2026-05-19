<section>
    <header style="margin-bottom: 20px;">
        <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 6px;">
            {{ __('Delete Account') }}
        </h3>
        <p style="font-size: var(--text-sm); color: var(--color-text-secondary);">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button type="button" class="btn-delete" style="font-size: var(--text-sm); padding: 9px 16px; border: 1px solid var(--color-danger); border-radius: var(--radius-sm); background: transparent; cursor: pointer;" onclick="document.getElementById('delete-account-modal').style.display='block'">
        {{ __('Delete Account') }}
    </button>

    <div id="delete-account-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 200; align-items: center; justify-content: center;">
        <div style="background: var(--color-surface); padding: 24px; border-radius: var(--radius-md); max-width: 420px; width: 90%; box-shadow: var(--shadow-lg); border: 1px solid var(--color-border);">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <h3 style="font-size: var(--text-lg); font-weight: 600; color: var(--color-text-primary); margin-bottom: 8px;">
                    {{ __('Are you sure you want to delete your account?') }}
                </h3>

                <p style="font-size: var(--text-sm); color: var(--color-text-secondary); margin-bottom: 20px;">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>

                <div class="form-group">
                    <label for="delete-password">{{ __('Password') }}</label>
                    <input id="delete-password" name="password" type="password" placeholder="{{ __('Password') }}">
                    @error('password', 'userDeletion')<p class="error">{{ $message }}</p>@enderror
                </div>

                <div class="form-actions" style="margin-top: 16px;">
                    <button type="button" class="btn-secondary" onclick="document.getElementById('delete-account-modal').style.display='none'">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn-delete" style="font-size: var(--text-sm); padding: 9px 16px; border: 1px solid var(--color-danger); border-radius: var(--radius-sm); background: var(--color-danger); color: white; cursor: pointer; margin-left: 10px;">
                        {{ __('Delete Account') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

