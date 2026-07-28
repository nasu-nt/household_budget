<?php

    use App\Http\Controllers\ProfileController;
    use Illuminate\Support\Facades\Route;

    Route::prefix('settings')
        ->name('settings.')
        ->group(function () {
            Route::get('/', function () {
                return redirect()->route('settings.profile.edit');
            })->name('index');

            Route::get('/profile', [ProfileController::class, 'edit'])
                ->name('profile.edit');

            Route::patch('/profile', [ProfileController::class, 'update'])
                ->name('profile.update');

            Route::delete('/profile', [ProfileController::class, 'destroy'])
                ->name('profile.destroy');

            // 以下はSettings画面作成中の仮ルート
            Route::view('/categories', 'settings.categories.index')
                ->name('categories.index');

            Route::view('/budget', 'settings.budget.edit')
                ->name('budget.edit');

            Route::view('/appearance', 'settings.appearance.edit')
                ->name('appearance.edit');

            Route::view('/subscriptions', 'settings.subscriptions.index')
                ->name('subscriptions.index');
        });