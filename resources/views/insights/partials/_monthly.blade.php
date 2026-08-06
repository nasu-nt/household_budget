<section
    class="monthly-insights"
    aria-label="Monthly spending insights"
>
    <header class="monthly-insights__header">
        <div class="monthly-insights__controls">
            <a
                href="{{ route('insights.monthly', [
                    'month' => $previousMonth,
                ]) }}"
                class="monthly-insights__nav-button"
                aria-label="Previous period"
            >
                ◀
            </a>

            <p class="monthly-insights__period">
                <time datetime="{{ $periodStartDate }}">
                    {{ $periodStartLabel }}
                </time>

                <span aria-hidden="true">
                    -
                </span>

                <time datetime="{{ $periodEndDate }}">
                    {{ $periodEndLabel }}
                </time>
            </p>

            <a
                href="{{ route('insights.monthly', [
                    'month' => $nextMonth,
                ]) }}"
                class="monthly-insights__nav-button"
                aria-label="Next period"
            >
                ▶
            </a>
        </div>

        <a
            href="{{ route('insights.monthly.current') }}"
            class="
                monthly-insights__current-button
                {{ $month === $currentPeriodMonth
                    ? 'is-current'
                    : '' }}
            "
            @if ($month === $currentPeriodMonth)
                aria-current="date"
            @endif
        >
            Current period
        </a>
    </header>
    
    {{-- 支出サマリー --}}
    <section
        class="monthly-insights__summary"
        aria-labelledby="monthly-insights-summary-title"
    >
        <div class="monthly-insights__summary-main">
            <span
                class="monthly-insights__summary-icon-wrapper"
                aria-hidden="true"
            >
                <img
                    class="monthly-insights__summary-icon"
                    src="{{ asset('images/icons/wallet_1.svg') }}"
                    alt=""
                >
            </span>

            <div class="monthly-insights__summary-spending">
                <h1
                    id="monthly-insights-summary-title"
                    class="monthly-insights__summary-title"
                >
                    Current period’s spending:
                </h1>

                <p class="monthly-insights__summary-amount">
                    ¥{{ number_format($currentPeriodTotal) }}
                </p>
            </div>
        </div>

        <div class="monthly-insights__comparison">
            <p class="monthly-insights__comparison-label">
                vs. previous period
            </p>

            <p
                class="
                    monthly-insights__comparison-amount
                    {{ $previousPeriodDifferenceClass }}
                "
            >
                <span>
                    @if ($previousPeriodDifference > 0)
                        +¥{{ number_format($previousPeriodDifference) }}
                    @elseif ($previousPeriodDifference < 0)
                        -¥{{ number_format(abs($previousPeriodDifference)) }}
                    @else
                        ¥0
                    @endif
                </span>


                <span class="monthly-insights__comparison-percentage">
                    @if ($previousPeriodDifferencePercentage === null)
                        (—)
                    @elseif ($previousPeriodDifferencePercentage < 0)
                        (&nbsp;-&nbsp;{{ number_format(
                            abs($previousPeriodDifferencePercentage),
                            1
                        ) }}%&nbsp;)
                    @else
                        (&nbsp;+&nbsp;{{number_format(
                            $previousPeriodDifferencePercentage,
                            1
                        ) }}%&nbsp;)
                    @endif
                </span>
            </p>
        </div>

        <div class="monthly-insights__comparison">
            <p class="monthly-insights__comparison-label">
                vs. 6-period average
            </p>

            <p
                class="
                    monthly-insights__comparison-amount
                    {{ $sixPeriodAverageDifferenceClass }}
                "
            >
                <span>
                    @if ($sixPeriodAverageDifference > 0)
                        +¥{{ number_format($sixPeriodAverageDifference) }}
                    @elseif ($sixPeriodAverageDifference < 0)
                        -¥{{ number_format(abs($sixPeriodAverageDifference)) }}
                    @else
                        ¥0
                    @endif
                </span>

                <span class="monthly-insights__comparison-percentage">
                    @if ($sixPeriodAverageDifferencePercentage === null)
                        (—)
                    @elseif ($sixPeriodAverageDifferencePercentage < 0)
                        (&nbsp-&nbsp;{{ number_format(
                            abs($sixPeriodAverageDifferencePercentage),
                            1
                        ) }}%&nbsp)
                    @else
                        (&nbsp+&nbsp{{ number_format(
                            $sixPeriodAverageDifferencePercentage,
                            1
                        ) }}%&nbsp)
                    @endif
                </span>
            </p>
        </div>
    </section>

    {{-- カテゴリ別支出 --}}
    <section
        class="monthly-insights__categories"
        aria-labelledby="monthly-insights-categories-title"
    >
        <h2
            id="monthly-insights-categories-title"
            class="monthly-insights__section-title"
        >
            Spending by category
        </h2>

        <div class="monthly-insights__categories-card">
            @if ($categorySpending === [])
                <p class="monthly-insights__categories-empty">
                    No spending recorded for this period.
                </p>

                <div class="monthly-insights__categories-empty-total">
                    <strong>Total</strong>

                    <span>
                        ¥0
                    </span>
                </div>
            @else
                <table class="monthly-insights__categories-table">
                    <colgroup>
                        <col class="monthly-insights__category-col--name">
                        <col class="monthly-insights__category-col--amount">
                        <col class="monthly-insights__category-col--percentage">
                        <col class="monthly-insights__category-col--chart">
                        <col class="monthly-insights__category-col--comparison">
                    </colgroup>

                    <thead>
                        <tr>
                            <th
                                scope="col"
                                colspan="2"
                                class="monthly-insights__category-heading-empty"
                            ></th>

                            <th
                                scope="col"
                                class="monthly-insights__category-heading-empty"
                            ></th>

                            <th scope="col">
                                % of total
                            </th>

                            <th scope="col">
                                vs last period
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($categorySpending as $category)
                            <tr class="monthly-insights__category-row">
                                <th
                                    scope="row"
                                    class="monthly-insights__category-name"
                                >
                                    <span
                                        class="monthly-insights__category-color"
                                        style="
                                            --category-color:
                                                {{ $category['color'] }};
                                        "
                                        aria-hidden="true"
                                    ></span>

                                    <span>
                                        {{ $category['name'] }}
                                    </span>
                                </th>

                                <td class="monthly-insights__category-amount">
                                    ¥{{ number_format($category['amount']) }}
                                </td>

                                <td class="monthly-insights__category-percentage">
                                    {{ $category['percentage'] }}%
                                </td>

                                <td class="monthly-insights__category-chart">
                                    <div
                                        class="monthly-insights__category-track"
                                        aria-hidden="true"
                                    >
                                        <span
                                            class="monthly-insights__category-fill"
                                            style="
                                                --category-width:
                                                    {{ $category['barPercentage'] }}%;
                                                --category-color:
                                                    {{ $category['color'] }};
                                            "
                                        ></span>
                                    </div>
                                </td>
                                <td
                                    class="
                                        monthly-insights__category-comparison
                                        {{ $category['differenceClass'] }}
                                    "
                                >
                                    <div class="monthly-insights__category-comparison-content">
                                        <span class="monthly-insights__category-comparison-value">
                                            @if ($category['difference'] > 0)
                                                +¥{{ number_format($category['difference']) }}
                                            @elseif ($category['difference'] < 0)
                                                -¥{{ number_format(abs($category['difference'])) }}
                                            @else
                                                ¥0
                                            @endif
                                        </span>

                                        @if ($category['difference'] > 0)
                                            <img
                                                class="monthly-insights__category-comparison-icon"
                                                src="{{ asset('images/icons/allow-up1.svg') }}"
                                                alt=""
                                                aria-hidden="true"
                                            >
                                        @elseif ($category['difference'] < 0)
                                            <img
                                                class="monthly-insights__category-comparison-icon"
                                                src="{{ asset('images/icons/allow-down_1.svg') }}"
                                                alt=""
                                                aria-hidden="true"
                                            >
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr class="monthly-insights__category-total-row">
                            <th
                                scope="row"
                                class="monthly-insights__category-total-label"
                            >
                                Total
                            </th>

                            <td class="monthly-insights__category-total-amount">
                                ¥{{ number_format($currentPeriodTotal) }}
                            </td>

                            <td class="monthly-insights__category-total-percentage">
                                100%
                            </td>

                            {{-- Totalのバー --}}
                            <td
                                colspan="2"
                                class="monthly-insights__category-total-chart"
                            >
                                <div
                                    class="monthly-insights__category-total-bar"
                                    aria-hidden="true"
                                >
                                    @foreach ($categorySpending as $category)
                                        <span
                                            class="monthly-insights__category-total-segment"
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
            <div class="monthly-insights__category-records-action">
                <div
                    class="monthly-insights__category-records-tooltip"
                    tabindex="0"
                    aria-describedby="category-records-coming-soon"
                >
                    <button
                        type="button"
                        class="monthly-insights__category-records-button"
                        disabled
                    >
                        View records from all categories
                    </button>

                    <span
                        id="category-records-coming-soon"
                        class="monthly-insights__category-records-message"
                        role="tooltip"
                    >
                        Coming soon — this feature is still under development.
                    </span>
                </div>
            </div>
            @endif
        </div>
    </section>

    {{-- 日別支出推移 --}}
    <section
        class="monthly-insights__trend"
        aria-labelledby="monthly-insights-trend-title"
        data-monthly-spending-trend
    >
        <h2
            id="monthly-insights-trend-title"
            class="monthly-insights__section-title"
        >
            Daily spending trend
        </h2>

        <div class="monthly-insights__trend-card">
            @if (
                $highestSpendingDay === null
                || $lowestSpendingDay === null
            )
                <p class="monthly-insights__trend-empty">
                    No spending recorded for this period.
                </p>
            @else
                <div class="monthly-insights__trend-legend">
                    <div class="monthly-insights__trend-legend-item">
                        <span
                            class="
                                monthly-insights__trend-legend-dot
                                monthly-insights__trend-legend-dot--highest
                            "
                            aria-hidden="true"
                        ></span>

                        <span>
                            Highest
                        </span>

                        <strong>
                            ¥{{ number_format(
                                $highestSpendingDay['amount']
                            ) }}
                        </strong>

                        <time datetime="{{ $highestSpendingDay['date'] }}">
                            ({{ \Illuminate\Support\Carbon::parse(
                                $highestSpendingDay['date']
                            )->format('Y/n/j') }})
                        </time>
                    </div>

                    <div class="monthly-insights__trend-legend-item">
                        <span
                            class="
                                monthly-insights__trend-legend-dot
                                monthly-insights__trend-legend-dot--lowest
                            "
                            aria-hidden="true"
                        ></span>

                        <span>
                            Lowest
                        </span>

                        <strong>
                            ¥{{ number_format(
                                $lowestSpendingDay['amount']
                            ) }}
                        </strong>

                    <time datetime="{{ $lowestSpendingDay['date'] }}">
                        ({{ \Illuminate\Support\Carbon::parse(
                            $lowestSpendingDay['date']
                        )->format('Y/n/j') }})
                    </time>
                    </div>
                </div>

                <div class="monthly-insights__trend-chart-wrapper">
                    <canvas
                        class="monthly-insights__trend-chart"
                        data-monthly-spending-chart
                        data-daily-url-template="{{ route(
                            'insights.daily',
                            [
                                'date' => '__DATE__',
                            ]
                        ) }}"
                        aria-label="
                            Daily spending bar chart for the selected period
                        "
                        role="img"
                    >
                        Daily spending chart
                    </canvas>
                </div>

                <div class="monthly-insights__trend-guide">
                    <img
                        class="monthly-insights__trend-guide-icon"
                        src="{{ asset(
                            'images/icons/Information_1.svg'
                        ) }}"
                        alt=""
                        aria-hidden="true"
                    >

                    <p>
                        Tap a bar to view that day’s details.
                    </p>
                </div>

                <script
                    type="application/json"
                    data-monthly-spending-trend-data
                >
                    @json($dailySpendingTrend)
                </script>
            @endif
        </div>
    </section>
 
    {{-- 累積支出の進捗 --}}
    <section
        class="monthly-insights__progress"
        aria-labelledby="monthly-insights-progress-title"
    >
        <div class="monthly-insights__progress-heading">
            <h2
                id="monthly-insights-progress-title"
                class="monthly-insights__section-title"
            >
                Cumulative progress
            </h2>

            <p class="monthly-insights__progress-period">
                Budget period: 
                {{ $periodStartLabel }}
                -
                {{ $periodEndLabel }}
                ({{ $periodDays }} days)
            </p>
        </div>

        <div class="monthly-insights__progress-card">
            {{-- 金額サマリー --}}
            <div class="monthly-insights__progress-summary">
                <div class="monthly-insights__progress-summary-item">
                    <p class="monthly-insights__progress-summary-label">
                        Total spent
                    </p>

                    <p class="monthly-insights__progress-summary-amount">
                        ¥{{ number_format($currentPeriodTotal) }}
                    </p>

                    <p class="monthly-insights__progress-summary-note">
                        ({{ $budgetUsagePercentage }}% of budget)
                    </p>
                </div>

                <div class="monthly-insights__progress-summary-item">
                    @if ($overBudgetAmount > 0)
                        <p class="monthly-insights__progress-summary-label">
                            Over budget
                        </p>

                        <p
                            class="
                                monthly-insights__progress-summary-amount
                                is-over
                            "
                        >
                            +¥{{ number_format($overBudgetAmount) }}
                        </p>
                    @else
                        <p class="monthly-insights__progress-summary-label">
                            Within budget
                        </p>

                        <p
                            class="
                                monthly-insights__progress-summary-amount
                                is-within
                            "
                        >
                            ¥0
                        </p>
                    @endif
                </div>

                <div class="monthly-insights__progress-summary-item">
                    @if ($overLimitAmount > 0)
                        <p class="monthly-insights__progress-summary-label">
                            Over limit
                        </p>

                        <p
                            class="
                                monthly-insights__progress-summary-amount
                                is-over
                            "
                        >
                            +¥{{ number_format($overLimitAmount) }}
                        </p>
                    @else
                        <p class="monthly-insights__progress-summary-label">
                            Remaining to limit
                        </p>

                        <p class="monthly-insights__progress-summary-amount">
                            ¥{{ number_format(
                                $remainingToLimitAmount
                            ) }}
                        </p>
                    @endif
                </div>
            </div>

            {{--
                プログレスバーと各金額の位置。

                Controllerで計算した割合を、
                CSSカスタムプロパティとしてSCSSへ渡す。
            --}}
            <div
                class="monthly-insights__progress-visual"
                style="
                    --progress-eighty-position:
                        {{ $eightyPercentPosition }}%;

                    --progress-budget-position:
                        {{ $monthlyBudgetPosition }}%;

                    --progress-limit-position:
                        {{ $spendingLimitPosition }}%;

                    --progress-current-position:
                        {{ $currentSpendingPosition }}%;
                "
            >
                <div
                    class="monthly-insights__progress-bar"
                    role="meter"
                    aria-label="Current spending progress"
                    aria-valuemin="0"
                    aria-valuemax="{{ $spendingLimit }}"
                    aria-valuenow="{{ min(
                        $currentPeriodTotal,
                        $spendingLimit
                    ) }}"
                    aria-valuetext="
                        ¥{{ number_format($currentPeriodTotal) }}
                        spent.
                        Monthly budget:
                        ¥{{ number_format($monthlyBudget) }}.
                        Spending limit:
                        ¥{{ number_format($spendingLimit) }}.
                    "
                >
                    <span
                        class="
                            monthly-insights__progress-segment
                            monthly-insights__progress-segment--within
                        "
                        aria-hidden="true"
                    ></span>

                    <span
                        class="
                            monthly-insights__progress-segment
                            monthly-insights__progress-segment--slightly-high
                        "
                        aria-hidden="true"
                    ></span>

                    <span
                        class="
                            monthly-insights__progress-segment
                            monthly-insights__progress-segment--over-budget
                        "
                        aria-hidden="true"
                    ></span>

                    <span
                        class="
                            monthly-insights__progress-segment
                            monthly-insights__progress-segment--over-limit
                        "
                        aria-hidden="true"
                    ></span>

                    {{-- 現在の支出位置 --}}
                    <span
                        class="monthly-insights__progress-current"
                        aria-hidden="true"
                    >
                        <span class="monthly-insights__progress-current-label">
                            Current
                        </span>

                        <span class="monthly-insights__progress-current-line">
                        </span>

                        <span class="monthly-insights__progress-current-dot">
                        </span>
                    </span>
                </div>

                {{-- バーの下に表示する基準金額 --}}
                <div class="monthly-insights__progress-scale">
                    <span class="monthly-insights__progress-zero">
                        ¥0
                    </span>

                    <div
                        class="
                            monthly-insights__progress-marker
                            monthly-insights__progress-marker--eighty
                        "
                    >
                        <img
                            class="monthly-insights__progress-marker-arrow"
                            src="{{ asset('images/icons/allow-up_2.svg') }}"
                            alt=""
                            aria-hidden="true"
                        >
                        <span class="monthly-insights__progress-marker-label">
                            80% of budget
                        </span>

                        <strong class="monthly-insights__progress-marker-amount">
                            ¥{{ number_format($eightyPercentBudget) }}
                        </strong>
                    </div>

                    <div
                        class="
                            monthly-insights__progress-marker
                            monthly-insights__progress-marker--budget
                        "
                    >
                        <img
                            class="monthly-insights__progress-marker-arrow"
                            src="{{ asset('images/icons/allow-up_2.svg') }}"
                            alt=""
                            aria-hidden="true"
                        >
                        <span class="monthly-insights__progress-marker-label">
                            Monthly Budget
                        </span>

                        <strong class="monthly-insights__progress-marker-amount">
                            ¥{{ number_format($monthlyBudget) }}
                        </strong>
                    </div>

                    <div
                        class="
                            monthly-insights__progress-marker
                            monthly-insights__progress-marker--limit
                        "
                    >
                        <img
                            class="monthly-insights__progress-marker-arrow"
                            src="{{ asset('images/icons/allow-up_2.svg') }}"
                            alt=""
                            aria-hidden="true"
                        >
                        <span class="monthly-insights__progress-marker-label">
                            Spending Limit
                        </span>

                        <strong class="monthly-insights__progress-marker-amount">
                            ¥{{ number_format($spendingLimit) }}
                        </strong>
                    </div>
                </div>
            </div>

            {{-- 色の意味 --}}
            <ul
                class="monthly-insights__progress-legend"
                aria-label="Spending progress legend"
            >
                <li class="monthly-insights__progress-legend-item">
                    <span
                        class="
                            monthly-insights__progress-legend-color
                            monthly-insights__progress-legend-color--within
                        "
                        aria-hidden="true"
                    ></span>

                    <span class="monthly-insights__progress-legend-text">
                        <strong>Within budget</strong>
                        <span>Up to 80% of budget</span>
                    </span>
                </li>

                <li class="monthly-insights__progress-legend-item">
                    <span
                        class="
                            monthly-insights__progress-legend-color
                            monthly-insights__progress-legend-color--slightly-high
                        "
                        aria-hidden="true"
                    ></span>

                    <span class="monthly-insights__progress-legend-text">
                        <strong>Slightly high</strong>
                        <span>80%-100% of budget</span>
                    </span>
                </li>

                <li class="monthly-insights__progress-legend-item">
                    <span
                        class="
                            monthly-insights__progress-legend-color
                            monthly-insights__progress-legend-color--over-budget
                        "
                        aria-hidden="true"
                    ></span>

                    <span class="monthly-insights__progress-legend-text">
                        <strong>Over budget</strong>
                        <span>Above budget, up to the limit</span>
                    </span>
                </li>

                <li class="monthly-insights__progress-legend-item">
                    <span
                        class="
                            monthly-insights__progress-legend-color
                            monthly-insights__progress-legend-color--over-limit
                        "
                        aria-hidden="true"
                    ></span>

                    <span class="monthly-insights__progress-legend-text">
                        <strong>Over limit</strong>
                        <span>Above limit</span>
                    </span>
                </li>
            </ul>
        </div>
    </section>
</section>