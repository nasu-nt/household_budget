<?php

namespace App\Http\Requests\Insights;

use Illuminate\Foundation\Http\FormRequest;

class SaveDailyNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'note' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }
}