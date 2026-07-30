import './bootstrap';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.data('budgetSettingsForm', budgetSettingsForm);
Alpine.start();

import { initToast } from './components/toast';
import { initPasswordToggle } from './components/password-toggle';
import { initFormErrorClear } from './components/form-error-clear';
import { initDashboardSidebar } from './pages/dashboard/sidebar';
import { initColorPickers } from './components/color-picker';
import { initAppearanceSettings } from './components/appearance-settings';
import { initMoneyInputs } from './components/money-input';

document.addEventListener('DOMContentLoaded', () => {
    initToast();
    initPasswordToggle();
    initFormErrorClear();
    initDashboardSidebar();
    initColorPickers();
    initAppearanceSettings();
    initMoneyInputs();
});
