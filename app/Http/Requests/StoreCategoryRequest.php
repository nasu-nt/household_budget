<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    protected $errorBag = 'storeCategory';

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'color_code' => strtoupper(
                (string) $this->input('color_code')
            ),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('categories', 'name')
                    ->where(fn ($query) => $query
                        ->where('user_id', $this->user()->id)
                        ->whereNull('archived_at')
                    ),
            ],
            'color_code' => [
                'required',
                'string',
                'regex:/^#[0-9A-F]{6}$/',
            ],
            'intent' => [
                'nullable',
                Rule::in(['create_new']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => __(
                'A current category with this name already exists.'
            ),
            'color_code.regex' => __(
                'Select a valid category color.'
            ),
        ];
    }
}
