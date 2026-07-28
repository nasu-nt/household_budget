<?php

use App\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\Route;

Route::post('/expenses', [ExpenseController::class, 'store'])
    ->name('expenses.store');

Route::patch('/expenses/{expense}', [ExpenseController::class, 'update'])
    ->name('expenses.update');

Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])
    ->name('expenses.destroy');