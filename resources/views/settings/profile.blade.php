<x-app-layout>
    <main class="settings-page">
        <div class="settings-page__layout">
            <aside class="settings-page__sidebar">
                @include('settings.partials.settings-menu')
            </aside>

            <div class="settings-page__main">
                <h1 class="settings-page__title">
                    {{ __('Account Settings') }}
                </h1>

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