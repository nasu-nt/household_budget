@php
    $colorFields = [
        'all_good_color' => __('All good'),
        'slightly_high_color' => __('Slightly high'),
        'over_budget_color' => __('Over budget'),
        'over_limit_color' => __('Over limit'),
    ];
@endphp

<section
    class="settings-card appearance-settings-card"
    aria-labelledby="spending-status-colors-title"
>
    <div class="appearance-settings-card__content">
        <header class="appearance-settings-card__header">
            <h2
                id="spending-status-colors-title"
                class="settings-card__title"
            >
                {{ __('Spending Status Colors') }}
            </h2>

            <p class="settings-card__description">
                {{ __('Choose the colors used to represent spending status across the calendar and insights.') }}
            </p>
        </header>

        <form
            method="POST"
            action="{{ route('settings.appearance.update') }}"
            class="appearance-settings-form"
            data-appearance-settings
        >
            @csrf
            @method('PATCH')

            <div class="appearance-settings-form__colors">
                @foreach ($colorFields as $field => $label)
                    @php
                        $color = old(
                            $field,
                            $appearanceSetting->{$field}
                        );
                    @endphp

                    <div class="appearance-settings-form__row">
                        <label
                            for="{{ $field }}-code"
                            class="appearance-settings-form__label"
                        >
                            {{ $label }}
                        </label>

                        <div class="appearance-settings-form__control">
                            <div
                                class="color-picker color-picker--compact
                                    appearance-settings-form__color-picker"
                                data-color-pair
                                data-default-color="{{ \App\Models\AppearanceSetting::DEFAULT_COLORS[$field] }}"
                            >
                                <div class="color-picker__control">
                                    <input
                                        id="{{ $field }}-picker"
                                        type="color"
                                        value="{{ $color }}"
                                        class="color-picker__input"
                                        data-color-input
                                        aria-label="{{ __(':status color picker', [
                                            'status' => $label,
                                        ]) }}"
                                        aria-controls="{{ $field }}-code"
                                    >

                                    <input
                                        id="{{ $field }}-code"
                                        type="text"
                                        name="{{ $field }}"
                                        value="{{ strtoupper($color) }}"
                                        class="color-picker__code
                                            @error($field) color-picker__code--error @enderror"
                                        data-color-code
                                        maxlength="7"
                                        pattern="^#[0-9A-Fa-f]{6}$"
                                        inputmode="text"
                                        autocomplete="off"
                                        spellcheck="false"
                                        @error($field)
                                            aria-invalid="true"
                                            aria-describedby="{{ $field }}-error"
                                        @enderror
                                    >
                                </div>
                            </div>

                            @error($field)
                                <p
                                    id="{{ $field }}-error"
                                    class="settings-form__error
                                        appearance-settings-form__error"
                                    role="alert"
                                >
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="appearance-settings-form__actions">
                <button
                    type="button"
                    class="settings-form__button
                        appearance-settings-form__button
                        appearance-settings-form__button--secondary"
                    data-reset-colors
                >
                    {{ __('Reset to Default') }}
                </button>

                <button
                    type="submit"
                    class="settings-form__button
                        appearance-settings-form__button"
                >
                    {{ __('Save Appearance Settings') }}
                </button>
            </div>
        </form>
    </div>
</section>
