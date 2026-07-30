@php
    $oldEndOfMonth = old('is_end_of_month');

    $isEndOfMonth = $oldEndOfMonth === null
        ? $budgetSetting->is_end_of_month
        : (bool) ((int) $oldEndOfMonth);

    $monthlyBudget = (int) old(
        'monthly_budget',
        $budgetSetting->monthly_budget
    );

    $monthlyLimit = (int) old(
        'monthly_limit',
        $budgetSetting->monthly_limit
    );

    $closingDay = (int) old(
        'closing_day',
        $budgetSetting->closing_day ?? 27
    );

    $statusRows = [
        [
            'label' => __('All good'),
            'color' => $appearanceSetting->all_good_color,
            'daily' => __('Below 80% of the daily guideline'),
            'monthly' => __('Below 80% of the cumulative budget pace'),
        ],
        [
            'label' => __('Slightly high'),
            'color' => $appearanceSetting->slightly_high_color,
            'daily' => __('80%–100% of the daily guideline'),
            'monthly' => __('80%–100% of the cumulative budget pace'),
        ],
        [
            'label' => __('Over budget'),
            'color' => $appearanceSetting->over_budget_color,
            'daily' => __('Above the daily guideline, up to the daily limit'),
            'monthly' => __('Above the cumulative budget pace, up to the cumulative limit pace'),
        ],
        [
            'label' => __('Over limit'),
            'color' => $appearanceSetting->over_limit_color,
            'daily' => __('Above the daily limit'),
            'monthly' => __('Above the cumulative limit pace'),
        ],
    ];
