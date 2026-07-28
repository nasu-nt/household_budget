<header class="header app-header">
    <div class="header__inner">
        <a href="{{ route('dashboard') }}" class="header__title app-header__title">
            HOUSEHOLD BUDGET
        </a>

        <nav class="app-header__nav" aria-label="Main navigation">
            {{-- Dashboard --}}
            <a
                href="{{ route('dashboard') }}"
                class="app-header__link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}"
                @if (request()->routeIs('dashboard')) aria-current="page" @endif
            >
                Dashboard
            </a>
            {{-- Insights --}}
            <a
                href=""
                class="app-header__link {{ request()->routeIs('insights.*') ? 'is-active' : '' }}"
                @if (request()->routeIs('insights.*')) aria-current="page" @endif
            >
                Insights
            </a>
            {{-- Settings --}}
            <a
                href="{{ route('settings.index') }}"
                class="app-header__link {{ request()->routeIs('settings.*') ? 'is-active' : '' }}"
                @if (request()->routeIs('settings.*')) aria-current="page" @endif
            >
                Settings
            </a>
            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}" class="app-header__logout-form">
                @csrf
                <button type="submit" class="app-header__link">Logout</button>
            </form>
        </nav>
    </div>
</header>