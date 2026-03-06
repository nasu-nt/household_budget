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

// ログインエラー文の削除
document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const errorMessages = document.querySelectorAll('.error-message');

    if (passwordInput) {
        passwordInput.addEventListener('input', function () {
            passwordInput.classList.remove('is-invalid');

            errorMessages.forEach(function (message) {
                message.style.display = 'none';
            });
        });
    }

    const confirmPasswordInput = document.getElementById('password_confirmation');
    if(confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function () {
            confirmPasswordInput.classList.remove('is-invalid');

            errorMessages.forEach(function (message) {
                message.style.display = 'none';
            });
        });
    }
});