<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBudgetSettingRequest;
use App\Models\AppearanceSetting;
use App\Models\BudgetSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class BudgetSettingController extends Controller
{
    public function edit(Request $request): View
    {
        $userId = $request->user()->id;

        $budgetSetting = BudgetSetting::query()->firstOrCreate(
            ['user_id' => $userId],
            BudgetSetting::DEFAULT_VALUES,
        );

        $appearanceSetting = AppearanceSetting::query()->firstOrCreate(
            ['user_id' => $userId],
            AppearanceSetting::DEFAULT_COLORS,
        );

        return view('settings.budget.edit', [
            'budgetSetting' => $budgetSetting,
            'appearanceSetting' => $appearanceSetting,
        ]);
    }

    public function update(
        UpdateBudgetSettingRequest $request
    ): RedirectResponse {
        BudgetSetting::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->validated(),
        );

        return Redirect::route('settings.budget.edit')
            ->with(
                'success',
                __('Budget settings updated successfully.')
            );
    }
}
