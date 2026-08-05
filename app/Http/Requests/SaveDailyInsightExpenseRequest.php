<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveDailyInsightExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'recorded_time' => ['nullable', 'date_format:H:i'],
            'category_id' => ['required', 'integer',
                Rule::exists('categories', 'id')
                    ->where(
                        fn ($query) => $query
                            ->where('user_id', $userId)
                            ->where('is_active', true)
                    ),
            ],
            'amount' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'memo' => ['nullable','string', 'max:255'],
            'sort' => ['nullable',
                Rule::in([
                    'recorded_time',
                    'category',
                    'amount',
                ]),
            ],
            'direction' => ['nullable',
                Rule::in([
                    'asc',
                    'desc',
                ]),
            ],

            /*
             * バリデーションエラー後に、
             * 対象の入力行を再表示するための値。
             */
            'creating_record' => ['nullable',
                Rule::in(['1']),
            ],

            'editing_expense_id' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'recorded_time.date_format' =>
                __('Please enter the time in HH:MM format.'),

            'category_id.required' =>
                __('Please select a category.'),

            'category_id.integer' =>
                __('Please select a valid category.'),

            'category_id.exists' =>
                __('Please select an available category.'),

            'amount.required' =>
                __('Please enter an amount.'),

            'amount.integer' =>
                __('Please enter the amount using whole numbers.'),

            'amount.min' =>
                __('Please enter an amount of at least ¥1.'),

            'amount.max' =>
                __('The amount is too large.'),

            'memo.string' =>
                __('Please enter the memo as text.'),

            'memo.max' =>
                __('The memo must not exceed 255 characters.'),

            'sort.in' =>
                __('The selected sorting option is invalid.'),

            'direction.in' =>
                __('The selected sorting direction is invalid.'),
        ];
    }
}