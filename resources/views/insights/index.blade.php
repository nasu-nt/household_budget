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
                href="{{ route('insights.monthly', [
                    'month' => now()->format('Y-m'),
                ]) }}"
                class="insights-tabs__item
                    {{ $activeView === 'monthly' ? 'is-active' : '' }}"
                @if ($activeView === 'monthly')
                    aria-current="page"
                @endif
            >
                Monthly
            </a>
        </nav>

        <main class="insights__panel">
            @if ($activeView === 'monthly')
                @include('insights.partials._monthly')
            @else
                @include('insights.partials._daily')
            @endif
        </main>
    </div>
</x-app-layout>