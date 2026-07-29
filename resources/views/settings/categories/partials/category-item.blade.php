@php
    $hasUpdateErrors = $errors->updateCategory->isNotEmpty()
        && (int) old('category_id') === $category->id;

    $categoryName = $hasUpdateErrors
        ? old('name', $category->name)
        : $category->name;

    $categoryColor = $hasUpdateErrors
        ? old('color_code', $category->color_code)
        : $category->color_code;

    $statusLabel = $category->is_active
        ? __('Active')
        : __('Disabled');
@endphp

<article
    class="category-item"
    role="listitem"
    x-data="{
        isEditing: @js($hasUpdateErrors),
        color: @js($categoryColor)
    }"
>
    <div
        class="category-item__summary"
        x-show="! isEditing"
        @if ($hasUpdateErrors) x-cloak @endif
    >
        <span class="category-item__name">
            {{ $category->name }}
        </span>

        <span class="category-item__color">
            <span
                class="category-item__swatch"
                style="--category-color: {{ $category->color_code }}"
                aria-hidden="true"
            ></span>
            <span class="sr-only">
                {{ __('Color: :color', [
                    'color' => strtoupper($category->color_code),
                ]) }}
            </span>
        </span>

        <span class="category-item__status">
            {{ $statusLabel }}
        </span>

        <button
            type="button"
            class="category-item__edit-button"
            x-on:click="isEditing = true"
            x-bind:aria-expanded="isEditing.toString()"
            aria-controls="category-editor-{{ $category->id }}"
        >
            {{ __('Edit') }}
        </button>
    </div>

    <form
        id="category-editor-{{ $category->id }}"
        method="post"
        action="{{ route('settings.categories.update', $category) }}"
        class="category-editor"
        x-show="isEditing"
        x-cloak
    >
        @csrf
        @method('patch')

        <input
            type="hidden"
            name="category_id"
            value="{{ $category->id }}"
        >

        <div class="category-editor__fields">
            <div class="category-editor__field category-editor__field--name">
                <label
                    for="category_name_{{ $category->id }}"
                    class="category-editor__label"
                >
                    {{ __('Name') }}
                </label>

                <input
                    id="category_name_{{ $category->id }}"
                    name="name"
                    type="text"
                    class="category-editor__input
                        @if ($hasUpdateErrors && $errors->updateCategory->has('name'))
                            is-invalid
                        @endif"
                    value="{{ $categoryName }}"
                    required
                    maxlength="50"
                    autocomplete="off"
                    @if ($hasUpdateErrors && $errors->updateCategory->has('name'))
                        aria-invalid="true"
                        aria-describedby="category-name-error-{{ $category->id }}"
                    @endif
                >

                @if ($hasUpdateErrors)
                    @error('name', 'updateCategory')
                        <p
                            id="category-name-error-{{ $category->id }}"
                            class="category-editor__error"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                @endif
            </div>

            <div class="category-editor__field category-editor__field--color">
                <label
                    for="category_color_{{ $category->id }}"
                    class="category-editor__label"
                >
                    {{ __('Color') }}
                </label>

                <div class="color-picker color-picker--compact">
                    <div class="color-picker__control">
                        <input
                            id="category_color_{{ $category->id }}"
                            name="color_code"
                            type="color"
                            class="color-picker__input
                                @if ($hasUpdateErrors && $errors->updateCategory->has('color_code'))
                                    is-invalid
                                @endif"
                            x-model="color"
                            required
                            @if ($hasUpdateErrors && $errors->updateCategory->has('color_code'))
                                aria-invalid="true"
                                aria-describedby="category-color-error-{{ $category->id }}"
                            @endif
                        >

                        <output
                            class="color-picker__value"
                            for="category_color_{{ $category->id }}"
                            x-text="color.toUpperCase()"
                        >
                            {{ strtoupper($categoryColor) }}
                        </output>
                    </div>
                </div>

                @if ($hasUpdateErrors)
                    @error('color_code', 'updateCategory')
                        <p
                            id="category-color-error-{{ $category->id }}"
                            class="category-editor__error"
                            role="alert"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                @endif
            </div>

            <div class="category-editor__save">
                <button
                    type="submit"
                    name="intent"
                    value="save"
                    class="category-editor__button category-editor__button--save"
                >
                    {{ __('Save') }}
                </button>
            </div>
        </div>

        <div class="category-editor__actions">
            <p class="category-editor__status">
                {{ __('Status: :status', ['status' => $statusLabel]) }}
            </p>

            <div class="category-editor__action-buttons">
                <button
                    type="button"
                    class="category-editor__button category-editor__button--cancel"
                    x-on:click="isEditing = false"
                >
                    {{ __('Cancel') }}
                </button>

                <button
                    type="submit"
                    name="intent"
                    value="{{ $category->is_active ? 'disable' : 'enable' }}"
                    class="category-editor__button category-editor__button--status"
                >
                    {{ $category->is_active ? __('Disable') : __('Enable') }}
                </button>
            </div>
        </div>
    </form>
</article>
