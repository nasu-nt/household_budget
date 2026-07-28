<section>
    <header class="settings-card__header">
        <h2 class="settings-card__title">
            {{ __('Profile Information') }}
        </h2>

        <p class="settings-card__description">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form
        id="send-verification"
        method="post"
        action="{{ route('verification.send') }}"
    >
        @csrf
    </form>

    <form
        method="post"
        action="{{ route('settings.profile.update') }}"
        class="settings-form"
    >
        @csrf
        @method('patch')

        <div class="settings-form__fields">
            <div class="settings-form__row">
                <label
                    for="name"
                    class="settings-form__label"
                >
                    {{ __('Display Name') }}
                </label>

                <div class="settings-form__control">
                    <input
                        id="name"
                        name="name"
                        type="text"
                        class="settings-form__input
                            @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name) }}"
                        required
                        autofocus
                        autocomplete="name"
                        maxlength="255"
                        data-error-target="name"
                        @error('name')
                            aria-invalid="true"
                            aria-describedby="name-error"
                        @enderror
                    >

                    @error('name')
                        <p
                            id="name-error"
                            class="settings-form__error"
                            data-error-message="name"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div class="settings-form__row">
                <label
                    for="email"
                    class="settings-form__label"
                >
                    {{ __('Email') }}
                </label>

                <div class="settings-form__control">
                    <input
                        id="email"
                        name="email"
                        type="email"
                        class="settings-form__input
                            @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email) }}"
                        required
                        autocomplete="username"
                        maxlength="255"
                        data-error-target="email"
                        @error('email')
                            aria-invalid="true"
                            aria-describedby="email-error"
                        @enderror
                    >

                    @error('email')
                        <p
                            id="email-error"
                            class="settings-form__error"
                            data-error-message="email"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror

                    @if (
                        $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail
                        && ! $user->hasVerifiedEmail()
                    )
                        <div class="settings-form__verification">
                            <p>
                                {{ __('Your email address is unverified.') }}

                                <button
                                    type="submit"
                                    form="send-verification"
                                    class="settings-form__link-button"
                                >
                                    {{ __('Re-send verification email') }}
                                </button>
                            </p>

                            @if (session('status') === 'verification-link-sent')
                                <p
                                    class="settings-form__verification-status"
                                    role="status"
                                >
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="settings-form__actions">
            <div class="settings-form__action-content">
                <button
                    type="submit"
                    class="settings-form__button"
                >
                    {{ __('Update Profile') }}
                </button>

                @if (session('status') === 'profile-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="settings-form__success"
                        role="status"
                        aria-live="polite"
                    >
                        {{ __('Profile updated successfully.') }}
                    </p>
                @endif
            </div>
        </div>
    </form>
</section>