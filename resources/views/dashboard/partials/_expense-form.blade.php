{{-- resources/views/dashboard/partials/_expense-form.blade.php --}}

@php
    $expenseRows = old('expenses');

    if (! is_array($expenseRows) || $expenseRows === []) {
        $expenseRows = [
            [
                'expense_date' => now()->format('Y-m-d'),
                'recorded_time' => '',
                'category_id' => '',
                'amount' => '',
                'memo' => '',
            ],
        ];
    }

    // JavaScriptを無効化されても5件を超えて表示しない
    $expenseRows = array_slice(
        array_values($expenseRows),
        0,
        5
    );
@endphp

<section
    class="dashboard__record-expense-area"
    aria-labelledby="expense-form-title"
>
    <header class="expense-form__header">
        <h2
            id="expense-form-title"
            class="expense-form__title"
        >
            <img
                class="expense-form__title-icon"
                src="{{ asset('images/icons/pen_1.svg') }}"
                alt=""
                aria-hidden="true"
            >

            <span>{{ __('Log your spending') }}</span>
        </h2>
    </header>

    <form
        class="expense-form"
        method="POST"
        action="{{ route('expenses.store') }}"
        data-expense-form
        data-default-date="{{ now()->format('Y-m-d') }}"
    >
        @csrf

        <div
            class="expense-form__entries"
            data-expense-list
        >
            @foreach ($expenseRows as $index => $expense)
                <fieldset
                    class="expense-entry"
                    data-expense-card
                >
                    <legend
                        class="sr-only"
                        data-expense-legend
                    >
                        {{ __('Expense :number', [
                            'number' => $index + 1,
                        ]) }}
                    </legend>

                    {{-- Date --}}
                    <div class="expense-entry__row">
                        <label
                            for="expense_expense_date_{{ $index }}"
                            class="expense-entry__label"
                            data-expense-label="expense_date"
                        >
                            {{ __('Date') }}
                        </label>

                       <div class="expense-entry__control">
                            <div class="expense-entry__date-wrapper">
                                <input
                                    id="expense_expense_date_{{ $index }}"
                                    name="expenses[{{ $index }}][expense_date]"
                                    type="date"
                                    class="expense-entry__input
                                        expense-entry__date-input
                                        @error("expenses.{$index}.expense_date")
                                            is-invalid
                                        @enderror"
                                    value="{{ data_get(
                                        $expense,
                                        'expense_date',
                                        ''
                                    ) }}"
                                    required
                                    data-expense-field="expense_date"
                                    @error("expenses.{$index}.expense_date")
                                        aria-invalid="true"
                                        aria-describedby="expense_expense_date_{{ $index }}_error"
                                    @enderror
                                >

                                <img
                                    class="expense-entry__date-icon"
                                    src="{{ asset('images/icons/calendar_1.svg') }}"
                                    alt=""
                                    aria-hidden="true"
                                >
                            </div>

                            @error("expenses.{$index}.expense_date")
                                <p
                                    id="expense_expense_date_{{ $index }}_error"
                                    class="expense-entry__error"
                                    role="alert"
                                    data-expense-error="expense_date"
                                >
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                    

                    {{-- Time --}}
                    <div class="expense-entry__row">
                        <label
                            for="expense_recorded_time_{{ $index }}"
                            class="expense-entry__label"
                            data-expense-label="recorded_time"
                        >
                            {{ __('Time') }}
                        </label>

                        <div class="expense-entry__control">
                            <div class="expense-entry__time-wrapper">
                                <input
                                    id="expense_recorded_time_{{ $index }}"
                                    name="expenses[{{ $index }}][recorded_time]"
                                    type="time"
                                    class="expense-entry__input
                                        expense-entry__time-input
                                        @error("expenses.{$index}.recorded_time")
                                            is-invalid
                                        @enderror"
                                    value="{{ data_get(
                                        $expense,
                                        'recorded_time',
                                        ''
                                    ) }}"
                                    step="60"
                                    data-expense-field="recorded_time"
                                    @error("expenses.{$index}.recorded_time")
                                        aria-invalid="true"
                                        aria-describedby="expense_recorded_time_{{ $index }}_error"
                                    @enderror
                                >

                                <img
                                    class="expense-entry__time-icon"
                                    src="{{ asset('images/icons/clock_1.svg') }}"
                                    alt=""
                                    aria-hidden="true"
                                >
                            </div>

                            @error("expenses.{$index}.recorded_time")
                                <p
                                    id="expense_recorded_time_{{ $index }}_error"
                                    class="expense-entry__error"
                                    role="alert"
                                    data-expense-error="recorded_time"
                                >
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Amount --}}
                    <div class="expense-entry__row">
                        <label
                            for="expense_amount_{{ $index }}"
                            class="expense-entry__label"
                            data-expense-label="amount"
                        >
                            {{ __('Amount') }}
                        </label>

                        <div class="expense-entry__control">
                            <div class="money-input">
                                <span
                                    class="money-input__currency"
                                    aria-hidden="true"
                                >
                                    ¥
                                </span>
                                <input
                                    id="expense_amount_{{ $index }}"
                                    name="expenses[{{ $index }}][amount]"
                                    type="text"
                                    class="expense-entry__input money-input__field
                                        oney-input__field
                                        @error("expenses.{$index}.amount")
                                            is-invalid
                                        @enderror"
                                    inputmode="numeric"
                                    autocomplete="off"
                                    maxlength="13"
                                    pattern="[0-9,]*"
                                    data-max-digits="10"
                                    value="{{ data_get($expense, 'amount') }}"
                                    required
                                    data-expense-field="amount"
                                    data-expense-amount
                                    data-money-input
                                    @error("expenses.{$index}.amount")
                                        aria-invalid="true"
                                        aria-describedby="expense_amount_{{ $index }}_error"
                                    @enderror
                                >

                                @error("expenses.{$index}.amount")
                                    <p
                                        id="expense_amount_{{ $index }}_error"
                                        class="expense-entry__error"
                                        role="alert"
                                        data-expense-error="amount"
                                    >
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Memo --}}
                    <div class="expense-entry__row">
                        <label
                            for="expense_memo_{{ $index }}"
                            class="expense-entry__label"
                            data-expense-label="memo"
                        >
                            {{ __('Memo') }}
                        </label>

                        <div class="expense-entry__control">
                            <input
                                id="expense_memo_{{ $index }}"
                                name="expenses[{{ $index }}][memo]"
                                type="text"
                                class="expense-entry__input
                                    @error("expenses.{$index}.memo")
                                        is-invalid
                                    @enderror"
                                maxlength="255"
                                value="{{ data_get($expense, 'memo') }}"
                                data-expense-field="memo"
                                @error("expenses.{$index}.memo")
                                    aria-invalid="true"
                                    aria-describedby="expense_memo_{{ $index }}_error"
                                @enderror
                            >

                            @error("expenses.{$index}.memo")
                                <p
                                    id="expense_memo_{{ $index }}_error"
                                    class="expense-entry__error"
                                    role="alert"
                                    data-expense-error="memo"
                                >
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="expense-entry__actions">
                        <button
                            type="button"
                            class="expense-entry__remove"
                            data-remove-expense
                            @if (count($expenseRows) === 1)
                                hidden
                            @endif
                        >
                            {{ __('Remove this expense') }}
                        </button>
                    </div>
                </fieldset>
            @endforeach
        </div>

        <button
            type="button"
            class="expense-form__add"
            data-add-expense
            @if (count($expenseRows) >= 5)
                hidden
            @endif
        >
            <span aria-hidden="true">＋</span>
            {{ __('Add another expense') }}
        </button>

        <p
            class="expense-form__limit"
            data-expense-limit
            @if (count($expenseRows) < 5)
                hidden
            @endif
        >
            {{ __('Up to 5 expenses can be added at once.') }}
        </p>

        <span
            class="sr-only"
            aria-live="polite"
            data-expense-count
        ></span>

        <button
            type="submit"
            class="expense-form__submit"
        >
            {{ __('Save expense') }}
        </button>
    </form>
</section>