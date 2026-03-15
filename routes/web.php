<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');
require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard/index')->name('dashboard');
});

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });
// 
// Route::prefix('settings')->middleware('auth')->name('settings.')->group(function () {
//     Route::get('/account', [SettingsAccountController::class, 'edit'])->name('account.edit');
//     Route::patch('/account', [SettingsAccountController::class, 'update'])->name('account.update');
// 
//     Route::get('/budget', [SettingsBudgetController::class, 'edit'])->name('budget.edit');
//     Route::patch('/budget', [SettingsBudgetController::class, 'update'])->name('budget.update');
// 
//     Route::get('/display', [SettingsDisplayController::class, 'edit'])->name('display.edit');
//     Route::patch('/display', [SettingsDisplayController::class, 'update'])->name('display.update');
// 
//     // さぶすく
// });