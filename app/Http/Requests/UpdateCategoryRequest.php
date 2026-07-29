<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    protected $errorBag = 'updateCategory';

    public function authorize(): bool
    {
        $category = $this->route('category');

        return $category instanceof Category
            && $this->user() !== null
            && (int) $category->user_id === (int) $this->user()->id;
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
        /** @var Category $category */
        $category = $this->route('category');

        return [
            'category_id' => [
                'required',
                'integer',
                Rule::in([$category->id]),
            ],
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('categories', 'name')
                    ->where(fn ($query) => $query->where(
                        'user_id',
                        $this->user()->id
                    ))
                    ->ignore($category->id),
            ],
            'color_code' => [
                'required',
                'string',
                'regex:/^#[0-9A-F]{6}$/',
            ],
            'intent' => [
                'required',
                Rule::in(['save', 'enable', 'disable']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => __('A category with this name already exists.'),
            'color_code.regex' => __('Select a valid category color.'),
        ];
    }
}
