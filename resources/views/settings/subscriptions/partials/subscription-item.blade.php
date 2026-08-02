@php
    $hasUpdateErrors = $errors->updateSubscription->isNotEmpty()
        && (int) old('subscription_id') === $subscription->id;

    $subscriptionName = $hasUpdateErrors
        ? old('name', $subscription->name)
        : $subscription->name;

    $subscriptionAmount = (int) ($hasUpdateErrors
        ? old('amount', $subscription->amount)
        : $subscription->amount);

    $subscriptionCategoryId = (int) ($hasUpdateErrors
        ? old('category_id', $subscription->category_id)
        : $subscription->category_id);

    $oldEndOfMonth = $hasUpdateErrors
        ? old('is_end_of_month')
        : null;

    $isEndOfMonth = $oldEndOfMonth === null
        ? $subscription->is_end_of_month
        : (bool) ((int) $oldEndOfMonth);

    $billingDay = (int) ($hasUpdateErrors
        ? old('billing_day', $subscription->billing_day ?? 27)
        : ($subscription->billing_day ?? 27));

    $statusLabel = $subscription->is_active
        ? __('Active')
        : __('Disabled');

    $billingDayLabel = $subscription->is_end_of_month
        ? __('End of month')
        : __('Day :day', ['day' => $subscription->billing_day]);
@endphp

<article
    class="subscription-item"
    role="listitem"
    x-data="{
        isEditing: @js($hasUpdateErrors),
        initialIsEndOfMonth: @js($subscription->is_end_of_month),
        isEndOfMonth: @js($isEndOfMonth),
        openEditor() {
            this.isEditing = true;
            this.$nextTick(() => this.$refs.name.focus());
        },
        cancelEditor() {
            this.$refs.form.reset();
            this.isEndOfMonth = this.initialIsEndOfMonth;
            this.isEditing = false;
        }
    }"
