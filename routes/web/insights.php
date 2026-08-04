<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\Insights\DailyInsightController;
use App\Http\Controllers\Insights\DailyNoteController;
use App\Http\Controllers\Insights\MonthlyInsightController;
use Illuminate\Support\Facades\Route;

Route::prefix('insights')
    ->name('insights.')
    ->group(function () {
        Route::get('/', function () {
            return redirect()->route('insights.daily', [
                'date' => now()->format('Y-m-d'),
            ]);
        })->name('index');

        Route::get('/days/{date}', [DailyInsightController::class, 'show'])
            ->where('date', '\d{4}-\d{2}-\d{2}')
            ->name('daily');

        Route::put('/days/{date}/note', [DailyNoteController::class, 'update'])
            ->where('date', '\d{4}-\d{2}-\d{2}')
            ->name('daily-note.update');

        Route::post('/days/{date}/records',
            [ExpenseController::class, 'storeFromDailyInsights'],
            )->name('daily-record.store');

        Route::put('/days/{date}/records/{expense}',
            [ExpenseController::class, 'updateFromDailyInsights'],
            )->name('daily-record.update');

        // Monthly
        Route::get('/months/{month}', [MonthlyInsightController::class, 'show'])
            ->where('month', '\d{4}-\d{2}')
            ->name('monthly');
    });