@endphp

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
                    {{ __('Budget Settings') }}
                </h1>

                <div class="settings-page__sections">
                    <section
                        class="settings-card budget-settings-card"
                        aria-labelledby="budget-overview-title"
                    >
                        <header class="settings-card__header">
                            <h2
                                id="budget-overview-title"
                                class="settings-card__title"
                            >
                                {{ __('Budget Overview') }}
                            </h2>

                            <p class="settings-card__description">
                                {{ __('Set your monthly budget, spending limit, and closing day.') }}
                            </p>
                        </header>

                        <form
                            method="post"
                            action="{{ route('settings.budget.update') }}"
                            class="settings-form budget-form"
                            x-data="budgetSettingsForm(@js([
                                'monthlyBudget' => $monthlyBudget,
                                'monthlyLimit' => $monthlyLimit,
                                'isEndOfMonth' => $isEndOfMonth,
                                'closingDay' => $closingDay,
                            ]))"
                        >
                            @csrf
                            @method('patch')

                            <div class="settings-form__fields">
                                <div class="settings-form__row">
                                    <label
                                        for="monthly_budget_display"
                                        class="settings-form__label"
                                    >
                                        {{ __('Monthly Budget (¥)') }}
                                    </label>

                                    <div class="settings-form__control">
                                        <input
                                            type="hidden"
                                            name="monthly_budget"
                                            x-bind:value="monthlyBudget"
                                        >

                                        <input
                                            id="monthly_budget_display"
                                            type="text"
                                            class="settings-form__input
                                                budget-form__amount-input
                                                @error('monthly_budget')
                                                    is-invalid
                                                @enderror"
                                            x-model="monthlyBudgetInput"
                                            x-on:input="
                                                updateMonthlyBudget(
                                                    $event.target.value
                                                )
                                            "
                                            inputmode="numeric"
                                            autocomplete="off"
                                            maxlength="13"
                                            required
                                            @error('monthly_budget')
                                                aria-invalid="true"
                                                aria-describedby="monthly-budget-error"
                                            @enderror
                                        >

                                        @error('monthly_budget')
                                            <p
                                                id="monthly-budget-error"
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
                                        for="monthly_limit_display"
                                        class="settings-form__label"
                                    >
                                        {{ __('Spending Limit (¥)') }}
                                    </label>

                                    <div class="settings-form__control">
                                        <input
                                            type="hidden"
                                            name="monthly_limit"
                                            x-bind:value="monthlyLimit"
                                        >

                                        <input
                                            id="monthly_limit_display"
                                            type="text"
                                            class="settings-form__input
                                                budget-form__amount-input
                                                @error('monthly_limit')
                                                    is-invalid
                                                @enderror"
                                            x-model="monthlyLimitInput"
                                            x-on:input="
                                                updateMonthlyLimit(
                                                    $event.target.value
                                                )
                                            "
                                            inputmode="numeric"
                                            autocomplete="off"
                                            maxlength="13"
                                            required
                                            @error('monthly_limit')
                                                aria-invalid="true"
                                                aria-describedby="monthly-limit-error"
                                            @enderror
                                        >

                                        @error('monthly_limit')
                                            <p
                                                id="monthly-limit-error"
                                                class="settings-form__error"
                                                role="alert"
                                            >
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>

                                <fieldset
                                    class="settings-form__row
                                        budget-form__closing-row"
                                >
                                    <legend class="settings-form__label">
                                        {{ __('Closing Day') }}
                                    </legend>

                                    <div class="settings-form__control">
                                        <div
                                            class="budget-form__closing-controls"
                                        >
                                            <label
                                                class="budget-form__radio"
                                            >
                                                <input
                                                    type="radio"
                                                    name="is_end_of_month"
                                                    value="1"
                                                    x-bind:checked="
                                                        isEndOfMonth
                                                    "
                                                    x-on:change="
                                                        isEndOfMonth = true
                                                    "
                                                >

                                                <span>
                                                    {{ __('End of month') }}
                                                </span>
                                            </label>

                                            <label
                                                class="budget-form__radio"
                                            >
                                                <input
                                                    type="radio"
                                                    name="is_end_of_month"
                                                    value="0"
                                                    x-bind:checked="
                                                        ! isEndOfMonth
                                                    "
                                                    x-on:change="
                                                        isEndOfMonth = false
                                                    "
                                                >

                                                <span>
                                                    {{ __('Specific day') }}
                                                </span>
                                            </label>

                                            <select
                                                id="closing_day"
                                                name="closing_day"
                                                class="budget-form__closing-select
                                                    @error('closing_day')
                                                        is-invalid
                                                    @enderror"
                                                x-model.number="closingDay"
                                                x-bind:disabled="
                                                    isEndOfMonth
                                                "
                                                aria-label="{{ __('Specific closing day') }}"
                                                @error('closing_day')
                                                    aria-invalid="true"
                                                    aria-describedby="closing-day-error"
                                                @enderror
                                            >
                                                @for ($day = 1; $day <= 31; $day++)
                                                    <option
                                                        value="{{ $day }}"
                                                    >
                                                        {{ $day }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>

                                        @error('closing_day')
                                            <p
                                                id="closing-day-error"
                                                class="settings-form__error"
                                                role="alert"
                                            >
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </fieldset>
                            </div>

                            <section
                                class="budget-guide"
                                aria-labelledby="status-guide-title"
                            >
                                <div class="budget-guide__summary">
                                    <div class="budget-guide__metric">
                                        <h3 class="budget-guide__metric-title">
                                            {{ __('Daily Spending Guideline') }}
                                        </h3>

                                        <p class="budget-guide__metric-value">
                                            <strong
                                                x-text="
                                                    formatCurrency(
                                                        dailyGuideline
                                                    )
                                                "
                                            ></strong>

                                            <span>{{ __('per day') }}</span>
                                        </p>
                                    </div>

                                    <div class="budget-guide__metric">
                                        <h3 class="budget-guide__metric-title">
                                            {{ __('Daily Spending Limit') }}
                                        </h3>

                                        <p class="budget-guide__metric-value">
                                            <strong
                                                x-text="
                                                    formatCurrency(
                                                        dailyLimit
                                                    )
                                                "
                                            ></strong>

                                            <span>{{ __('per day') }}</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="budget-guide__status">
                                    <h3
                                        id="status-guide-title"
                                        class="budget-guide__status-title"
                                    >
                                        {{ __('Status guide') }}
                                    </h3>

                                    <div
                                        class="budget-guide__table"
                                        role="table"
                                        aria-label="{{ __('Spending status guide') }}"
                                    >
                                        <div
                                            class="budget-guide__table-header"
                                            role="row"
                                        >
                                            <span
                                                aria-hidden="true"
                                            ></span>
                                            <span
                                                aria-hidden="true"
                                            ></span>

                                            <strong role="columnheader">
                                                {{ __('Daily') }}
                                            </strong>

                                            <strong role="columnheader">
                                                {{ __('Monthly') }}
                                            </strong>
                                        </div>

                                        @foreach ($statusRows as $status)
                                            <div
                                                class="budget-guide__table-row"
                                                role="row"
                                            >
                                                <span
                                                    class="budget-guide__status-label"
                                                    role="rowheader"
                                                >
                                                    {{ $status['label'] }}
                                                </span>

                                                <span
                                                    class="budget-guide__swatch"
                                                    style="
                                                        --status-color:
                                                        {{ $status['color'] }}
                                                    "
                                                    aria-hidden="true"
                                                ></span>

                                                <span role="cell">
                                                    {{ $status['daily'] }}
                                                </span>

                                                <span role="cell">
                                                    {{ $status['monthly'] }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </section>

                            <div class="settings-form__actions">
                                <div
                                    class="settings-form__action-content"
                                >
                                    <button
                                        type="submit"
                                        class="settings-form__button
                                            budget-form__submit"
                                    >
                                        {{ __('Update Budget Settings') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </main>
</x-app-layout>
