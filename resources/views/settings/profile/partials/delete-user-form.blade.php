<section class="settings-delete">
    <header class="settings-card__header">
        <h2 class="settings-card__title">
            {{ __('Delete Account') }}
        </h2>

        <p class="settings-card__description">
            {{ __('Once your account is deleted, all data will be permanently removed.') }}
            <br>
            {{ __('Please download any data you want to keep before deleting your account.') }}
        </p>
    </header>

    <div class="profile-delete__actions">
        <button
            type="button"
            class="profile-delete__button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            @if ($user->isDemoAccount())
                disabled
            @endif
        >
            {{ __('Delete account') }}
        </button>
    </div>

    <x-modal
        name="confirm-user-deletion"
        :show="$errors->userDeletion->isNotEmpty()"
        focusable
    >
        <form
            method="post"
            action="{{ route('settings.profile.destroy') }}"
            class="profile-delete__modal"
        >
            @csrf
            @method('delete')

            <h2 class="profile-delete__modal-title">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="profile-delete__modal-description">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
                <br>
                {{ __('Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="profile-delete__modal-field">
                <label
                    for="delete_account_password"
                    class="sr-only"
                >
                    {{ __('Password') }}
                </label>

                <input
                    id="delete_account_password"
                    name="password"
                    type="password"
                    class="settings-form__input profile-delete__modal-input
                        @error('password', 'userDeletion') is-invalid @enderror"
                    placeholder="{{ __('Password') }}"
                    autocomplete="current-password"
                    @error('password', 'userDeletion')
                        aria-invalid="true"
                        aria-describedby="delete-account-password-error"
                    @enderror
                >

                @error('password', 'userDeletion')
                    <p
                        id="delete-account-password-error"
                        class="settings-form__error"
                        role="alert"
                    >
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="profile-delete__modal-actions">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="s">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>