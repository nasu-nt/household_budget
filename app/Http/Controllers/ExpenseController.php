<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
                'array:expense_date,recorded_time,category_id,amount,memo',
            ],

            'expenses.*.expense_date' => [
                'required',
                'date_format:Y-m-d',
            ],

            'expenses.*.recorded_time' => [
                'nullable',
                'date_format:H:i',
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
                $expense->recorded_time = $expenseData['recorded_time'] ?? null;
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

    /*
     * Daily InsightsでのRecord更新用
     */
    public function updateFromDailyInsights(
        Request $request,
        string $date,
        int $expense
    ): RedirectResponse {
        /*
        * URLの日付が正しい形式か確認する。
        */
        $dateValidator = Validator::make(
            [
                'date' => $date,
            ],
            [
                'date' => [
                    'required',
                    'date_format:Y-m-d',
                ],
            ],
        );

        if ($dateValidator->fails()) {
            abort(404);
        }

        $userId = $request->user()->id;

        /*
        * 編集フォームの入力内容を確認する。
        */
        $validated = $request->validate([
            'recorded_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')
                    ->where(
                        fn ($query) => $query
                            ->where('user_id', $userId)
                            ->where('is_active', true)
                    ),
            ],

            'amount' => [
                'required',
                'integer',
                'min:1',
                'max:2147483647',
            ],

            'memo' => [
                'nullable',
                'string',
                'max:255',
            ],

            'sort' => [
                'nullable',
                Rule::in([
                    'recorded_time',
                    'category',
                    'amount',
                ]),
            ],

            'direction' => [
                'nullable',
                Rule::in([
                    'asc',
                    'desc',
                ]),
            ],
        ]);

        /*
        * ログインユーザー本人かつ、
        * 表示中の日付に属する支出だけ取得する。
        */
        $expenseModel = Expense::query()
            ->where('id', $expense)
            ->where('user_id', $userId)
            ->where('expense_date', $date)
            ->firstOrFail();

        $expenseModel->recorded_time =
            $validated['recorded_time'] ?? null;

        $expenseModel->category_id =
            $validated['category_id'];

        $expenseModel->amount =
            $validated['amount'];

        $expenseModel->memo =
            $validated['memo'] ?? null;

        $expenseModel->save();

        /*
        * 編集前の並び順を維持してDaily Insightsへ戻る。
        */
        return redirect()
            ->route('insights.daily', [
                'date' => $date,
                'sort' =>
                    $validated['sort']
                    ?? 'recorded_time',
                'direction' =>
                    $validated['direction']
                    ?? 'desc',
            ])
            ->with(
                'success',
                __('Expense updated successfully.'),
            );
    }
    public function storeFromDailyInsights(
        Request $request,
        string $date
    ): RedirectResponse {
        /*
        * URLの日付がYYYY-MM-DD形式か確認する。
        */
        $dateValidator = Validator::make(
            [
                'date' => $date,
            ],
            [
                'date' => [
                    'required',
                    'date_format:Y-m-d',
                ],
            ],
        );

        if ($dateValidator->fails()) {
            abort(404);
        }

        $userId = $request->user()->id;

        /*
        * 新規レコードの入力値を確認する。
        */
        $validated = $request->validate([
            'recorded_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')
                    ->where(
                        fn ($query) => $query
                            ->where('user_id', $userId)
                            ->where('is_active', true)
                    ),
            ],

            'amount' => [
                'required',
                'integer',
                'min:1',
                'max:2147483647',
            ],

            'memo' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
            * 保存後もRecordsの並び順を維持する。
            */
            'sort' => [
                'nullable',
                Rule::in([
                    'recorded_time',
                    'category',
                    'amount',
                ]),
            ],

            'direction' => [
                'nullable',
                Rule::in([
                    'asc',
                    'desc',
                ]),
            ],
        ]);

        $expense = new Expense();
        $expense->user_id = $userId;
        $expense->expense_date = $date;
        $expense->recorded_time =
            $validated['recorded_time'] ?? null;
        $expense->category_id =
            $validated['category_id'];
        $expense->amount =
            $validated['amount'];
        $expense->memo =
            $validated['memo'] ?? null;

        $expense->save();

        return redirect()
            ->route('insights.daily', [
                'date' => $date,
                'sort' =>
                    $validated['sort']
                    ?? 'recorded_time',
                'direction' =>
                    $validated['direction']
                    ?? 'desc',
            ])
            ->with(
                'success',
                __('Expense saved successfully.'),
            );
    }
}