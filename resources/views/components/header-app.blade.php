<header class="header app-header">
    <div class="header__inner">
        <a href="{{ route('dashboard') }}" class="title app-header__title">
            HOUSEHOLD BUDGET
        </a>

        <nav class="app-header__nav" aria-label="Main navigation">
            {{-- Dashboard --}}
            <a
                href="{{ route('dashboard') }}"
                class="app-header__link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}"
            >
                Dashboard
            </a>
            {{-- Insights --}}
            <a
                href=""
                class="app-header__link {{ request()->routeIs('insights.*') ? 'is-active' : '' }}"
            >
                Insights
            </a>
            {{-- Settings --}}
            <a
                href=""
                class="app-header__link {{ request()->routeIs('settings.*') ? 'is-active' : '' }}"
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