{{-- resources/views/dashboard/partials/_recurring-expenses-cta.blade.php --}}
<section
    class="subscription-cta"
    aria-labelledby="subscription-cta-title"
>
    <div class="subscription-cta__content">
        <div class="subscription-cta__heading">
            <img
                class="subscription-cta__icon"
                src="{{ asset('images/icons/subscription_2.svg') }}"
                alt=""
                aria-hidden="true"
            >

            <h2
                id="subscription-cta-title"
                class="subscription-cta__title"
            >
                {{ __('Subscriptions') }}
            </h2>
        </div>

        <a
            class="subscription-cta__link"
            href="{{ route('settings.subscriptions.index') }}"
        >
            {{ __('Manage subscriptions') }}
        </a>

        <p class="subscription-cta__description">
            {{ __('Set up subscriptions once and add them automatically every month.') }}
        </p>
    </div>
</section>
