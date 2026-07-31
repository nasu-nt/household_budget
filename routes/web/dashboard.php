<?php

use App\Http\Controllers\Dashboard\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get(
    '/dashboard/calendar',
    [DashboardController::class, 'calendar'],
)->name('dashboard.calendar');

Route::get(
    '/dashboard',
    [DashboardController::class, 'index'],
)->name('dashboard');
