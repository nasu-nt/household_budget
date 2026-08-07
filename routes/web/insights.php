<?php

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\Insights\DailyInsightController;
use App\Http\Controllers\Insights\DailyNoteController;
use App\Http\Controllers\Insights\MonthlyInsightController;
use App\Http\Controllers\Insights\MonthlyNoteController;
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
        
        Route::delete(
            'days/{date}/records/{expense}',
            [ExpenseController::class, 'destroyFromDailyInsights']
        )->name('daily-record.destroy');

        // Monthly
        // {month}は予算期間の終了月とする
        // 【例】URL: /insights/months/2026-06
        // ・月末締め 2026/06/01 ～ 2026/06/30
        // ・27日締め 2026/05/28 ～ 2026/06/27
        Route::get('/months', [MonthlyInsightController::class, 'current'])
            ->name('monthly.current');

        Route::get('/months/{month}', [MonthlyInsightController::class, 'show'])
            ->where('month', '\d{4}-\d{2}')
            ->name('monthly');

        Route::put('/months/{month}/note', [ MonthlyNoteController::class, 'update'])
        ->where('month', '\d{4}-\d{2}')
        ->name('monthly-note.update');
        
    });