<section class="settings-card category-settings-card">
    <header class="settings-card__header">
        <h2 class="settings-card__title">
            {{ __('Existing Categories') }}
        </h2>

        <p class="settings-card__description">
            {{ __('View and manage your saved categories.') }}
        </p>
    </header>

    <div class="category-list">
        <div
            class="category-list__header"
            aria-hidden="true"
        >
            <span>{{ __('Name') }}</span>
            <span>{{ __('Color') }}</span>
            <span>{{ __('Status') }}</span>
            <span></span>
        </div>

        <div
            class="category-list__items"
            role="list"
        >
            @forelse ($categories as $category)
                @include(
                    'settings.categories.partials.category-item',
                    ['category' => $category]
                )
            @empty
                <p class="category-list__empty">
                    {{ __('No categories have been added yet.') }}
                </p>
            @endforelse
        </div>
    </div>
</section>
