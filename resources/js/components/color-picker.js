// カラーピッカーの表示値を更新する
export function initColorPickers() {
    const colorPickers = document.querySelectorAll('.color-picker');

    colorPickers.forEach((colorPicker) => {
        const input = colorPicker.querySelector('.color-picker__input');
        const output = colorPicker.querySelector('.color-picker__value');

        if (
            !(input instanceof HTMLInputElement)
            || !(output instanceof HTMLElement)
        ) {
            return;
        }

        const updateColorValue = () => {
            output.textContent = input.value.toUpperCase();
        };

        input.addEventListener('input', updateColorValue);
        input.addEventListener('change', updateColorValue);

        updateColorValue();
    });
}