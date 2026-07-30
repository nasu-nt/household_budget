<?php

namespace App\Http\Requests;

use App\Models\Subscription;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubscriptionRequest extends FormRequest
{
    protected $errorBag = 'updateSubscription';

    public function authorize(): bool
    {
        $subscription = $this->route('subscription');

        return $subscription instanceof Subscription
            && $this->user() !== null
            && $subscription->user_id === $this->user()->id;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'subscription_id' => [
                'required',
                'integer',
                Rule::in([$this->route('subscription')->id]),
            ],
            'intent' => [
                'required',
                Rule::in(['save', 'enable', 'disable', 'archive']),
            ],
            'name' => [
                'exclude_unless:intent,save',
                'required',
                'string',
                'max:100',
            ],
            'amount' => [
                'exclude_unless:intent,save',
                'required',
                'integer',
                'min:1',
                'max:2147483647',
            ],
            'category_id' => [
                'exclude_unless:intent,save',
                'required',
                'integer',
                Rule::exists('categories', 'id')
                    ->where(fn ($query) => $query
                        ->where('user_id', $this->user()->id)
                        ->whereNull('archived_at')),
            ],
            'is_end_of_month' => [
                'exclude_unless:intent,save',
                'required',
                'boolean',
            ],
            'billing_day' => [
                'exclude_unless:intent,save',
                'nullable',
                'required_if:is_end_of_month,0',
                'integer',
                'between:1,31',
            ],
        ];
    }
}
