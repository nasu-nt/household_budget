// password マスク切り替え
document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('toggle-password-icon');

    toggleButton.addEventListener('click', function () {
        const isHidden = passwordInput.type === 'password';
        passwordInput.type = isHidden ? 'text' : 'password';
        passwordIcon.src = isHidden
            ? '/images/icons/eye.svg'
            : '/images/icons/eye-slash.svg';
    });
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
});