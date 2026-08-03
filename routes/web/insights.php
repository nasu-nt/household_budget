<?php

use App\Http\Controllers\Insights\DailyInsightController;
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

        Route::get('/months/{month}', [MonthlyInsightController::class, 'show'])
            ->where('month', '\d{4}-\d{2}')
            ->name('monthly');

        Route::get('/days/{date}', [DailyInsightController::class, 'show'])
            ->where('date', '\d{4}-\d{2}-\d{2}')
            ->name('daily');
    });