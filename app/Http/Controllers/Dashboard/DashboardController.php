<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $categories = $request->user()
            ->categories()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('dashboard.index', [
            'categories' => $categories,
        ]);
    }
}