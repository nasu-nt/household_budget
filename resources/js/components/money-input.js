const DIGITS_ONLY = /[^\d]/g;

function formatAmount(value) {
    const digits = value.replace(DIGITS_ONLY, '');
    return digits ? Number(digits).toLocaleString('ja-JP') : '';
}

export function initMoneyInputs() {
    document.querySelectorAll('[data-money-input]').forEach((input) => {
        input.addEventListener('input', () => {
            // カンマの増減でカーソル位置がずれるのを防ぐため、末尾からの距離で保持
            const posFromEnd = input.value.length - input.selectionStart;
            input.value = formatAmount(input.value);
            const newPos = input.value.length - posFromEnd;
            input.setSelectionRange(newPos, newPos);
        });

        // 送信直前にカンマを外して生の数値に戻す(サーバー側でバリデーションエラーにならないように)
        input.closest('form')?.addEventListener('submit', () => {
            input.value = input.value.replace(/,/g, '');
        });
    });
}