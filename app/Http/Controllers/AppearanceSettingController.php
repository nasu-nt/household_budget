<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAppearanceSettingRequest;
use App\Models\AppearanceSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppearanceSettingController extends Controller
{
    public function index(Request $request): View
    {
        $appearanceSetting = AppearanceSetting::query()->firstOrCreate(
            ['user_id' => $request->user()->id],
            AppearanceSetting::DEFAULT_COLORS,
        );

        return view('settings.appearance.index', [
            'appearanceSetting' => $appearanceSetting,
        ]);
    }

    public function update(
        UpdateAppearanceSettingRequest $request,
    ): RedirectResponse {
        AppearanceSetting::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->validated(),
        );

        return to_route('settings.appearance.index')
            ->with(
                'success',
                __('Appearance settings updated.'),
            );
    }
}