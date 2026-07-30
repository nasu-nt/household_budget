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
                <h1 class="settings-page__title">
                    {{ __('Subscription Settings') }}
                </h1>

                <div class="settings-page__sections">
                    @include(
                        'settings.subscriptions.partials.add-subscription-card'
                    )

                    @include(
                        'settings.subscriptions.partials.existing-subscriptions-card'
                    )
                </div>
            </div>
        </div>
    </main>
</x-app-layout>
