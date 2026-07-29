@php
    $settingsMenuItems = [
        [
            'label' => __('Account'),
            'description' => __('Name, email, password, and security settings.'),
            'route' => 'settings.profile.edit',
            'active' => 'settings.profile.*',
        ],
        [
            'label' => __('Categories'),
            'description' => __('Manage categories used for expense records and budgets.'),
            'route' => 'settings.categories.index',
            'active' => 'settings.categories.*',
        ],
        [
            'label' => __('Budget'),
            'description' => __('Monthly budget, closing day, and spending rules.'),
            'route' => 'settings.budget.edit',
            'active' => 'settings.budget.*',
        ],
        [
            'label' => __('Appearance'),
            'description' => __('Customize colors used to show spending status.'),
            'route' => 'settings.appearance.edit',
            'active' => 'settings.appearance.*',
        ],
        [
            'label' => __('Subscriptions'),
            'description' => __('Recurring payments, billing dates, and monthly costs.'),
            'route' => 'settings.subscriptions.index',
            'active' => 'settings.subscriptions.*',
        ],
    ];
@endphp

<nav
    class="settings-menu"
    aria-labelledby="settings-menu-title"
>
    <h2
        id="settings-menu-title"
        class="settings-menu__title"
    >
        {{ __('Settings') }}
    </h2>

    <ul class="settings-menu__list">
        @foreach ($settingsMenuItems as $item)
            @php
                $routeExists = \Illuminate\Support\Facades\Route::has($item['route']);
                $isActive = request()->routeIs($item['active']);
            @endphp

            <li class="settings-menu__list-item">
                @if ($routeExists && ! $isActive)
                    <a
                        href="{{ route($item['route']) }}"
                        class="settings-menu__item"
                    >
                        <span class="settings-menu__label">
                            {{ $item['label'] }}
                        </span>
                    </a>
                @elseif ($isActive)
                    <span
                        class="settings-menu__item is-active"
                        aria-current="page"
                    >
                        <span class="settings-menu__label">
                            {{ $item['label'] }}
                        </span>
                    </span>
                @else
                    <span
                        class="settings-menu__item is-disabled"
                        aria-disabled="true"
                    >
                        <span class="settings-menu__label">
                            {{ $item['label'] }}
                        </span>
                    </span>
                @endif

                <span class="settings-menu__description">
                    {{ $item['description'] }}
                </span>
            </li>
        @endforeach
    </ul>
</nav>