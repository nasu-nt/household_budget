<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        // ユーザーがいない（一応の防御）
        if (!$user) {
            return false;
        }

        // 通常ユーザー
        if (!$user->isDemoAccount()) {
            return true;
        }

        // Demoユーザー
        return $this->input('email') === $user->email;
    }

    /**
     * デモユーザーでメールアドレスを変更しようとした場合
     */
    protected function failedAuthorization(): void
    {
        throw new AuthorizationException(
            __('profile.demo_email_change_disabled')
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation.required'),
            'name.string' => __('validation.string'),
            'name.max' => __('validation.max'),

            'email.required' => __('validation.required'),
            'email.string' => __('validation.string'),
            'email.lowercase' => __('validation.lowercase'),
            'email.email' => __('validation.email'),
            'email.max' => __('validation.max'),
            'email.unique' => __('validation.unique'),
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'display name',
            'email' => 'email address',
        ];
    }
}
