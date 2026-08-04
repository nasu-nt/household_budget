const NON_DIGIT_PATTERN = /\D/g;

let isInitialized = false;

function formatAmount(input) {
    const maxDigits = Number.parseInt(
        input.dataset.maxDigits ?? '10',
        10,
    );

    const digits = input.value
        .normalize('NFKC')
        .replace(NON_DIGIT_PATTERN, '')
        .slice(0, maxDigits);

    return digits === ''
        ? ''
        : Number(digits).toLocaleString('ja-JP');
}

function formatMoneyInput(input) {
    const selectionStart =
        input.selectionStart ?? input.value.length;

    const positionFromEnd =
        input.value.length - selectionStart;

    input.value = formatAmount(input);

    const newPosition = Math.max(
        input.value.length - positionFromEnd,
        0,
    );

    input.setSelectionRange(
        newPosition,
        newPosition,
    );
}

function getMoneyInput(target) {
    if (!(target instanceof HTMLInputElement)) {
        return null;
    }

    if (!target.matches('[data-money-input]')) {
        return null;
    }

    return target;
}

export function initMoneyInputs() {
    if (isInitialized) {
        return;
    }

    isInitialized = true;

    document.querySelectorAll(
        '[data-money-input]',
    ).forEach((input) => {
        if (!(input instanceof HTMLInputElement)) {
            return;
        }

        input.value = formatAmount(input);
    });

    document.addEventListener('input', (event) => {
        const input = getMoneyInput(event.target);

        if (!input) {
            return;
        }

        /*
         * IMEで全角数字を入力している途中では、
         * 文字を削除・整形しない。
         */
        if (
            event instanceof InputEvent
            && event.isComposing
        ) {
            return;
        }

        formatMoneyInput(input);
    });

    /*
     * IMEの変換確定後に、全角数字を半角へ変換する。
     */
    document.addEventListener(
        'compositionend',
        (event) => {
            const input = getMoneyInput(event.target);

            if (!input) {
                return;
            }

            formatMoneyInput(input);
        },
    );

    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        form.querySelectorAll(
            '[data-money-input]',
        ).forEach((input) => {
            if (!(input instanceof HTMLInputElement)) {
                return;
            }

            input.value = input.value.replace(
                /,/g,
                '',
            );
        });
    });
}