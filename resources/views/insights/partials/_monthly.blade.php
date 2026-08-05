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
</section>