<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppearanceSettingRequest extends FormRequest
{
    /**
     * @var list<string>
     */
    private const COLOR_FIELDS = [
        'all_good_color',
        'slightly_high_color',
        'over_budget_color',
        'over_limit_color',
    ];

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        $rules = [];

        foreach (self::COLOR_FIELDS as $field) {
            $rules[$field] = [
                'bail',
                'required',
                'string',
                'regex:/^#[0-9A-F]{6}$/',
            ];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $messages = [];

        foreach (self::COLOR_FIELDS as $field) {
            $messages["{$field}.required"] =
                'Please enter a color code.';
            $messages["{$field}.regex"] =
                'Please enter a valid color code, such as #F8FAFC.';
        }

        return $messages;
    }

    protected function prepareForValidation(): void
    {
        $colors = [];

        foreach (self::COLOR_FIELDS as $field) {
            $value = $this->input($field);

            if (is_string($value)) {
                $colors[$field] = strtoupper(trim($value));
            }
        }

        $this->merge($colors);
    }
}
