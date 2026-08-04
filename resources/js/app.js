import './bootstrap';

import Alpine from 'alpinejs';

import { initToast } from './components/toast';
import { initMoneyInputs } from './components/money-input';
import { initPasswordToggle } from './components/password-toggle';
import { initFormErrorClear } from './components/form-error-clear';
import { initColorPickers } from './components/color-picker';
import { initAppearanceSettings } from './components/appearance-settings';
import { initDashboardSidebar } from './pages/dashboard/sidebar';
import { initDashboardCalendar } from './pages/dashboard/calendar';
import { initDailyInsightsRecords } from './pages/insights/records';
import { budgetSettingsForm } from './pages/settings/budget';

import './pages/insights/chart';

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
 * 通常のJavaScript機能を初期化する。
 *
 * DOMContentLoaded後に実行することで、
 * Blade内のHTML要素が読み込まれてから
 * querySelectorなどの処理を行える。
 */
document.addEventListener('DOMContentLoaded', () => {
    initToast();
    initMoneyInputs();
    initPasswordToggle();
    initFormErrorClear();
    initColorPickers();
    initAppearanceSettings();
    initDashboardSidebar();
    initDashboardCalendar();
    initDailyInsightsRecords();
});