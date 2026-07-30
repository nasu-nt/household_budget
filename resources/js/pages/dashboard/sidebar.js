export function initDashboardSidebar() {
    const sidebar = document.getElementById('sidebar');
    const btn = document.getElementById('sidebarToggle');
    if (!sidebar || !btn) return;

    // a11y: 初期値と切り替えを管理
    const setA11y = (isClosed) => {
        btn.setAttribute('aria-expanded', String(!isClosed));
        btn.setAttribute('aria-label', isClosed ? 'Open add expense form' : 'Close add expense form');
    };

    // 初期状態反映
    setA11y(sidebar.classList.contains('is-closed'));

    btn.addEventListener('click', () => {
        const isClosed = sidebar.classList.toggle('is-closed');
        btn.classList.toggle('is-closed');
        // 開閉に合わせて更新
        setA11y(isClosed);
    });
}