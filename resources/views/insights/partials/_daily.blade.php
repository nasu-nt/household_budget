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
</section>