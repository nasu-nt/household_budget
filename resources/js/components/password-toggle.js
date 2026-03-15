// password マスク切り替え
export function initPasswordToggle() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-toggle-password]');
        if (!btn) return;

        const selector = btn.getAttribute('data-target');
        const input = document.querySelector(selector);
        if (!input) return;

        const icon = btn.querySelector('img');
        const isHidden = input.type === 'password';

        input.type = isHidden ? 'text' : 'password';
        btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');

        if (icon) {
            icon.src = isHidden
                ? '/images/icons/eye.svg'
                : '/images/icons/eye-slash.svg';
        }
    });
}