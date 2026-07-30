<?php

use App\Http\Controllers\AppearanceSettingController;
use App\Http\Controllers\BudgetSettingController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')
    ->prefix('settings')
    ->name('settings.')
    ->group(function () {
        // Account
        Route::get('/', function () {
            return redirect()->route('settings.profile.index');
        })->name('index');

        // Category
        Route::get(
            '/categories',
            [CategoryController::class, 'index']
        )->name('categories.index');

        Route::post(
            '/categories',
            [CategoryController::class, 'store']
        )->name('categories.store');

        Route::patch(
            '/categories/{category}/restore',
            [CategoryController::class, 'restore']
        )
            ->whereNumber('category')
            ->name('categories.restore');

        Route::patch(
            '/categories/{category}',
            [CategoryController::class, 'update']
        )
            ->whereNumber('category')
            ->name('categories.update');

        // Budget
        Route::get(
            '/budget',
            [BudgetSettingController::class, 'edit']
        )->name('budget.edit');

        Route::patch(
            '/budget',
            [BudgetSettingController::class, 'update']
        )->name('budget.update');

        // Appearance
        Route::get(
            '/appearance',
            [AppearanceSettingController::class, 'index']
        )->name('appearance.index');

        Route::patch(
            '/appearance',
            [AppearanceSettingController::class, 'update']
        )->name('appearance.update');
    });