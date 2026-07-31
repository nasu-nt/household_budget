<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBudgetSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'monthly_budget' => [
                'bail',
                'required',
                'integer',
                'min:1',
                'max:2147483647',
            ],
            'monthly_limit' => [
                'bail',
                'required',
                'integer',
                'gte:monthly_budget',
                'max:2147483647',
            ],
            'is_end_of_month' => [
                'required',
                'boolean',
            ],
            'closing_day' => [
                'nullable',
                Rule::requiredIf(
                    fn (): bool => ! $this->boolean('is_end_of_month')
                ),
                'integer',
                'between:1,31',
            ],
        ];
    }

    /**
     * Normalize the closing-day fields before validation.
     */
    protected function prepareForValidation(): void
    {
        $isEndOfMonth = $this->boolean('is_end_of_month');

        $this->merge([
            'is_end_of_month' => $isEndOfMonth,
            'closing_day' => $isEndOfMonth
                ? null
                : $this->input('closing_day'),
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'monthly_budget.min' => __(
                'Monthly budget must be at least ¥1.'
            ),
            'monthly_limit.gte' => __(
                'Spending limit must be greater than or equal to the monthly budget.'
            ),
            'closing_day.required' => __(
                'Select a closing day.'
            ),
            'closing_day.between' => __(
                'Closing day must be between 1 and 31.'
            ),
        ];
    }
}
