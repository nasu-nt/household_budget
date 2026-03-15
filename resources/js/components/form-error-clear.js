// 各フォームのエラー文の削除
export function initFormErrorClear() {
    const inputs = document.querySelectorAll('[data-error-target]');

    inputs.forEach((input) => {
        input.addEventListener('input', () => {
            input.classList.remove('is-invalid');

            const key = input.dataset.errorTarget;
            const errorMessage = document.querySelector(
                `[data-error-message="${key}"]`
            );

            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        });
    });
}