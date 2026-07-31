@php
    $statusRows = [
        [
            'label' => __('All good'),
            'color' => $appearanceSetting->all_good_color,
            'daily' => __('Below 80% of the daily guideline'),
            'monthly' => __('Below 80% of the cumulative budget pace'),
        ],
        [
            'label' => __('Slightly high'),
            'color' => $appearanceSetting->slightly_high_color,
            'daily' => __('80%–100% of the daily guideline'),
            'monthly' => __('80%–100% of the cumulative budget pace'),
        ],
        [
            'label' => __('Over budget'),
            'color' => $appearanceSetting->over_budget_color,
            'daily' => __(
                'Above the daily guideline, up to the daily limit'
            ),
            'monthly' => __(
                'Above the cumulative budget pace, up to the cumulative limit pace'
            ),
        ],
        [
            'label' => __('Over limit'),
            'color' => $appearanceSetting->over_limit_color,
            'daily' => __('Above the daily limit'),
            'monthly' => __('Above the cumulative limit pace'),
        ],
    ];
@endphp

<section
    class="budget-guide"
    aria-labelledby="status-guide-title"
>
    <div class="budget-guide__summary">
        <div class="budget-guide__metric">
            <h3 class="budget-guide__metric-title">
                {{ __('Daily Spending Guideline') }}
            </h3>

            <p class="budget-guide__metric-value">
                <strong
                    x-text="formatCurrency(dailyGuideline)"
                ></strong>

                <span>{{ __('per day') }}</span>
            </p>
        </div>

        <div class="budget-guide__metric">
            <h3 class="budget-guide__metric-title">
                {{ __('Daily Spending Limit') }}
            </h3>

            <p class="budget-guide__metric-value">
                <strong
                    x-text="formatCurrency(dailyLimit)"
                ></strong>

                <span>{{ __('per day') }}</span>
            </p>
        </div>
    </div>

    <div class="budget-guide__status">
        <h3
            id="status-guide-title"
            class="budget-guide__status-title"
        >
            {{ __('Status guide') }}
        </h3>

        <div
            class="budget-guide__table"
            role="table"
            aria-label="{{ __('Spending status guide') }}"
        >
            <div
                class="budget-guide__table-header"
                role="row"
            >
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>

                <strong role="columnheader">
                    {{ __('Daily') }}
                </strong>

                <strong role="columnheader">
                    {{ __('Monthly') }}
                </strong>
            </div>

            @foreach ($statusRows as $status)
                <div
                    class="budget-guide__table-row"
                    role="row"
                >
                    <span
                        class="budget-guide__status-label"
                        role="rowheader"
                    >
                        {{ $status['label'] }}
                    </span>

                    <span
                        class="budget-guide__swatch"
                        style="
                            --status-color:
                            {{ $status['color'] }}
                        "
                        aria-hidden="true"
                    ></span>

                    <span role="cell">
                        {{ $status['daily'] }}
                    </span>

                    <span role="cell">
                        {{ $status['monthly'] }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</section>