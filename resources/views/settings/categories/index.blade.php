<x-app-layout>
    <main class="settings-page">
        <div class="settings-page__layout">
            <aside class="settings-page__sidebar">
                @include('settings.partials.settings-menu')
            </aside>

            <div class="settings-page__main">
                <h1 class="settings-page__title">
                    {{ __('Category Settings') }}
                </h1>

                <div class="settings-card">
                    {{ __('Category settings will be implemented here.') }}
                </div>
            </div>
        </div>
    </main>
</x-app-layout>