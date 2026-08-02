const DIGITS_ONLY = /[^\d]/g;

function formatAmount(value) {
    const digits = value.replace(DIGITS_ONLY, '');

    return digits
        ? Number(digits).toLocaleString('ja-JP')
        : '';
}

export function initMoneyInputs() {
    document.querySelectorAll('[data-money-input]').forEach((input) => {
        // バリデーションエラー後のold値も、初期表示時にカンマ区切りにする
        input.value = formatAmount(input.value);

        input.addEventListener('input', () => {
            // カンマを増減してもカーソルが大きくずれないよう、
            // 入力欄の末尾からの距離を保存する
            const selectionStart = input.selectionStart ?? input.value.length;
            const positionFromEnd =
                input.value.length - selectionStart;

            input.value = formatAmount(input.value);

            const newPosition = Math.max(
                input.value.length - positionFromEnd,
                0
            );

            input.setSelectionRange(
                newPosition,
                newPosition
            );
        });

        // Laravelへ送信する直前にカンマを削除する
        input.closest('form')?.addEventListener('submit', () => {
            input.value = input.value.replace(/,/g, '');
        });
    });
}