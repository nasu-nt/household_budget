<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Models\Category;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $categories = Category::query()
            ->where('user_id', $userId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $subscriptions = Subscription::query()
            ->with('category')
            ->where('user_id', $userId)
            ->whereNull('archived_at')
            ->orderBy('name')
            ->orderBy('id')
            ->get();

        return view('settings.subscriptions.index', [
            'categories' => $categories,
            'subscriptions' => $subscriptions,
        ]);
    }

    public function store(
        StoreSubscriptionRequest $request
    ): RedirectResponse {
        $validated = $request->validated();

        Subscription::query()->create([
            'user_id' => $request->user()->id,
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'amount' => $validated['amount'],
            'is_end_of_month' => (bool) $validated['is_end_of_month'],
            'billing_day' => (bool) $validated['is_end_of_month']
                ? null
                : $validated['billing_day'],
            'is_active' => true,
        ]);

        return redirect()
            ->route('settings.subscriptions.index')
            ->with('success', __('Subscription added successfully.'));
    }

    public function update(
        UpdateSubscriptionRequest $request,
        Subscription $subscription
    ): RedirectResponse {
        $validated = $request->validated();

        match ($validated['intent']) {
            'save' => $subscription->update([
                'category_id' => $validated['category_id'],
                'name' => $validated['name'],
                'amount' => $validated['amount'],
                'is_end_of_month' => (bool) $validated['is_end_of_month'],
                'billing_day' => (bool) $validated['is_end_of_month']
                    ? null
                    : $validated['billing_day'],
            ]),
            'enable' => $subscription->update(['is_active' => true]),
            'disable' => $subscription->update(['is_active' => false]),
            'archive' => $subscription->update([
                'is_active' => false,
                'archived_at' => now(),
            ]),
        };

        $message = match ($validated['intent']) {
            'save' => __('Subscription updated successfully.'),
            'enable' => __('Subscription enabled successfully.'),
            'disable' => __('Subscription disabled successfully.'),
            'archive' => __('Subscription archived successfully.'),
        };

        return redirect()
            ->route('settings.subscriptions.index')
            ->with('success', $message);
    }
}
