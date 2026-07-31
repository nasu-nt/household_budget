import './bootstrap';

import Alpine from 'alpinejs';

import { initToast } from './components/toast';
import { initPasswordToggle } from './components/password-toggle';
import { initFormErrorClear} from './components/form-error-clear';
import { initDashboardSidebar} from './pages/dashboard/sidebar';
import { initDashboardCalendar} from './pages/dashboard/calendar';
import { initColorPickers} from './components/color-picker';
import { initAppearanceSettings} from './components/appearance-settings';
import { initMoneyInputs} from './components/money-input';
import { budgetSettingsForm} from './pages/settings/budget';

window.Alpine = Alpine;

Alpine.data(
    'budgetSettingsForm',
    budgetSettingsForm,
);

Alpine.start();

// import './pages/login';

document.addEventListener('DOMContentLoaded', () => {
    initToast();
    initPasswordToggle();
    initFormErrorClear();
    initDashboardSidebar();
    initDashboardCalendar();
    initColorPickers();
    initAppearanceSettings();
    initMoneyInputs();
<<<<<<< Updated upstream
});

=======
});
>>>>>>> Stashed changes
