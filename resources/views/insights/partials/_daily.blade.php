{{-- resources/views/insights/partials/_daily.blade.php --}}
<section
    class="daily-insights"
    aria-label="Daily spending insights"
>
    <header class="daily-insights__header">
        <div class="daily-insights__controls">
            <a
                href="{{ route('insights.daily', [
                    'date' => $previousDate,
                ]) }}"
                class="daily-insights__nav-button"
                aria-label="Previous day"
            >
                ◀
            </a>

            <time
                class="daily-insights__date"
                datetime="{{ $date }}"
            >
                {{ $dateLabel }}
            </time>

            <div class="daily-insights__date-picker-wrapper">
                <input
                    type="date"
                    id="daily-insights-date"
                    class="daily-insights__date-input"
                    value="{{ $date }}"
                    data-daily-insights-date
                >

                <button
                    type="button"
                    class="daily-insights__date-picker-button"
                    data-daily-insights-date-picker
                    aria-label="Select date"
                >
                    <img
                        class="daily-insights__date-icon"
                        src="{{ asset('images/icons/calendar_1.svg') }}"
                        alt=""
                        aria-hidden="true"
                    >
                </button>
            </div>

            <a
                href="{{ route('insights.daily', [
                    'date' => $nextDate,
                ]) }}"
                class="daily-insights__nav-button"
                aria-label="Next day"
            >
                ▶
            </a>
        </div>

        <a
            href="{{ route('insights.daily', [
                'date' => $todayDate,
            ]) }}"
            class="daily-insights__today-button
                {{ $date === $todayDate ? 'is-current' : '' }}"
            @if ($date === $todayDate)
                aria-current="date"
            @endif
        >
            Today
        </a>
    </header>

    {{-- 支出サマリー --}}
    <section
        class="daily-insights__summary"
        aria-labelledby="daily-insights-summary-title"
    >
        <div class="daily-insights__summary-main">
            <span
                class="daily-insights__summary-icon-wrapper"
                aria-hidden="true"
            >
                <img
                    class="daily-insights__summary-icon"
                    src="{{ asset('images/icons/wallet_1.svg') }}"
                    alt=""
                >
            </span>

            <div class="daily-insights__summary-spending">
                <h1
                    id="daily-insights-summary-title"
                    class="daily-insights__summary-title"
                >
                    Spending on this day:
                </h1>

                <p class="daily-insights__summary-amount">
                    ¥{{ number_format($currentDayTotal) }}
                </p>
            </div>
        </div>

        <div class="daily-insights__comparison">
            <p class="daily-insights__comparison-label">
                vs. the previous day
            </p>

            <p
                class="
                    daily-insights__comparison-amount
                    {{ $previousDayDifferenceClass }}
                "
            >
                @if ($previousDayDifference > 0)
                    +¥{{ number_format($previousDayDifference) }}
                @elseif ($previousDayDifference < 0)
                    -¥{{ number_format(abs($previousDayDifference)) }}
                @else
                    ¥0
                @endif
            </p>
        </div>

        <div class="daily-insights__comparison">
            <p class="daily-insights__comparison-label">
                vs. current period's<br>
                daily average
            </p>

            <p
                class="
                    daily-insights__comparison-amount
                    {{ $averageDifferenceClass }}
                "
            >
                @if ($averageDifference > 0)
                    +¥{{ number_format($averageDifference) }}
                @elseif ($averageDifference < 0)
                    -¥{{ number_format(abs($averageDifference)) }}
                @else
                    ¥0
                @endif
            </p>
        </div>
    </section>

    {{-- Spending Notesフォーム --}}
    <section
        class="daily-insights__notes"
        aria-labelledby="daily-insights-notes-title"
    >
        <h2
            id="daily-insights-notes-title"
            class="daily-insights__notes-title"
        >
            Spending Notes
        </h2>

        <form
            method="POST"
            action="{{ route('insights.daily-note.update', [
                'date' => $date,
            ]) }}"
            class="daily-insights__notes-form"
        >
            @csrf
            @method('PUT')

            <div class="daily-insights__notes-field">
                <textarea
                    id="daily-note"
                    name="note"
                    class="
                        daily-insights__notes-input
                        @error('note') is-invalid @enderror
                    "
                    rows="1"
                    maxlength="500"
                    placeholder="Add a note about this day's spending..."
                    aria-describedby="
                        daily-note-count
                        @error('note') daily-note-error @enderror
                    "
                >{{ old('note', $dailyNote ?? '') }}</textarea>

                @error('note')
                    <p
                        id="daily-note-error"
                        class="daily-insights__notes-error"
                        role="alert"
                    >
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <button
                type="submit"
                class="daily-insights__notes-save"
            >
                Save
            </button>
        </form>
    </section>

    {{-- カテゴリ別支出 --}}
    <section
        class="daily-insights__categories"
        aria-labelledby="daily-insights-categories-title"
    >
        <h2
            id="daily-insights-categories-title"
            class="daily-insights__section-title"
        >
            Spending by category
        </h2>

        <div class="daily-insights__categories-card">
            @if ($categorySpending === [])
                <p class="daily-insights__categories-empty">
                    No spending recorded for this day.
                </p>

                <div class="daily-insights__categories-empty-total">
                    <strong>Total</strong>
                    <span>¥0</span>
                </div>
            @else
                <table class="daily-insights__categories-table">
                    <colgroup>
                        <col class="daily-insights__category-col--name">
                        <col class="daily-insights__category-col--amount">
                        <col class="daily-insights__category-col--percentage">
                        <col class="daily-insights__category-col--chart">
                    </colgroup>
                    <tbody>
                        @foreach ($categorySpending as $category)
                            <tr class="daily-insights__category-row">
                                <th
                                    scope="row"
                                    class="daily-insights__category-name"
                                >
                                    {{ $category['name'] }}
                                </th>

                                <td class="daily-insights__category-amount">
                                    ¥{{ number_format($category['amount']) }}
                                </td>

                                <td class="daily-insights__category-percentage">
                                    {{ $category['percentage'] }}%
                                </td>

                                <td class="daily-insights__category-chart">
                                    <div
                                        class="daily-insights__category-track"
                                        aria-hidden="true"
                                    >
                                        <span
                                            class="daily-insights__category-fill"
                                            style="
                                                --category-width:
                                                    {{ $category['barPercentage'] }}%;
                                                --category-color:
                                                    {{ $category['color'] }};
                                            "
                                        ></span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr class="daily-insights__category-total-row">
                            <th
                                scope="row"
                                class="daily-insights__category-total-label"
                            >
                                Total
                            </th>

                            <td class="daily-insights__category-total-amount">
                                ¥{{ number_format($currentDayTotal) }}
                            </td>

                            <td class="daily-insights__category-total-percentage">
                                100%
                            </td>

                            <td class="daily-insights__category-total-chart">
                                <div
                                    class="daily-insights__category-total-bar"
                                    aria-hidden="true"
                                >
                                    @foreach ($categorySpending as $category)
                                        <span
                                            class="daily-insights__category-total-segment"
                                            style="
                                                --category-width:
                                                    {{ $category['barPercentage'] }}%;
                                                --category-color:
                                                    {{ $category['color'] }};
                                            "
                                        ></span>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </div>
    </section>

    {{-- 支出レコード一覧 --}}
    <section
        class="daily-insights__records"
        aria-labelledby="daily-insights-records-title"
        data-daily-records-section
    >
        <h2
            id="daily-insights-records-title"
            class="daily-insights__section-title"
        >
            Records
        </h2>

        @php
            $createFormId = 'daily-record-create-form';

            $hasCreateRecordErrors =
                old('creating_record') === '1'
                && $errors->any();
        @endphp

        <div class="daily-insights__records-card">
            <p
                class="daily-insights__records-empty"
                data-records-empty
                @if (
                    $dailyRecords !== []
                    || $hasCreateRecordErrors
                )
                    hidden
                @endif
            >
                No spending records for this day.
            </p>

            <table
                class="daily-insights__records-table"
                data-daily-records
                @if (
                    $dailyRecords === []
                    && ! $hasCreateRecordErrors
                )
                    hidden
                @endif
            >
                    <colgroup>
                        <col class="daily-insights__record-col--time">
                        <col class="daily-insights__record-col--category">
                        <col class="daily-insights__record-col--amount">
                        <col class="daily-insights__record-col--memo">
                        <col class="daily-insights__record-col--actions">
                    </colgroup>

                    <thead>
                        <tr>
                            <th
                                scope="col"
                                @if ($recordSort === 'recorded_time')
                                    aria-sort="{{ $recordDirection === 'asc'
                                        ? 'ascending'
                                        : 'descending' }}"
                                @endif
                            >
                                <a
                                    href="{{ route('insights.daily', [
                                        'date' => $date,
                                        'sort' => 'recorded_time',
                                        'direction' =>
                                            $recordSort === 'recorded_time'
                                            && $recordDirection === 'asc'
                                                ? 'desc'
                                                : 'asc',
                                    ]) }}"
                                    class="daily-insights__records-sort
                                        {{ $recordSort === 'recorded_time'
                                            ? 'is-active'
                                            : '' }}"
                                >
                                    <span>Recorded time</span>

                                    <span
                                        class="daily-insights__records-sort-icon"
                                        aria-hidden="true"
                                    >
                                        @if (
                                            $recordSort === 'recorded_time'
                                            && $recordDirection === 'asc'
                                        )
                                            ▲
                                        @else
                                            ▼
                                        @endif
                                    </span>
                                </a>
                            </th>

                            <th
                                scope="col"
                                @if ($recordSort === 'category')
                                    aria-sort="{{ $recordDirection === 'asc'
                                        ? 'ascending'
                                        : 'descending' }}"
                                @endif
                            >
                                <a
                                    href="{{ route('insights.daily', [
                                        'date' => $date,
                                        'sort' => 'category',
                                        'direction' =>
                                            $recordSort === 'category'
                                            && $recordDirection === 'asc'
                                                ? 'desc'
                                                : 'asc',
                                    ]) }}"
                                    class="daily-insights__records-sort
                                        {{ $recordSort === 'category'
                                            ? 'is-active'
                                            : '' }}"
                                >
                                    <span>Category</span>

                                    <span
                                        class="daily-insights__records-sort-icon"
                                        aria-hidden="true"
                                    >
                                        @if (
                                            $recordSort === 'category'
                                            && $recordDirection === 'asc'
                                        )
                                            ▲
                                        @else
                                            ▼
                                        @endif
                                    </span>
                                </a>
                            </th>

                            <th
                                scope="col"
                                @if ($recordSort === 'amount')
                                    aria-sort="{{ $recordDirection === 'asc'
                                        ? 'ascending'
                                        : 'descending' }}"
                                @endif
                            >
                                <a
                                    href="{{ route('insights.daily', [
                                        'date' => $date,
                                        'sort' => 'amount',
                                        'direction' =>
                                            $recordSort === 'amount'
                                            && $recordDirection === 'asc'
                                                ? 'desc'
                                                : 'asc',
                                    ]) }}"
                                    class="daily-insights__records-sort
                                        {{ $recordSort === 'amount'
                                            ? 'is-active'
                                            : '' }}"
                                >
                                    <span>Amount</span>

                                    <span
                                        class="daily-insights__records-sort-icon"
                                        aria-hidden="true"
                                    >
                                        @if (
                                            $recordSort === 'amount'
                                            && $recordDirection === 'asc'
                                        )
                                            ▲
                                        @else
                                            ▼
                                        @endif
                                    </span>
                                </a>
                            </th>

                            <th scope="col">
                                Memo
                            </th>

                            <th scope="col">
                                <span class="sr-only">
                                    Actions
                                </span>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($dailyRecords as $record)
                            @php
                                $editFormId =
                                    "daily-record-edit-{$record['id']}";

                                $hasRecordErrors =
                                    (int) old('editing_expense_id')
                                    === $record['id'];
                            @endphp

                            {{-- 通常表示行 --}}
                            <tr
                                id="daily-insights-record-{{ $record['id'] }}"
                                data-record-display-row
                                @if ($hasRecordErrors)
                                    hidden
                                @endif
                            >
                                <td class="daily-insights__record-time">
                                    @if ($record['recordedTime'] === null)
                                        <span aria-label="Time not recorded">
                                            —
                                        </span>
                                    @else
                                        <time
                                            datetime="{{ $record['recordedTime'] }}"
                                        >
                                            {{ $record['recordedTime'] }}
                                        </time>
                                    @endif
                                </td>

                                <td class="daily-insights__record-category">
                                    <span
                                        class="daily-insights__record-category-color"
                                        style="
                                            --record-category-color:
                                                {{ $record['categoryColor'] }};
                                        "
                                        aria-hidden="true"
                                    ></span>

                                    <span>
                                        {{ $record['categoryName'] }}
                                    </span>
                                </td>

                                <td class="daily-insights__record-amount">
                                    ¥{{ number_format($record['amount']) }}
                                </td>

                                <td class="daily-insights__record-memo">
                                    {{ $record['memo'] !== ''
                                        ? $record['memo']
                                        : '—' }}
                                </td>

                                <td class="daily-insights__record-actions">
                                    <button
                                        type="button"
                                        class="daily-insights__record-edit"
                                        data-record-edit
                                        aria-controls="
                                            daily-insights-record-edit-{{ $record['id'] }}
                                        "
                                    >
                                        Edit
                                    </button>
                                </td>
                            </tr>

                            {{-- 編集フォーム行 --}}
                            <tr
                                id="daily-insights-record-edit-{{ $record['id'] }}"
                                class="daily-insights__record-edit-row"
                                data-record-edit-row
                                @if (! $hasRecordErrors)
                                    hidden
                                @endif
                            >
                                {{-- Recorded time --}}
                                <td class="daily-insights__record-time-edit">
                                    <form
                                        id="{{ $editFormId }}"
                                        method="POST"
                                        action="{{ route(
                                            'insights.daily-record.update',
                                            [
                                                'date' => $date,
                                                'expense' => $record['id'],
                                            ]
                                        ) }}"
                                    >
                                        @csrf
                                        @method('PUT')

                                        <input
                                            type="hidden"
                                            name="editing_expense_id"
                                            value="{{ $record['id'] }}"
                                        >

                                        <input
                                            type="hidden"
                                            name="sort"
                                            value="{{ $recordSort }}"
                                        >

                                        <input
                                            type="hidden"
                                            name="direction"
                                            value="{{ $recordDirection }}"
                                        >
                                    </form>

                                    <div class="daily-insights__record-time-wrapper">
                                        <input
                                            form="{{ $editFormId }}"
                                            type="time"
                                            name="recorded_time"
                                            class="
                                                daily-insights__record-input
                                                daily-insights__record-time-input
                                                @if ($hasRecordErrors)
                                                    @error('recorded_time')
                                                        is-invalid
                                                    @enderror
                                                @endif
                                            "
                                            value="{{ $hasRecordErrors
                                                ? old('recorded_time')
                                                : ($record['recordedTime'] ?? '') }}"
                                            step="60"
                                            aria-label="Recorded time"
                                            novalidate
                                        >

                                        <img
                                            class="daily-insights__record-time-icon"
                                            src="{{ asset('images/icons/clock_1.svg') }}"
                                            alt=""
                                            aria-hidden="true"
                                        >
                                    </div>

                                    @if ($hasRecordErrors)
                                        @error('recorded_time')
                                            <p
                                                class="daily-insights__record-error"
                                                role="alert"
                                            >
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    @endif
                                </td>

                                {{-- Category --}}
                                <td class="daily-insights__record-category-edit">
                                    <div class="daily-insights__record-select-wrapper">
                                        <select
                                            form="{{ $editFormId }}"
                                            name="category_id"
                                            class="
                                                daily-insights__record-select
                                                @if ($hasRecordErrors)
                                                    @error('category_id')
                                                        is-invalid
                                                    @enderror
                                                @endif
                                            "
                                            aria-label="Category"
                                            novalidate
                                        >
                                            @foreach ($recordCategories as $category)
                                                <option
                                                    value="{{ $category->id }}"
                                                    @selected(
                                                        (string) (
                                                            $hasRecordErrors
                                                                ? old('category_id')
                                                                : $record['categoryId']
                                                        )
                                                        === (string) $category->id
                                                    )
                                                >
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @if ($hasRecordErrors)
                                        @error('category_id')
                                            <p
                                                class="daily-insights__record-error"
                                                role="alert"
                                            >
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    @endif
                                </td>

                                {{-- Amount --}}
                                <td class="daily-insights__record-amount-edit">
                                    <div class="daily-insights__record-money">
                                        <span aria-hidden="true">
                                            ¥
                                        </span>

                                        <input
                                            form="{{ $editFormId }}"
                                            type="text"
                                            name="amount"
                                            class="daily-insights__record-input
                                                @if ($hasRecordErrors)
                                                    @error('amount')
                                                        is-invalid
                                                    @enderror
                                                @endif"
                                            value="{{ $hasRecordErrors
                                                ? old('amount')
                                                : $record['amount'] }}"
                                            inputmode="numeric"
                                            maxlength="13"
                                            data-money-input
                                            data-max-digits="10"
                                            aria-label="Amount"
                                            novalidate
                                        >
                                    </div>

                                    @if ($hasRecordErrors)
                                        @error('amount')
                                            <p
                                                class="daily-insights__record-error"
                                                role="alert"
                                            >
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    @endif
                                </td>

                                {{-- Memo --}}
                                <td class="daily-insights__record-memo-edit">
                                    <input
                                        form="{{ $editFormId }}"
                                        type="text"
                                        name="memo"
                                        class="daily-insights__record-input
                                            @if ($hasRecordErrors)
                                                @error('memo')
                                                    is-invalid
                                                @enderror
                                            @endif"
                                        value="{{ $hasRecordErrors
                                            ? old('memo')
                                            : $record['memo'] }}"
                                        maxlength="255"
                                        aria-label="Memo"
                                    >

                                    @if ($hasRecordErrors)
                                        @error('memo')
                                            <p
                                                class="daily-insights__record-error"
                                                role="alert"
                                            >
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    @endif
                                </td>

                                {{-- Save / Cancel / Delete --}}
                                <td class="daily-insights__record-actions">
                                    <div class="daily-insights__record-edit-actions">
                                        <div class="daily-insights__record-edit-buttons">
                                            <button
                                                form="{{ $editFormId }}"
                                                type="submit"
                                                class="daily-insights__record-save"
                                            >
                                                Save
                                            </button>

                                            <button
                                                type="button"
                                                class="daily-insights__record-cancel"
                                                data-record-cancel
                                            >
                                                Cancel
                                            </button>
                                        </div>

                                        <button
                                            type="button"
                                            class="daily-insights__record-delete"
                                            data-record-delete
                                            data-record-delete-url="{{ route(
                                                'insights.daily-record.destroy',
                                                [
                                                    'date' => $date,
                                                    'expense' => $record['id'],
                                                ]
                                            ) }}"
                                            data-record-delete-summary="{{
                                                $record['categoryName']
                                            }} / ¥{{ number_format($record['amount']) }}"
                                            aria-haspopup="dialog"
                                            aria-controls="daily-record-delete-modal"
                                        >
                                            <img
                                                class="daily-insights__record-delete-icon"
                                                src="{{ asset('images/icons/trash_1.svg') }}"
                                                alt=""
                                                aria-hidden="true"
                                            >

                                            <span>Delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        {{-- 新規レコード入力行 --}}
                        <tr
                            class="daily-insights__record-create-row"
                            data-record-create-row
                            @if (! $hasCreateRecordErrors)
                                hidden
                            @endif
                        >
                            {{-- Recorded time --}}
                            <td class="daily-insights__record-time-edit">
                                <form
                                    id="{{ $createFormId }}"
                                    method="POST"
                                    action="{{ route(
                                        'insights.daily-record.store',
                                        [
                                            'date' => $date,
                                        ]
                                    ) }}"
                                    novalidate
                                >
                                    @csrf

                                    <input
                                        type="hidden"
                                        name="creating_record"
                                        value="1"
                                    >

                                    <input
                                        type="hidden"
                                        name="sort"
                                        value="{{ $recordSort }}"
                                    >

                                    <input
                                        type="hidden"
                                        name="direction"
                                        value="{{ $recordDirection }}"
                                    >
                                </form>

                                <div class="daily-insights__record-time-wrapper">
                                    <input
                                        form="{{ $createFormId }}"
                                        type="time"
                                        name="recorded_time"
                                        class="
                                            daily-insights__record-input
                                            daily-insights__record-time-input
                                            @if ($hasCreateRecordErrors)
                                                @error('recorded_time')
                                                    is-invalid
                                                @enderror
                                            @endif
                                        "
                                        value="{{ $hasCreateRecordErrors
                                            ? old('recorded_time')
                                            : '' }}"
                                        step="60"
                                        aria-label="Recorded time"
                                        data-record-create-time
                                    >

                                    <img
                                        class="daily-insights__record-time-icon"
                                        src="{{ asset('images/icons/clock_1.svg') }}"
                                        alt=""
                                        aria-hidden="true"
                                    >
                                </div>

                                @if ($hasCreateRecordErrors)
                                    @error('recorded_time')
                                        <p
                                            class="daily-insights__record-error"
                                            role="alert"
                                        >
                                            {{ $message }}
                                        </p>
                                    @enderror
                                @endif
                            </td>

                            {{-- Category --}}
                            <td class="daily-insights__record-category-edit">
                                <div class="daily-insights__record-select-wrapper">
                                    <select
                                        form="{{ $createFormId }}"
                                        name="category_id"
                                        class="
                                            daily-insights__record-select
                                            @if ($hasCreateRecordErrors)
                                                @error('category_id')
                                                    is-invalid
                                                @enderror
                                            @endif
                                        "
                                        aria-label="Category"
                                        novalidate
                                    >
                                        <option value=""></option>

                                        @foreach ($recordCategories as $category)
                                            <option
                                                value="{{ $category->id }}"
                                                @selected(
                                                    (string) old('category_id')
                                                    === (string) $category->id
                                                )
                                            >
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                @if ($hasCreateRecordErrors)
                                    @error('category_id')
                                        <p
                                            class="daily-insights__record-error"
                                            role="alert"
                                        >
                                            {{ $message }}
                                        </p>
                                    @enderror
                                @endif
                            </td>

                            {{-- Amount --}}
                            <td class="daily-insights__record-amount-edit">
                                <div class="daily-insights__record-money">
                                    <span aria-hidden="true">
                                        ¥
                                    </span>

                                    <input
                                        form="{{ $createFormId }}"
                                        type="text"
                                        name="amount"
                                        class="
                                            daily-insights__record-input
                                            @if ($hasCreateRecordErrors)
                                                @error('amount')
                                                    is-invalid
                                                @enderror
                                            @endif
                                        "
                                        value="{{ $hasCreateRecordErrors
                                            ? old('amount')
                                            : '' }}"
                                        inputmode="numeric"
                                        maxlength="13"
                                        data-money-input
                                        data-max-digits="10"
                                        aria-label="Amount"
                                        novalidate
                                    >
                                </div>

                                @if ($hasCreateRecordErrors)
                                    @error('amount')
                                        <p
                                            class="daily-insights__record-error"
                                            role="alert"
                                        >
                                            {{ $message }}
                                        </p>
                                    @enderror
                                @endif
                            </td>

                            {{-- Memo --}}
                            <td class="daily-insights__record-memo-edit">
                                <input
                                    form="{{ $createFormId }}"
                                    type="text"
                                    name="memo"
                                    class="
                                        daily-insights__record-input
                                        @if ($hasCreateRecordErrors)
                                            @error('memo')
                                                is-invalid
                                            @enderror
                                        @endif
                                    "
                                    value="{{ $hasCreateRecordErrors
                                        ? old('memo')
                                        : '' }}"
                                    maxlength="255"
                                    aria-label="Memo"
                                >

                                @if ($hasCreateRecordErrors)
                                    @error('memo')
                                        <p
                                            class="daily-insights__record-error"
                                            role="alert"
                                        >
                                            {{ $message }}
                                        </p>
                                    @enderror
                                @endif
                            </td>

                            {{-- Save / Cancel --}}
                            <td class="daily-insights__record-actions">
                                <div class="daily-insights__record-edit-buttons">
                                    <button
                                        form="{{ $createFormId }}"
                                        type="submit"
                                        class="daily-insights__record-save"
                                    >
                                        Save
                                    </button>

                                    <button
                                        type="button"
                                        class="daily-insights__record-cancel"
                                        data-record-create-cancel
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
        </div>
        <div
            class="daily-insights__record-add-area"
            data-record-add-row
            @if ($hasCreateRecordErrors)
                hidden
            @endif
        >
            <button
                type="button"
                class="daily-insights__record-add"
                data-record-add
            >
                <span aria-hidden="true">
                    ＋
                </span>

                <span>
                    Add another record
                </span>
            </button>
        </div>
        {{-- Record削除確認モーダル --}}
        <div
            id="daily-record-delete-modal"
            class="daily-insights__delete-modal"
            data-record-delete-modal
            hidden
        >
            <div
                class="daily-insights__delete-modal-backdrop"
                data-record-delete-close
            ></div>

            <section
                class="daily-insights__delete-dialog"
                role="dialog"
                aria-modal="true"
                aria-labelledby="daily-record-delete-title"
                aria-describedby="daily-record-delete-description"
            >
                <h3
                    id="daily-record-delete-title"
                    class="daily-insights__delete-title"
                >
                    Delete this record?
                </h3>

                <p
                    id="daily-record-delete-description"
                    class="daily-insights__delete-description"
                >
                    This action cannot be undone.
                </p>

                <p
                    class="daily-insights__delete-target"
                    data-record-delete-target
                ></p>

                <form
                    method="POST"
                    action=""
                    class="daily-insights__delete-form"
                    data-record-delete-form
                >
                    @csrf
                    @method('DELETE')

                    <input
                        type="hidden"
                        name="sort"
                        value="{{ $recordSort }}"
                    >

                    <input
                        type="hidden"
                        name="direction"
                        value="{{ $recordDirection }}"
                    >

                    <div class="daily-insights__delete-actions">
                        <button
                            type="button"
                            class="daily-insights__delete-cancel"
                            data-record-delete-close
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            class="daily-insights__delete-confirm"
                        >
                            Delete
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </section>
</section>