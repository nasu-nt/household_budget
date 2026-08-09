/**
 * Login画面を初期化する。
 *
 * Demo Accountでログインしたあとにブラウザの「戻る」を使うと、
 * 見た目はLogin画面でもDemo Accountのログイン状態が残るため、
 * キャッシュから復元された場合だけページを再読み込みする。
 */
export function initLoginPage() {
    const loginPage = document.querySelector('[data-login-page]');

    // Login画面以外では何もしない。
    if (!loginPage) {
        return;
    }

    window.addEventListener('pageshow', (event) => {

        if (!event.persisted) {
            return;
        }

        // ページを再読み込みする。
        window.location.reload();
    });
}