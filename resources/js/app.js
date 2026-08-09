import './bootstrap';

import Alpine from 'alpinejs';

// Components
import { initAppearanceStatus } from './components/appearance-status';
import { initColorPickers } from './components/color-picker';
import { initFormErrorClear } from './components/form-error-clear';
import { initMoneyInputs } from './components/money-input';
import { initPasswordToggle } from './components/password-toggle';
import { initToast } from './components/toast';

// Pages
import { initLoginPage } from './pages/auth/login';
import { initDashboardCalendar } from './pages/dashboard/calendar';
import { initDashboardSidebar } from './pages/dashboard/sidebar';
import { initDailyInsightsRecords } from './pages/insights/records';
import { budgetSettingsForm } from './pages/settings/budget';

/*
 * Alpine.jsの初期設定
 */
window.Alpine = Alpine;

Alpine.data(
    'budgetSettingsForm',
    budgetSettingsForm,
);

Alpine.start();

/*
 * HTMLの読み込みが完了してから、
 * 各画面のJavaScript機能を初期化する。
 */
document.addEventListener('DOMContentLoaded', () => {
    // Components
    initAppearanceStatus();
    initColorPickers();
    initFormErrorClear();
    initMoneyInputs();
    initPasswordToggle();
    initToast();

    // Pages
    initLoginPage();
    initDashboardCalendar();
    initDashboardSidebar();
    initDailyInsightsRecords();

    /*
     * Monthly Insightsを開いている場合だけ、
     * Chart.jsを含むグラフ用ファイルを読み込む。
     *
     * グラフ側でエラーが発生しても、
     * Dashboardのカレンダーなどは巻き込まない。
     */
    const monthlySpendingTrend =
        document.querySelector(
            '[data-monthly-spending-trend]',
        );

    if (!monthlySpendingTrend) {
        return;
    }

    import('./pages/insights/chart.js')
        .then((module) => {
            const initMonthlySpendingChart =
                module.default;

            if (
                typeof initMonthlySpendingChart
                !== 'function'
            ) {
                throw new TypeError(
                    'Monthly spending chart initializer was not found.',
                );
            }

            initMonthlySpendingChart();
        })
        .catch((error) => {
            console.error(
                'Failed to load monthly spending chart.',
                error,
            );
        });
});