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

    <div class="settings-delete__actions">
        <button
            type="button"
            class="settings-delete__button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
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
            class="p-6"
        >
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="mt-6">
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
                    class="settings-form__input settings-delete__modal-input
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

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    {{ __('Delete Account') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>