<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * デモアカウントと、2026-05-28〜2026-06-27のモックデータを登録する。
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $now = now();

            $demoUser = User::query()->updateOrCreate(
                [
                    'email' => config('demo.email', 'demo@example.com'),
                ],
                [
                    'name' => config('demo.name', 'Demo User'),
                    'password' => Hash::make(
                        config('demo.password', 'demo-password'),
                    ),
                    'email_verified_at' => $now,
                ],
            );

            /*
             * Seederを再実行したときも同じ結果になるように、
             * デモユーザーに紐づくデータだけを依存関係の順に削除する。
             */
            DB::table('expenses')
                ->where('user_id', $demoUser->id)
                ->delete();

            DB::table('subscriptions')
                ->where('user_id', $demoUser->id)
                ->delete();

            DB::table('category_budgets')
                ->where('user_id', $demoUser->id)
                ->delete();

            DB::table('daily_notes')
                ->where('user_id', $demoUser->id)
                ->delete();

            DB::table('monthly_notes')
                ->where('user_id', $demoUser->id)
                ->delete();

            DB::table('appearance_settings')
                ->where('user_id', $demoUser->id)
                ->delete();

            DB::table('budget_settings')
                ->where('user_id', $demoUser->id)
                ->delete();

            DB::table('categories')
                ->where('user_id', $demoUser->id)
                ->delete();

            $categoryIds = $this->createCategories(
                userId: $demoUser->id,
                timestamp: $now,
            );

            DB::table('budget_settings')->insert([
                'user_id' => $demoUser->id,
                'monthly_budget' => 128400,
                'monthly_limit' => 150000,
                'is_end_of_month' => false,
                'closing_day' => 27,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('appearance_settings')->insert([
                'user_id' => $demoUser->id,
                'all_good_color' => '#F8FAFC',
                'slightly_high_color' => '#F7E7A6',
                'over_budget_color' => '#F3C38C',
                'over_limit_color' => '#EE8B8B',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $this->createSubscriptions(
                userId: $demoUser->id,
                categoryIds: $categoryIds,
                timestamp: $now,
            );

            $this->createExpensesAndDailyNotes(
                userId: $demoUser->id,
                categoryIds: $categoryIds,
                timestamp: $now,
            );

            /*
             * category_budgets と monthly_notes は、
             * 添付されたExcelに登録値がないため、架空の値は作らず空のままにする。
             */
        });
    }

    /**
     * @return array<string, int>
     */
    private function createCategories(
        int $userId,
        mixed $timestamp,
    ): array {
        $categories = [
            [
                'name' => 'Utilities',
                'sort_order' => 10,
                'color_code' => '#64748B',
            ],
            [
                'name' => 'Food',
                'sort_order' => 20,
                'color_code' => '#F59E0B',
            ],
            [
                'name' => 'Entertainment',
                'sort_order' => 30,
                'color_code' => '#8B5CF6',
            ],
            [
                'name' => 'Transport',
                'sort_order' => 40,
                'color_code' => '#3B82F6',
            ],
            [
                'name' => 'Rent',
                'sort_order' => 50,
                'color_code' => '#10B981',
            ],
        ];

        $categoryIds = [];

        foreach ($categories as $category) {
            $categoryIds[$category['name']] = DB::table('categories')
                ->insertGetId([
                    'user_id' => $userId,
                    'name' => $category['name'],
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                    'color_code' => $category['color_code'],
                    'archived_at' => null,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
        }

        return $categoryIds;
    }

    /**
     * @param array<string, int> $categoryIds
     */
    private function createSubscriptions(
        int $userId,
        array $categoryIds,
        mixed $timestamp,
    ): void {
        DB::table('subscriptions')->insert([
            [
                'user_id' => $userId,
                'category_id' => $categoryIds['Entertainment'],
                'name' => 'Netflix',
                'amount' => 890,
                'billing_day' => 15,
                'is_end_of_month' => false,
                'is_active' => true,
                'archived_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'user_id' => $userId,
                'category_id' => $categoryIds['Utilities'],
                'name' => 'Apple Music',
                'amount' => 1080,
                'billing_day' => 27,
                'is_end_of_month' => false,
                'is_active' => true,
                'archived_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'user_id' => $userId,
                'category_id' => $categoryIds['Entertainment'],
                'name' => 'Amazon Prime',
                'amount' => 600,
                'billing_day' => null,
                'is_end_of_month' => true,
                'is_active' => true,
                'archived_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
            [
                'user_id' => $userId,
                'category_id' => $categoryIds['Utilities'],
                'name' => 'Google AI Pro',
                'amount' => 2900,
                'billing_day' => 5,
                'is_end_of_month' => false,
                'is_active' => true,
                'archived_at' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ],
        ]);
    }

    /**
     * @param array<string, int> $categoryIds
     */
    private function createExpensesAndDailyNotes(
        int $userId,
        array $categoryIds,
        mixed $timestamp,
    ): void {
        $dailyData = [
            '2026-05-28' => [
                'expenses' => ['Food' => 1800, 'Transport' => 620],
                'note' => 'Food + Transport',
            ],
            '2026-05-29' => [
                'expenses' => ['Food' => 2650, 'Entertainment' => 1500],
                'note' => 'Entertainment spending',
            ],
            '2026-05-30' => [
                'expenses' => ['Food' => 1200, 'Transport' => 480],
            ],
            '2026-05-31' => [
                'expenses' => ['Food' => 1350, 'Entertainment' => 600],
                'note' => 'Amazon Prime (month end)',
            ],
            '2026-06-01' => [
                'expenses' => ['Food' => 900, 'Rent' => 42000],
                'note' => 'Rent',
            ],
            '2026-06-02' => [
                'expenses' => ['Food' => 1450, 'Transport' => 520],
            ],
            '2026-06-03' => [
                'expenses' => ['Food' => 1100, 'Entertainment' => 1200],
            ],
            '2026-06-04' => [
                'expenses' => ['Food' => 1750, 'Transport' => 760],
            ],
            '2026-06-05' => [
                'expenses' => ['Food' => 980, 'Entertainment' => 2900],
                'note' => 'Google AI Pro',
            ],
            '2026-06-06' => [
                'expenses' => ['Food' => 2100, 'Transport' => 600],
            ],
            '2026-06-07' => [
                'expenses' => ['Utilities' => 6800, 'Food' => 1250],
                'note' => 'Electricity / gas',
            ],
            '2026-06-08' => [
                'expenses' => ['Food' => 1550, 'Transport' => 480],
            ],
            '2026-06-09' => [
                'expenses' => ['Food' => 1300, 'Entertainment' => 980],
            ],
            '2026-06-10' => [
                'expenses' => ['Food' => 1800, 'Transport' => 720],
            ],
            '2026-06-11' => [
                'expenses' => ['Food' => 950, 'Transport' => 420],
            ],
            '2026-06-12' => [
                'expenses' => ['Food' => 2400, 'Entertainment' => 1800],
            ],
            '2026-06-13' => [
                'expenses' => ['Food' => 1200, 'Transport' => 550],
            ],
            '2026-06-14' => [
                'expenses' => ['Food' => 1650, 'Transport' => 680],
            ],
            '2026-06-15' => [
                'expenses' => ['Food' => 1350, 'Entertainment' => 890],
                'note' => 'Netflix',
            ],
            '2026-06-16' => [
                'expenses' => ['Food' => 1900, 'Transport' => 500],
            ],
            '2026-06-17' => [
                'expenses' => ['Food' => 980, 'Entertainment' => 1500],
            ],
            '2026-06-18' => [
                'expenses' => ['Food' => 1500, 'Transport' => 620],
            ],
            '2026-06-19' => [
                'expenses' => ['Food' => 2900, 'Entertainment' => 1100, 'Transport' => 200],
            ],
            '2026-06-20' => [
                'expenses' => ['Utilities' => 4400, 'Food' => 1200],
                'note' => 'Mobile / internet',
            ],
            '2026-06-21' => [
                'expenses' => ['Food' => 1400, 'Transport' => 560],
            ],
            '2026-06-22' => [
                'expenses' => ['Food' => 1700, 'Transport' => 760],
            ],
            '2026-06-23' => [
                'expenses' => ['Food' => 1050, 'Entertainment' => 800],
            ],
            '2026-06-24' => [
                'expenses' => [
                    'Food' => 1950,
                    'Entertainment' => 850,
                    'Transport' => 650,
                ],
            ],
            '2026-06-25' => [
                'expenses' => [
                    'Food' => 3100,
                    'Entertainment' => 2200,
                    'Transport' => 980,
                ],
                'note' => 'Daily page sample',
            ],
            '2026-06-26' => [
                'expenses' => ['Food' => 1250, 'Transport' => 500],
            ],
            '2026-06-27' => [
                'expenses' => ['Food' => 1600, 'Entertainment' => 1080],
                'note' => 'Apple Music',
            ],
        ];

        $expenseRows = [];
        $dailyNoteRows = [];

        foreach ($dailyData as $date => $data) {
            foreach ($data['expenses'] as $categoryName => $amount) {
                $expenseRows[] = [
                    'user_id' => $userId,
                    'category_id' => $categoryIds[$categoryName],
                    'amount' => $amount,
                    'expense_date' => $date,
                    'memo' => null,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }

            if (isset($data['note'])) {
                $dailyNoteRows[] = [
                    'user_id' => $userId,
                    'note_date' => $date,
                    'note' => $data['note'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
        }

        DB::table('expenses')->insert($expenseRows);
        DB::table('daily_notes')->insert($dailyNoteRows);
    }
}