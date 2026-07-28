// カラーピッカーからカラーコードを取り出す
export function initColorPickers() {
    const colorPickers = document.querySelectorAll('.color-picker');

    colorPickers.forEach((colorPicker) => {
        const input = colorPicker.querySelector('.color-picker__input');
        const value = colorPicker.querySelector('.color-picker__value');

        if (!(input instanceof HTMLInputElement) || !(value instanceof HTMLElement)) {
            return;
        }

        const updateValue = () => {
            value.textContent = input.value.toUpperCase();
        };

        input.addEventListener('input', updateValue);
        updateValue();
    });
}