>
    <div
        class="subscription-item__summary"
        x-show="! isEditing"
        @if ($hasUpdateErrors) x-cloak @endif
    >
        <span class="subscription-item__name">
            {{ $subscription->name }}
        </span>

        <span class="subscription-item__category">
            {{ $subscription->category?->name ?? __('Uncategorized') }}
        </span>

        <span class="subscription-item__amount">
            ¥{{ number_format($subscription->amount) }}
        </span>

        <span class="subscription-item__billing-day">
            {{ $billingDayLabel }}
        </span>

        <span class="subscription-item__status">
            {{ $statusLabel }}
        </span>

        <button
            type="button"
            class="subscription-item__edit-button"
            x-on:click="openEditor"
            x-bind:aria-expanded="isEditing.toString()"
            aria-controls="subscription-editor-{{ $subscription->id }}"
        >
            {{ __('Edit') }}
        </button>
    </div>

    <form
        id="subscription-editor-{{ $subscription->id }}"
        method="post"
        action="{{ route(
            'settings.subscriptions.update',
            $subscription
        ) }}"
        class="subscription-editor"
        x-ref="form"
        x-show="isEditing"
        x-cloak
    >
        @csrf
        @method('patch')

        <input
            type="hidden"
            name="subscription_id"
            value="{{ $subscription->id }}"
        >

        <div class="subscription-editor__fields">
            <div
                class="subscription-editor__field
                    subscription-editor__field--name"
            >
                <label
                    for="subscription_name_{{ $subscription->id }}"
                    class="subscription-editor__label"
                >
                    {{ __('Name') }}
                </label>

                <input
                    id="subscription_name_{{ $subscription->id }}"
                    name="name"
                    type="text"
                    class="subscription-editor__input
                        @if ($hasUpdateErrors && $errors->updateSubscription->has('name'))
                            is-invalid
                        @endif"
                    value="{{ $subscriptionName }}"
                    required
                    maxlength="100"
                    autocomplete="off"
                    x-ref="name"
                    @if ($hasUpdateErrors && $errors->updateSubscription->has('name'))
                        aria-invalid="true"
                        aria-describedby="subscription-name-error-{{ $subscription->id }}"
                    @endif
                >

                @if ($hasUpdateErrors)
                    @error('name', 'updateSubscription')
                        <p
                            id="subscription-name-error-{{ $subscription->id }}"
                            class="subscription-editor__error"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                @endif
            </div>

            <div
                class="subscription-editor__field
                    subscription-editor__field--amount"
            >
                <label
                    for="subscription_amount_{{ $subscription->id }}"
                    class="subscription-editor__label"
                >
                    {{ __('Price (¥)') }}
                </label>

                <div class="money-input">
                    <span
                        class="money-input__currency"
                        aria-hidden="true"
                    >
                        ¥
                    </span>

                    <input
                        id="subscription_amount_{{ $subscription->id }}"
                        name="amount"
                        type="text"
                        class="subscription-editor__input money-input__field
                            @if ($hasUpdateErrors && $errors->updateSubscription->has('amount'))
                                is-invalid
                            @endif"
                        value="{{ number_format($subscriptionAmount) }}"
                        required
                        inputmode="numeric"
                        autocomplete="off"
                        maxlength="13"
                        pattern="[0-9,]*"
                        data-max-digits="10"
                        data-money-input
                        @if ($hasUpdateErrors && $errors->updateSubscription->has('amount'))
                            aria-invalid="true"
                            aria-describedby="subscription-amount-error-{{ $subscription->id }}"
                        @endif
                    >
                </div>

                @if ($hasUpdateErrors)
                    @error('amount', 'updateSubscription')
                        <p
                            id="subscription-amount-error-{{ $subscription->id }}"
                            class="subscription-editor__error"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                @endif
            </div>

            <div class="subscription-editor__save">
                <button
                    type="submit"
                    name="intent"
                    value="save"
                    class="subscription-editor__button
                        subscription-editor__button--save"
                >
                    {{ __('Save') }}
                </button>
            </div>

            <div
                class="subscription-editor__field
                    subscription-editor__field--category"
            >
                <label
                    for="subscription_category_{{ $subscription->id }}"
                    class="subscription-editor__label"
                >
                    {{ __('Category') }}
                </label>

                <select
                    id="subscription_category_{{ $subscription->id }}"
                    name="category_id"
                    class="subscription-editor__select
                        @if ($hasUpdateErrors && $errors->updateSubscription->has('category_id'))
                            is-invalid
                        @endif"
                    required
                    @if ($hasUpdateErrors && $errors->updateSubscription->has('category_id'))
                        aria-invalid="true"
                        aria-describedby="subscription-category-error-{{ $subscription->id }}"
                    @endif
                >
                    @foreach ($categories as $category)
                        @continue(
                            $category->archived_at !== null
                            && $category->id !== $subscription->category_id
                        )

                        <option
                            value="{{ $category->id }}"
                            @selected($subscriptionCategoryId === $category->id)
                        >
                            {{ $category->name }}
                            @if (! $category->is_active)
                                ({{ __('Disabled') }})
                            @endif
                        </option>
                    @endforeach
                </select>

                @if ($hasUpdateErrors)
                    @error('category_id', 'updateSubscription')
                        <p
                            id="subscription-category-error-{{ $subscription->id }}"
                            class="subscription-editor__error"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                @endif
            </div>

            <fieldset
                class="subscription-editor__field
                    subscription-editor__field--billing"
            >
                <legend class="subscription-editor__label">
                    {{ __('Billing Day') }}
                </legend>

                <div class="subscription-billing">
                    <label class="subscription-billing__radio">
                        <input
                            type="radio"
                            name="is_end_of_month"
                            value="1"
                            @checked($isEndOfMonth)
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
                            @checked(! $isEndOfMonth)
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
                            id="subscription_billing_day_{{ $subscription->id }}"
                            name="billing_day"
                            class="subscription-form__select
                                subscription-billing__select
                                @if ($hasUpdateErrors && $errors->updateSubscription->has('billing_day'))
                                    is-invalid
                                @endif"
                            x-bind:disabled="isEndOfMonth"
                            aria-label="{{ __('Specific billing day') }}"
                            @if ($hasUpdateErrors && $errors->updateSubscription->has('billing_day'))
                                aria-invalid="true"
                                aria-describedby="subscription-billing-day-error-{{ $subscription->id }}"
                            @endif
                        >
                            @for ($day = 1; $day <= 31; $day++)
                                <option
                                    value="{{ $day }}"
                                    @selected($billingDay === $day)
                                >
                                    {{ $day }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                @if ($hasUpdateErrors)
                    @error('is_end_of_month', 'updateSubscription')
                        <p
                            class="subscription-editor__error"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror

                    @error('billing_day', 'updateSubscription')
                        <p
                            id="subscription-billing-day-error-{{ $subscription->id }}"
                            class="subscription-editor__error"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                @endif
            </fieldset>
        </div>

        <div class="subscription-editor__actions">
            <p class="subscription-editor__status">
                {{ __('Status: :status', ['status' => $statusLabel]) }}
            </p>

            <div class="subscription-editor__action-buttons">
                <button
                    type="submit"
                    name="intent"
                    value="{{ $subscription->is_active ? 'disable' : 'enable' }}"
                    class="subscription-editor__button
                        subscription-editor__button--status"
                    formnovalidate
                >
                    {{ $subscription->is_active ? __('Disable') : __('Enable') }}
                </button>

                <button
                    type="submit"
                    name="intent"
                    value="archive"
                    class="subscription-editor__button
                        subscription-editor__button--archive"
                    formnovalidate
                    x-on:click="
                        if (! confirm(@js(__('Archive this subscription?')))) {
                            $event.preventDefault();
                        }
                    "
                >
                    {{ __('Archive') }}
                </button>

                <button
                    type="button"
                    class="subscription-editor__button
                        subscription-editor__button--cancel"
                    x-on:click="cancelEditor"
                >
                    {{ __('Cancel') }}
                </button>
            </div>
        </div>
    </form>
</article>