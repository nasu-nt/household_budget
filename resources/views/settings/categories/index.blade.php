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
                    {{ __('Category Settings') }}
                </h1>

                <div class="settings-page__sections">
                    @include(
                        'settings.categories.partials.add-category-card'
                    )

                    @include(
                        'settings.categories.partials.existing-categories-card',
                        ['categories' => $categories]
                    )
                </div>
            </div>
        </div>
    </main>
</x-app-layout>
