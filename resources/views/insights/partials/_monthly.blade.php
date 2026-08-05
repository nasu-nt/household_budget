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
</section>