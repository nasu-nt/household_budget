<?php

use App\Http\Controllers\AppearanceSettingController;
use Illuminate\Support\Facades\Route;

Route::get(
    '/settings/appearance',
    [AppearanceSettingController::class, 'index']
)->name('settings.appearance.index');

Route::patch(
    '/settings/appearance',
    [AppearanceSettingController::class, 'update']
)->name('settings.appearance.update');