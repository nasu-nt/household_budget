@php
    $archivedCategoryConflict = session(
        'archived_category_conflict'
    );
    $newCategoryName = old('name', '');
    $newCategoryColor = old('color_code', '#808080');
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

    @if ($archivedCategoryConflict !== null)
        <div
            class="category-archive-prompt"
            role="alert"
            aria-labelledby="category-archive-prompt-title"
        >
            <div class="category-archive-prompt__content">
                <p
                    id="category-archive-prompt-title"
                    class="category-archive-prompt__title"
                >
                    {{ __('“:name” is archived.', [
                        'name' => $archivedCategoryConflict['name'],
                    ]) }}
                </p>

                <p class="category-archive-prompt__description">
                    {{ __('Restore the previous category or create a new category with the same name.') }}
                </p>
            </div>

            <div class="category-archive-prompt__actions">
                <form
                    method="post"
                    action="{{ route(
                        'settings.categories.restore',
                        $archivedCategoryConflict['id']
                    ) }}"
                >
                    @csrf
                    @method('patch')

                    <input
                        type="hidden"
                        name="color_code"
                        value="{{ $newCategoryColor }}"
                    >

                    <button
                        type="submit"
                        class="category-archive-prompt__button
                            category-archive-prompt__button--restore"
                    >
                        {{ __('Restore & Use') }}
                    </button>
                </form>

                <form
                    method="post"
                    action="{{ route('settings.categories.store') }}"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="intent"
                        value="create_new"
                    >

                    <input
                        type="hidden"
                        name="name"
                        value="{{ $newCategoryName }}"
                    >

                    <input
                        type="hidden"
                        name="color_code"
                        value="{{ $newCategoryColor }}"
                    >

                    <button
                        type="submit"
                        class="category-archive-prompt__button
                            category-archive-prompt__button--new"
                    >
                        {{ __('Use Another Category') }}
                    </button>
                </form>
            </div>
        </div>
    @endif

    <form
        method="post"
        action="{{ route('settings.categories.store') }}"
        class="settings-form category-form"
        x-data="{ color: @js($newCategoryColor) }"
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

                <div class="settings-form__control category-form__name-control">
                    <input
                        id="new_category_name"
                        name="name"
                        type="text"
                        class="settings-form__input
                            @error('name', 'storeCategory') is-invalid @enderror"
                        value="{{ $newCategoryName }}"
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
                                x-model="color"
                                required
                                @error('color_code', 'storeCategory')
                                    aria-invalid="true"
                                    aria-describedby="new-category-color-error"
                                @enderror
                            >

                            <output
                                class="color-picker__value"
                                for="new_category_color"
                                x-text="color.toUpperCase()"
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
