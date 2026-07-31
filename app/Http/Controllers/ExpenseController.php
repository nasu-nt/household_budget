<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
   public function store(Request $request): RedirectResponse
    {
        $userId = $request->user()->id;

        $validated = $request->validate([
            'expenses' => [
                'required',
                'array',
                'min:1',
                'max:5',
            ],

            'expenses.*' => [
                'required',
                'array:expense_date,category_id,amount,memo',
            ],

            'expenses.*.expense_date' => [
                'required',
                'date_format:Y-m-d',
            ],

            'expenses.*.category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')
                    ->where(
                        fn ($query) => $query
                            ->where('user_id', $userId)
                            ->where('is_active', true)
                    ),
            ],

            'expenses.*.amount' => [
                'required',
                'integer',
                'min:1',
                'max:2147483647',
            ],

            'expenses.*.memo' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        DB::transaction(function () use ($validated, $userId): void {
            foreach ($validated['expenses'] as $expenseData) {
                $expense = new Expense();

                $expense->user_id = $userId;
                $expense->category_id = $expenseData['category_id'];
                $expense->amount = $expenseData['amount'];
                $expense->expense_date = $expenseData['expense_date'];
                $expense->memo = $expenseData['memo'] ?? null;

                $expense->save();
            }
        });

        $expenseCount = count($validated['expenses']);

        return redirect()
            ->route('dashboard')
            ->with(
                'success',
                $expenseCount === 1
                    ? __('Expense saved successfully.')
                    : __(':count expenses saved successfully.', [
                        'count' => $expenseCount,
                    ])
        );
    }
}