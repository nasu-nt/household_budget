<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BudgetSettingSeeder extends Seeder
{
    /**
     * Seed the budget settings for existing users.
     */
    public function run(): void
    {
        $now = now();

        $budgetSettings = User::query()
            ->pluck('id')
            ->map(fn ($userId): array => [
                'user_id' => $userId,
                'monthly_budget' => 128_400,
                'monthly_limit' => 150_000,
                'is_end_of_month' => false,
                'closing_day' => 27,
                'created_at' => $now,
                'updated_at' => $now,
            ])
            ->all();

        if ($budgetSettings === []) {
            return;
        }

        DB::table('budget_settings')->upsert(
            $budgetSettings,
            ['user_id'],
            [
                'monthly_budget',
                'monthly_limit',
                'is_end_of_month',
                'closing_day',
                'updated_at',
            ]
        );
    }
}