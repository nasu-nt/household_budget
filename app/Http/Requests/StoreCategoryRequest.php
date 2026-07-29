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
            'color_code' => strtoupper((string) $this->input('color_code')),
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
                    ->where(
                        fn ($query) => $query->where(
                            'user_id',
                            $this->user()->id
                        )
                    ),
            ],
            'color_code' => [
                'required',
                'string',
                'regex:/^#[0-9A-F]{6}$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => __(
                'A category with this name already exists.'
            ),
            'color_code.regex' => __(
                'Select a valid category color.'
            ),
        ];
    }
}
