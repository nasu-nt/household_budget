@php
    $isDemoUser = auth()->user()?->email
        === config('demo.email', 'demo@example.com');
@endphp

<x-app-layout>
    <div class="insights">
        <nav
            class="insights-tabs"
            aria-label="Insights period"
        >
            <a
                href="{{ route('insights.daily', [
                    'date' => now()->format('Y-m-d'),
                ]) }}"
                class="insights-tabs__item
                    {{ $activeView === 'daily' ? 'is-active' : '' }}"
                @if ($activeView === 'daily')
                    aria-current="page"
                @endif
            >
                Daily
            </a>

            <a
                href="{{ route('insights.monthly.current') }}"
                class="insights-tabs__item
                    {{ $activeView === 'monthly' ? 'is-active' : '' }}"
                @if ($activeView === 'monthly')
                    aria-current="page"
                @endif
            >
                Monthly
            </a>
        </nav>

        @if ($isDemoUser)
            <div
                class="insights-demo"
                tabindex="0"
            >
                <span class="insights-demo__trigger">
                    <img
                        class="insights-demo__icon"
                        src="{{ asset('images/icons/Information_1.svg') }}"
                        alt=""
                        aria-hidden="true"
                    >

                    <span class="insights-demo__label">
                        Demo Account
                    </span>
                </span>

                <div
                    class="insights-demo__tooltip"
                    role="tooltip"
                >
                   <p>This account contains sample data.</p>

                    @if ($activeView === 'daily')
                        <p>
                            Daily Insights is available from
                            May 28 to June 27, 2026.
                        </p>

                        <p>
                            The Today button returns to June 19, 2026.
                        </p>
                    @else
                        <p>
                            Monthly Insights shows data from
                            May 28 to June 27, 2026.
                        </p>

                        <p>
                            You can open Daily Insights for each day
                            in this period.
                        </p>

                        <p>
                            The displayed reporting period does not change.
                        </p>
                    @endif

                    <p>You can add, edit, and delete sample data.</p>
                    <p>Sample data may be reset from time to time.</p>
                </div>
            </div>
        @endif

        <main class="insights__panel">
            @if ($activeView === 'monthly')
                @include('insights.partials._monthly')
            @else
                @include('insights.partials._daily')
            @endif
        </main>
    </div>
</x-app-layout>