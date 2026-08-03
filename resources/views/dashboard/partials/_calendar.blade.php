@php
    $isDemoUser = auth()->user()?->email
        === config('demo.email', 'demo@example.com');

    $dashboardDate = $isDemoUser
        ? '2026-06-27'
        : now()->format('Y-m-d');

    $dashboardDateObject = \Illuminate\Support\Carbon::parse(
        $dashboardDate,
    );

    $dashboardMonthLabel = $dashboardDateObject->format('F Y');
    $dashboardSpendingLabel = $dashboardDateObject->format('M j');
@endphp

<section
    class="dashboard-calendar"
    aria-label="Monthly spending calendar"
>
    <header class="dashboard-calendar__summary">
        <div class="dashboard-calendar__controls">
            <button
                type="button"
                class="dashboard-calendar__nav-button"
                data-dashboard-calendar-prev
                aria-label="Previous month"
            >
                ◀
            </button>

            {{--
                月次Insightsへの仮リンク。
                実装後にhrefを実際のURLへ変更する。
            --}}
            <a
                href="#"
                class="dashboard-calendar__month-link"
                data-dashboard-calendar-month-link
                aria-label="View monthly insights for {{ $dashboardMonthLabel }}"
            >
                {{ $dashboardMonthLabel }}
            </a>

            <div class="dashboard-calendar__date-picker-wrapper">
                <input
                    type="date"
                    id="dashboard-calendar-date"
                    class="dashboard-calendar__date-input"
                    value="{{ $dashboardDate }}"
                    data-dashboard-calendar-date
                >

                <button
                    type="button"
                    class="dashboard-calendar__date-picker"
                    data-dashboard-calendar-date-picker
                    aria-label="Select date"
                >
                    <img
                        class="dashboard-calendar__date-icon"
                        src="{{ asset('images/icons/calendar_1.svg') }}"
                        alt=""
                        aria-hidden="true"
                    >
                </button>
            </div>

            <button
                type="button"
                class="dashboard-calendar__nav-button"
                data-dashboard-calendar-next
                aria-label="Next month"
            >
                ▶
            </button>
        </div>

        <div class="dashboard-calendar__summary-right">
            @if ($isDemoUser)
                <div
                    class="dashboard-calendar__demo"
                    tabindex="0"
                >
                    <span class="dashboard-calendar__demo-trigger">
                        <img
                            class="dashboard-calendar__demo-icon"
                            src="{{ asset('images/icons/Information_1.svg') }}"
                            alt=""
                            aria-hidden="true"
                        >

                        <span class="dashboard-calendar__demo-label">
                            Demo Account
                        </span>
                    </span>

                    <div
                        class="dashboard-calendar__demo-tooltip"
                        role="tooltip"
                    >
                        <p>This account contains sample data.</p>
                        <p>The calendar date is fixed to June 27, 2026.</p>
                        <p>Changes may be reset.</p>
                    </div>
                </div>
            @endif

            <p class="dashboard-calendar__spending">
                <span class="dashboard-calendar__spending-label">
                    <span
                        class="dashboard-calendar__spending-date"
                        data-dashboard-calendar-spending-date
                    >
                        {{ $dashboardSpendingLabel }}
                    </span>
                    spending:
                </span>
                <strong data-dashboard-calendar-spending>
                    ¥2,680
                </strong>
            </p>
        </div>
    </header>

    <div
        id="calendar"
        class="dashboard-calendar__body"
        data-dashboard-calendar
        data-dashboard-calendar-url="{{ route('dashboard.calendar') }}"
        @if ($isDemoUser)
            data-demo-date="{{ $dashboardDate }}"
        @endif
    ></div>

    <ul
        class="dashboard-calendar__legend"
        aria-label="Spending status legend"
    >
        <li class="dashboard-calendar__legend-item">
            <span
                class="dashboard-calendar__legend-color dashboard-calendar__legend-color--all-good"
                style="background-color: {{ $appearanceSetting->all_good_color }};"
                aria-hidden="true"
            ></span>

            <span>All good</span>
        </li>

        <li class="dashboard-calendar__legend-item">
            <span
                class="dashboard-calendar__legend-color dashboard-calendar__legend-color--slightly-high"
                style="background-color: {{ $appearanceSetting->slightly_high_color }};"
                aria-hidden="true"
            ></span>

            <span>Slightly high</span>
        </li>

        <li class="dashboard-calendar__legend-item">
            <span
                class="dashboard-calendar__legend-color dashboard-calendar__legend-color--over-budget"
                style="background-color: {{ $appearanceSetting->over_budget_color }};"
                aria-hidden="true"
            ></span>

            <span>Over budget</span>
        </li>

        <li class="dashboard-calendar__legend-item">
            <span
                class="dashboard-calendar__legend-color dashboard-calendar__legend-color--over-limit"
                style="background-color: {{ $appearanceSetting->over_limit_color }};"
                aria-hidden="true"
            ></span>

            <span>Over limit</span>
        </li>
    </ul>
</section>