const HEX_COLOR_PATTERN = /^#[0-9A-F]{6}$/;

function normalizeColorCode(value) {
    return value.trim().toUpperCase();
}

function syncColorPair(pair, color) {
    const colorInput = pair.querySelector('[data-color-input]');
    const colorCode = pair.querySelector('[data-color-code]');

    if (!colorInput || !colorCode) {
        return;
    }

    colorInput.value = color;
    colorCode.value = color;
}

export function initAppearanceStatus() {
    document.querySelectorAll('[data-appearance-settings]').forEach((form) => {
        if (form.dataset.appearanceSettingsInitialized === 'true') {
            return;
        }

        form.dataset.appearanceSettingsInitialized = 'true';

        const colorPairs = form.querySelectorAll('[data-color-pair]');

        colorPairs.forEach((pair) => {
            const colorInput = pair.querySelector('[data-color-input]');
            const colorCode = pair.querySelector('[data-color-code]');

            if (!colorInput || !colorCode) {
                return;
            }

            colorInput.addEventListener('input', () => {
                colorCode.value = colorInput.value.toUpperCase();
                colorCode.setCustomValidity('');
            });

            colorCode.addEventListener('input', () => {
                const color = normalizeColorCode(colorCode.value);

                colorCode.value = color;
                colorCode.setCustomValidity('');

                if (HEX_COLOR_PATTERN.test(color)) {
                    colorInput.value = color;
                }
            });

            colorCode.addEventListener('invalid', () => {
                colorCode.setCustomValidity(
                    'Enter a color code in the format #F8FAFC.',
                );
            });
        });

        form
            .querySelector('[data-reset-colors]')
            ?.addEventListener('click', () => {
                colorPairs.forEach((pair) => {
                    const defaultColor = pair.dataset.defaultColor;

                    if (defaultColor) {
                        syncColorPair(pair, defaultColor);
                    }
                });
            });
    });
}
