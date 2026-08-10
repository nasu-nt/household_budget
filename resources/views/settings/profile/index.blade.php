<x-app-layout>
    <main class="settings-page">
        @if (session('success'))
            <div
                class="toast toast--success"
                data-toast
                role="status"
                aria-live="polite"
            >
                <span>{{ session('success') }}</span>

                <button
                    type="button"
                    data-toast-close
                    aria-label="{{ __('Close') }}"
                >
                    ×
                </button>
            </div>
        @endif
        <div class="settings-page__layout">
            <aside class="settings-page__sidebar">
                @include('settings.partials.settings-menu')
            </aside>

        <div class="settings-page__main">
            <div class="settings-profile__header">
                <h1 class="settings-page__title">
                    {{ __('Account Settings') }}
                </h1>

                @if ($user->isDemoAccount())
                    <div
                        class="settings-profile__demo"
                        tabindex="0"
                    >
                        <span class="settings-profile__demo-trigger">
                            <img
                                class="settings-profile__demo-icon"
                                src="{{ asset('images/icons/Information_1.svg') }}"
                                alt=""
                                aria-hidden="true"
                            >

                            <span class="settings-profile__demo-label">
                                {{ __('Demo Account') }}
                            </span>
                        </span>

                        <div
                            class="settings-profile__demo-tooltip"
                            role="tooltip"
                        >
                            <p>
                                {{ __('Some account settings are disabled for the demo account.') }}
                            </p>
                            <p>
                                {{ __('Email and password changes are not available.') }}
                            </p>
                            <p>
                                {{ __('The demo account cannot be deleted.') }}
                            </p>
                        </div>
                    </div>
                @endif
            </div>

                <div class="settings-page__sections">
                    {{-- Profile Information --}}
                    <div class="settings-card settings-card--profile">
                        @include('settings.profile.partials.update-profile-information-form')
                    </div>

                    {{-- Update Password --}}
                    <div class="settings-card settings-card--password">
                        @include('settings.profile.partials.update-password-form')
                    </div>

                    {{-- Delete Account --}}
                    <div class="settings-page__danger-zone">
                        <div class="settings-card settings-card--delete">
                            @include('settings.profile.partials.delete-user-form')
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </main>
</x-app-layout>