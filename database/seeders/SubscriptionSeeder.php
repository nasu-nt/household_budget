<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class SubscriptionSeeder extends Seeder
{
    /**
     * Seed sample subscriptions for the demo account.
     */
    public function run(): void
    {
        $demoUser = User::query()
            ->where(
                'email',
                config('demo.email', 'demo@example.com')
            )
            ->first();

        if ($demoUser === null) {
            throw new RuntimeException(
                'Create the demo user before running SubscriptionSeeder.'
            );
        }

        $categories = Category::query()
            ->where('user_id', $demoUser->id)
            ->whereIn('name', ['Utilities', 'Entertainment'])
            ->get()
            ->keyBy('name');

        if (! $categories->has('Utilities')
            || ! $categories->has('Entertainment')) {
            throw new RuntimeException(
                'Run CategorySeeder before SubscriptionSeeder.'
            );
        }

        $subscriptions = [
            [
                'name' => 'Netflix',
                'category_id' => $categories['Entertainment']->id,
                'amount' => 890,
                'is_end_of_month' => false,
                'billing_day' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Adobe CC',
                'category_id' => $categories['Utilities']->id,
                'amount' => 6_480,
                'is_end_of_month' => true,
                'billing_day' => null,
                'is_active' => false,
            ],
            [
                'name' => 'Apple Music',
                'category_id' => $categories['Utilities']->id,
                'amount' => 1_080,
                'is_end_of_month' => false,
                'billing_day' => 27,
                'is_active' => true,
            ],
            [
                'name' => 'Amazon Prime',
                'category_id' => $categories['Utilities']->id,
                'amount' => 600,
                'is_end_of_month' => true,
                'billing_day' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Google AI Pro',
                'category_id' => $categories['Utilities']->id,
                'amount' => 2_900,
                'is_end_of_month' => false,
                'billing_day' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($subscriptions as $subscription) {
            Subscription::query()->updateOrCreate(
                [
                    'user_id' => $demoUser->id,
                    'name' => $subscription['name'],
                ],
                [
                    ...$subscription,
                    'archived_at' => null,
                ]
            );
        }
    }
}
