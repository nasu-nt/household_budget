import './bootstrap';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// import './pages/login';

import { initToast } from './components/toast';
import { initPasswordToggle } from './components/password-toggle';
import { initFormErrorClear } from './components/form-error-clear';
import { initDashboardSidebar } from './features/dashboard/sidebar';
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
