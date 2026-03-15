export function initToast() {
    const toast = document.querySelector('[data-toast]');

    if (!toast) return;

    const rawDuration = Number(toast.dataset.toastDuration);
    const duration = Number.isNaN(rawDuration) ? 3000 : rawDuration;

    // ちょっとゆっくりめ(0.5s)にフェードアウト
    setTimeout(() => {
        toast.classList.add('is-fading');
    }, duration);

    setTimeout(() => {
        toast.classList.add('is-hidden');
    }, duration + 500);
}