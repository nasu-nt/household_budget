import './bootstrap';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// import './pages/login';

import { initToast } from './components/toast';
import { initPasswordToggle } from './components/password-toggle';
import { initFormErrorClear } from './components/form-error-clear';

document.addEventListener('DOMContentLoaded', () => {
    initToast();
    initPasswordToggle();
    initFormErrorClear();
});