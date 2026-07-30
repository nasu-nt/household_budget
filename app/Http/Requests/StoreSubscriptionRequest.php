<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubscriptionRequest extends FormRequest
{
    protected $errorBag = 'storeSubscription';

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'amount' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')
                    ->where(fn ($query) => $query
                        ->where('user_id', $this->user()->id)
                        ->where('is_active', true)
                        ->whereNull('archived_at')),
            ],
            'is_end_of_month' => ['required', 'boolean'],
            'billing_day' => [
                'nullable',
                'required_if:is_end_of_month,0',
                'integer',
                'between:1,31',
            ],
        ];
    }
}
