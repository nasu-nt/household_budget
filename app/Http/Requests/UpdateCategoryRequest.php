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
            && (int) $category->user_id === (int) $this->user()->id
            && $category->archived_at === null;
    }

    protected function prepareForValidation(): void
    {
        $input = [];

        if ($this->has('name')) {
            $input['name'] = trim((string) $this->input('name'));
        }

        if ($this->has('color_code')) {
            $input['color_code'] = strtoupper(
                (string) $this->input('color_code')
            );
        }

        $this->merge($input);
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
            'intent' => [
                'required',
                Rule::in(['save', 'enable', 'disable', 'archive']),
            ],
            'name' => [
                'exclude_unless:intent,save',
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
                'exclude_unless:intent,save',
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
