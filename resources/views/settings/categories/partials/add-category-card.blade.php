@php
    $hasStoreErrors = $errors->storeCategory->isNotEmpty();
    $newCategoryColor = $hasStoreErrors
        ? old('color_code', '#808080')
        : '#808080';
@endphp

<section class="settings-card category-settings-card">
    <header class="settings-card__header">
        <h2 class="settings-card__title">
            {{ __('Add New Category') }}
        </h2>

        <p class="settings-card__description">
            {{ __('Add a category for expense records and budgets.') }}
        </p>
    </header>

    <form
        method="post"
        action="{{ route('settings.categories.store') }}"
        class="settings-form category-form"
    >
        @csrf

        <div class="settings-form__fields">
            <div class="settings-form__row">
                <label
                    for="new_category_name"
                    class="settings-form__label"
                >
                    {{ __('Category Name') }}
                </label>

                <div class="settings-form__control">
                    <input
                        id="new_category_name"
                        name="name"
                        type="text"
                        class="settings-form__input
                            @error('name', 'storeCategory') is-invalid @enderror"
                        value="{{ $hasStoreErrors ? old('name') : '' }}"
                        required
                        maxlength="50"
                        autocomplete="off"
                        @error('name', 'storeCategory')
                            aria-invalid="true"
                            aria-describedby="new-category-name-error"
                        @enderror
                    >

                    @error('name', 'storeCategory')
                        <p
                            id="new-category-name-error"
                            class="settings-form__error"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div class="settings-form__row">
                <label
                    for="new_category_color"
                    class="settings-form__label"
                >
                    {{ __('Category Color') }}
                </label>

                <div class="settings-form__control">
                    <div class="color-picker color-picker--compact">
                        <div class="color-picker__control">
                            <input
                                id="new_category_color"
                                name="color_code"
                                type="color"
                                class="color-picker__input
                                    @error('color_code', 'storeCategory') is-invalid @enderror"
                                value="{{ $newCategoryColor }}"
                                required
                                @error('color_code', 'storeCategory')
                                    aria-invalid="true"
                                    aria-describedby="new-category-color-error"
                                @enderror
                            >

                            <output
                                class="color-picker__value"
                                for="new_category_color"
                            >
                                {{ strtoupper($newCategoryColor) }}
                            </output>
                        </div>
                    </div>

                    @error('color_code', 'storeCategory')
                        <p
                            id="new-category-color-error"
                            class="settings-form__error"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="settings-form__actions">
            <div class="settings-form__action-content">
                <button
                    type="submit"
                    class="settings-form__button"
                >
                    {{ __('Add Category') }}
                </button>
            </div>
        </div>
    </form>
</section>
