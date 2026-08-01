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

            <div class="dashboard-calendar__date-group">
                <div class="dashboard-calendar__date-wrapper">
                    <input
                        type="date"
                        id="dashboard-calendar-date"
                        class="dashboard-calendar__date"
                        value="{{ now()->format('Y-m-d') }}"
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

        <p class="dashboard-calendar__spending">
            <span>Today’s spending:</span>

            <strong data-dashboard-calendar-spending>
                ¥2,680
            </strong>
        </p>
    </header>

    <div
        id="dashboard-calendar"
        class="dashboard-calendar__body"
        data-dashboard-calendar
        data-dashboard-calendar-url="{{ route('dashboard.calendar') }}"
    ></div>

    <ul
        class="dashboard-calendar__legend"
        aria-label="Spending status legend"
    >
        <li class="dashboard-calendar__legend-item">
            <span
                class="dashboard-calendar__legend-color dashboard-calendar__legend-color--all-good"
                aria-hidden="true"
            ></span>
            <span>All good</span>
        </li>

        <li class="dashboard-calendar__legend-item">
            <span
                class="dashboard-calendar__legend-color dashboard-calendar__legend-color--slightly-high"
                aria-hidden="true"
            ></span>
            <span>Slightly high</span>
        </li>

        <li class="dashboard-calendar__legend-item">
            <span
                class="dashboard-calendar__legend-color dashboard-calendar__legend-color--over-budget"
                aria-hidden="true"
            ></span>
            <span>Over budget</span>
        </li>

        <li class="dashboard-calendar__legend-item">
            <span
                class="dashboard-calendar__legend-color dashboard-calendar__legend-color--over-limit"
                aria-hidden="true"
            ></span>
            <span>Over limit</span>
        </li>
    </ul>
</section>