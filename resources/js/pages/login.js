// password マスク切り替え
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
      icon.src = isHidden ? '/images/icons/eye.svg' : '/images/icons/eye-slash.svg';
    }
});

// 各フォームのエラー文の削除
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('[data-error-target]');

    inputs.forEach(function (input) {
        input.addEventListener('input', function () {
            input.classList.remove('is-invalid');

            const key = input.dataset.errorTarget;
            const errorMessage = document.querySelector(`[data-error-message="${key}"]`);

            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        });
    });
});