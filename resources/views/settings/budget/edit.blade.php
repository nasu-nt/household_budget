@php
    $oldEndOfMonth = old('is_end_of_month');

    $isEndOfMonth = $oldEndOfMonth === null
        ? (bool) $budgetSetting->is_end_of_month
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

                                        <div class="budget-form__money-input">
                                            <span
                                                class="budget-form__currency"
                                                aria-hidden="true"
                                            >
                                                ¥
                                            </span>

                                            <input
                                                id="monthly_budget_display"
                                                type="text"
                                                class="settings-form__input
                                                    budget-form__amount-input
                                                    @error('monthly_budget')
                                                        is-invalid
                                                    @enderror"
                                                value="{{ number_format($monthlyBudget) }}"
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
                                        </div>

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

                                        <div class="budget-form__money-input">
                                            <span
                                                class="budget-form__currency"
                                                aria-hidden="true"
                                            >
                                                ¥
                                            </span>

                                            <input
                                                id="monthly_limit_display"
                                                type="text"
                                                class="settings-form__input
                                                    budget-form__amount-input
                                                    @error('monthly_limit')
                                                        is-invalid
                                                    @enderror"
                                                value="{{ number_format($monthlyLimit) }}"
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
                                        </div>

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

                                <div
                                    class="settings-form__row
                                        budget-form__closing-row"
                                    role="group"
                                    aria-labelledby="closing-day-label"
                                    x-data="{
                                        closingIsEndOfMonth: @js($isEndOfMonth)
                                    }"
                                >
                                    <span
                                        id="closing-day-label"
                                        class="settings-form__label
                                            budget-form__closing-label"
                                    >
                                        {{ __('Closing Day') }}
                                    </span>

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
                                                    @checked($isEndOfMonth)
                                                    x-bind:checked="closingIsEndOfMonth"
                                                    x-on:change="
                                                        closingIsEndOfMonth = true;
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
                                                    @checked(! $isEndOfMonth)
                                                    x-bind:checked="! closingIsEndOfMonth"
                                                    x-on:change="
                                                        closingIsEndOfMonth = false;
                                                        isEndOfMonth = false
                                                    "
                                                >

                                                <span>
                                                    {{ __('Specific day') }}
                                                </span>
                                            </label>

                                            <div
                                                class="budget-form__select-wrapper"
                                                x-bind:class="{
                                                    'is-disabled': closingIsEndOfMonth
                                                }"
                                            >
                                                <select
                                                    id="closing_day"
                                                    name="closing_day"
                                                    class="budget-form__closing-select
                                                        @error('closing_day')
                                                            is-invalid
                                                        @enderror"
                                                    x-model.number="closingDay"
                                                    x-bind:disabled="closingIsEndOfMonth"
                                                    x-bind:aria-disabled="closingIsEndOfMonth.toString()"
                                                    aria-label="{{ __('Specific closing day') }}"
                                                    @error('closing_day')
                                                        aria-invalid="true"
                                                        aria-describedby="closing-day-error"
                                                    @enderror
                                                >
                                                    @for ($day = 1; $day <= 31; $day++)
                                                        <option
                                                            value="{{ $day }}"
                                                            @selected(
                                                                $closingDay === $day
                                                            )
                                                        >
                                                            {{ $day }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>

                                        @error('is_end_of_month')
                                            <p
                                                class="settings-form__error"
                                                role="alert"
                                            >
                                                {{ $message }}
                                            </p>
                                        @enderror

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
                                </div>
                            </div>

                            @include('settings.budget.partials._budget-guide')

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