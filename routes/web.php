<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('auth')->group(function () {
    require __DIR__.'/web/dashboard.php';
    require __DIR__.'/web/expenses.php';
    require __DIR__.'/web/insights.php';
    require __DIR__.'/web/settings.php';
});

require __DIR__.'/auth.php';