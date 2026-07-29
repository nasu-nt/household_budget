<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')
                    ->where(fn ($query) => $query->where(
                        'user_id',
                        $request->user()->id
                    )),
            ],
            'amount' => [
                'required',
                'integer',
                'min:1',
                'max:4294967295',
            ],
            'expense_date' => [
                'required',
                'date',
            ],
            'memo' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $request->user()
            ->expenses()
            ->create($validated);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Expense saved successfully.');
    }
}