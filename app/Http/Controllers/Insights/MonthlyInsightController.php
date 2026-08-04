<?php

namespace App\Http\Controllers\Insights;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MonthlyInsightController extends Controller
{
    public function show(string $month): View
    {
        return view('insights.index', [
            'activeView' => 'monthly',
            'month' => $month,
        ]);
    }
}