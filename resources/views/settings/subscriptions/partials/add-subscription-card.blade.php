@php
    $hasStoreErrors = $errors->storeSubscription->isNotEmpty();

    $newIsEndOfMonth = $hasStoreErrors
        ? (bool) ((int) old('is_end_of_month', 1))
        : true;

    $newBillingDay = (int) old('billing_day', 27);

    $activeCategories = $categories->filter(
        fn ($category) => $category->is_active
            && $category->archived_at === null
    );
@endphp

<section
    class="settings-card subscription-settings-card"
    aria-labelledby="add-subscription-title"
>
    <header class="settings-card__header">
        <h2
            id="add-subscription-title"
            class="settings-card__title"
        >
            {{ __('Add New Subscription') }}
        </h2>

        <p class="settings-card__description">
            {{ __('Add a monthly recurring payment.') }}
        </p>
    </header>

    <form
        method="post"
        action="{{ route('settings.subscriptions.store') }}"
        class="settings-form subscription-form"
        x-data="{
            isEndOfMonth: @js($newIsEndOfMonth)
        }"
    >
        @csrf

        <div class="settings-form__fields">
            <div class="settings-form__row">
                <label
                    for="new_subscription_name"
                    class="settings-form__label"
                >
                    {{ __('Name') }}
                </label>

                <div class="settings-form__control subscription-form__main-control">
                    <input
                        id="new_subscription_name"
                        name="name"
                        type="text"
                        class="settings-form__input
                            @error('name', 'storeSubscription') is-invalid @enderror"
                        value="{{ $hasStoreErrors ? old('name') : '' }}"
                        required
                        maxlength="100"
                        autocomplete="off"
                        @error('name', 'storeSubscription')
                            aria-invalid="true"
                            aria-describedby="new-subscription-name-error"
                        @enderror
                    >

                    @error('name', 'storeSubscription')
                        <p
                            id="new-subscription-name-error"
                            class="settings-form__error"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div class="settings-form__row">
                <label
                    for="new_subscription_amount"
                    class="settings-form__label"
                >
                    {{ __('Price (¥)') }}
                </label>

                <div class="settings-form__control subscription-form__main-control">
                    <div class="money-input">
                        <span
                            class="money-input__currency"
                            aria-hidden="true"
                        >
                            ¥
                        </span>

                        <input
                            id="new_subscription_amount"
                            name="amount"
                            type="text"
                            class="settings-form__input money-input__field
                                @error('amount', 'storeSubscription') is-invalid @enderror"
                            value="{{ $hasStoreErrors ? old('amount') : '' }}"
                            required
                            inputmode="numeric"
                            autocomplete="off"
                            maxlength="13"
                            pattern="[0-9,]*"
                            data-max-digits="10"
                            data-money-input
                            @error('amount', 'storeSubscription')
                                aria-invalid="true"
                                aria-describedby="new-subscription-amount-error"
                            @enderror
                        >
                    </div>

                    @error('amount', 'storeSubscription')
                        <p
                            id="new-subscription-amount-error"
                            class="settings-form__error"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div class="settings-form__row">
                <label
                    for="new_subscription_category"
                    class="settings-form__label"
                >
                    {{ __('Category') }}
                </label>

                <div class="settings-form__control subscription-form__main-control">
                    <div class="subscription-form__select-wrapper">
                        <select
                            id="new_subscription_category"
                            name="category_id"
                            class="subscription-form__select
                                @error('category_id', 'storeSubscription') is-invalid @enderror"
                            required
                            @error('category_id', 'storeSubscription')
                                aria-invalid="true"
                                aria-describedby="new-subscription-category-error"
                            @enderror
                        >
                            <option value="">
                                {{ __('Select category') }}
                            </option>

                            @foreach ($activeCategories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    @selected(
                                        $hasStoreErrors
                                        && (int) old('category_id') === $category->id
                                    )
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @error('category_id', 'storeSubscription')
                        <p
                            id="new-subscription-category-error"
                            class="settings-form__error"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror

                    @if ($activeCategories->isEmpty())
                        <p class="subscription-form__help">
                            {{ __('Create or enable a category before adding a subscription.') }}
                        </p>
                    @endif
                </div>
            </div>

            <div
                class="settings-form__row subscription-form__billing-row"
                role="group"
                aria-labelledby="new-subscription-billing-label"
            >
                <span
                    id="new-subscription-billing-label"
                    class="settings-form__label"
                >
                    {{ __('Billing Day') }}
                </span>

                <div class="settings-form__control">
                    <div class="subscription-billing">
                        <label class="subscription-billing__radio">
                            <input
                                type="radio"
                                name="is_end_of_month"
                                value="1"
                                @checked($newIsEndOfMonth)
                                x-bind:checked="isEndOfMonth"
                                x-on:change="isEndOfMonth = true"
                            >

                            <span>{{ __('End of month') }}</span>
                        </label>

                        <label class="subscription-billing__radio">
                            <input
                                type="radio"
                                name="is_end_of_month"
                                value="0"
                                @checked(! $newIsEndOfMonth)
                                x-bind:checked="! isEndOfMonth"
                                x-on:change="isEndOfMonth = false"
                            >

                            <span>{{ __('Specific day') }}</span>
                        </label>

                        <div
                            class="subscription-form__select-wrapper
                                subscription-billing__day-select-wrapper"
                            x-bind:class="{
                                'is-disabled': isEndOfMonth
                            }"
                        >
                            <select
                                id="new_subscription_billing_day"
                                name="billing_day"
                                class="subscription-form__select
                                    subscription-billing__select
                                    @error('billing_day', 'storeSubscription') is-invalid @enderror"
                                x-bind:disabled="isEndOfMonth"
                                aria-label="{{ __('Specific billing day') }}"
                                @error('billing_day', 'storeSubscription')
                                    aria-invalid="true"
                                    aria-describedby="new-subscription-billing-day-error"
                                @enderror
                            >
                                @for ($day = 1; $day <= 31; $day++)
                                    <option
                                        value="{{ $day }}"
                                        @selected($newBillingDay === $day)
                                    >
                                        {{ $day }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    @error('is_end_of_month', 'storeSubscription')
                        <p
                            class="settings-form__error"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror

                    @error('billing_day', 'storeSubscription')
                        <p
                            id="new-subscription-billing-day-error"
                            class="settings-form__error"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="settings-form__actions">
            <div class="settings-form__action-content">
                <button
                    type="submit"
                    class="settings-form__button
                        subscription-form__submit"
                    @disabled($activeCategories->isEmpty())
                >
                    {{ __('Add Subscription') }}
                </button>
            </div>
        </div>
    </form>
</section>