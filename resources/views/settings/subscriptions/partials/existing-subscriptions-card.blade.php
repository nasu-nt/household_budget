<section
    class="settings-card subscription-settings-card"
    aria-labelledby="existing-subscriptions-title"
>
    <header class="settings-card__header">
        <h2
            id="existing-subscriptions-title"
            class="settings-card__title"
        >
            {{ __('Existing Subscriptions') }}
        </h2>

        <p class="settings-card__description">
            {{ __('View and manage your saved subscriptions.') }}
        </p>
    </header>

    <div class="subscription-list">
        <div
            class="subscription-list__header"
            aria-hidden="true"
        >
            <span>{{ __('Name') }}</span>
            <span>{{ __('Category') }}</span>
            <span>{{ __('Price') }}</span>
            <span>{{ __('Billing Day') }}</span>
            <span>{{ __('Status') }}</span>
            <span></span>
        </div>

        <div
            class="subscription-list__items"
            role="list"
        >
            @forelse ($subscriptions as $subscription)
                @include(
                    'settings.subscriptions.partials.subscription-item',
                    [
                        'subscription' => $subscription,
                        'categories' => $categories,
                    ]
                )
            @empty
                <p class="subscription-list__empty">
                    {{ __('No subscriptions have been added yet.') }}
                </p>
            @endforelse
        </div>
    </div>
</section>
