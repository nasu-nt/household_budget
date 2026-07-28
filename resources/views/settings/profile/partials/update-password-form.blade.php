<section>
    <header class="settings-card__header">
        <h2 class="settings-card__title">
            {{ __('Update Password') }}
        </h2>

        <p class="settings-card__description">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form
        method="post"
        action="{{ route('password.update') }}"
        class="settings-form"
    >
        @csrf
        @method('put')

        <div class="settings-form__fields">
            <div class="settings-form__row">
                <label
                    for="update_password_current_password"
                    class="settings-form__label"
                >
                    {{ __('Current Password') }}
                </label>

                <div class="settings-form__control">
                    <input
                        id="update_password_current_password"
                        name="current_password"
                        type="password"
                        class="settings-form__input
                            @error('current_password', 'updatePassword') is-invalid @enderror"
                        required
                        autocomplete="current-password"
                        data-error-target="current_password"
                        @error('current_password', 'updatePassword')
                            aria-invalid="true"
                            aria-describedby="current-password-error"
                        @enderror
                    >

                    @error('current_password', 'updatePassword')
                        <p
                            id="current-password-error"
                            class="settings-form__error"
                            data-error-message="current_password"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div class="settings-form__row">
                <label
                    for="update_password_password"
                    class="settings-form__label"
                >
                    {{ __('New Password') }}
                </label>

                <div class="settings-form__control">
                    <input
                        id="update_password_password"
                        name="password"
                        type="password"
                        class="settings-form__input
                            @error('password', 'updatePassword') is-invalid @enderror"
                        required
                        autocomplete="new-password"
                        data-error-target="password"
                        @error('password', 'updatePassword')
                            aria-invalid="true"
                            aria-describedby="new-password-error"
                        @enderror
                    >

                    @error('password', 'updatePassword')
                        <p
                            id="new-password-error"
                            class="settings-form__error"
                            data-error-message="password"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div class="settings-form__row">
                <label
                    for="update_password_password_confirmation"
                    class="settings-form__label"
                >
                    {{ __('Confirm Password') }}
                </label>

                <div class="settings-form__control">
                    <input
                        id="update_password_password_confirmation"
                        name="password_confirmation"
                        type="password"
                        class="settings-form__input
                            @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                        required
                        autocomplete="new-password"
                        data-error-target="password_confirmation"
                        @error('password_confirmation', 'updatePassword')
                            aria-invalid="true"
                            aria-describedby="password-confirmation-error"
                        @enderror
                    >

                    @error('password_confirmation', 'updatePassword')
                        <p
                            id="password-confirmation-error"
                            class="settings-form__error"
                            data-error-message="password_confirmation"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="settings-form__actions">
            <div class="settings-form__action-content">
                <button
                    type="submit"
                    class="settings-form__button"
                >
                    {{ __('Update Password') }}
                </button>

                @if (session('status') === 'password-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="settings-form__success"
                        role="status"
                        aria-live="polite"
                    >
                        {{ __('Password updated successfully.') }}
                    </p>
                @endif
            </div>
        </div>
    </form>
</section>