export function initToast() {
    const toast = document.querySelector('[data-toast]');
    if (!toast) return;

    const closeBtn = toast.querySelector('[data-toast-close]');
    const rawDuration = Number(toast.dataset.toastDuration);
    const duration = Number.isNaN(rawDuration) ? 5000 : rawDuration;    // デフォルトは5s

    let fadeTimer;
    let hideTimer;
    let closed = false;

    const hideToast = () => {
        if (closed) return;
        closed = true;

        clearTimeout(fadeTimer);
        clearTimeout(hideTimer);

        toast.classList.add('is-hidden');
    };

    // 「x」ボタンが押されたら閉じる
    if (closeBtn) {
        closeBtn.addEventListener('click', hideToast);
    }

    // フェードアウト（0.5s）
    fadeTimer = setTimeout(() => {
        // 既に閉じている場合は処理を実行しない
        if (closed) return;
        toast.classList.add('is-fading');
    }, duration);

    hideTimer = setTimeout(hideToast, duration + 500); // closed = true にして終了
}