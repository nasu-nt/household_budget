<?php

use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::prefix('settings/categories')
    ->name('settings.categories.')
    ->controller(CategoryController::class)
    ->group(function () {
        Route::get('/', 'index')
            ->name('index');

        Route::post('/', 'store')
            ->name('store');

        Route::patch('/{category}/restore', 'restore')
            ->whereNumber('category')
            ->name('restore');

        Route::patch('/{category}', 'update')
            ->whereNumber('category')
            ->name('update');
    });