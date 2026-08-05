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
     * デモアカウントと、Monthly Insightsの比較に必要な
     * 2025-11-28〜2026-06-27のモックデータを登録する。
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

            /*
            * Monthly Insightsの前期間比較と
            * 直前6期間平均に使用する過去データ。
            */
            $this->createHistoricalExpenses(
                userId: $demoUser->id,
                categoryIds: $categoryIds,
                timestamp: $now,
            );

            $this->createExpensesAndDailyNotes(
                userId: $demoUser->id,
                categoryIds: $categoryIds,
                timestamp: $now,
            );
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
     * Monthly Insightsの比較に使用する過去6期間の支出を作成する。
     *
     * 直前6期間:
     * 2025-11-28〜2026-05-27
     *
     * 合計:
     * 795,600円
     *
     * @param array<string, int> $categoryIds
     */
    private function createHistoricalExpenses(
        int $userId,
        array $categoryIds,
        mixed $timestamp,
    ): void {
        /*
        * 過去期間はMonthly Insightsの比較計算に使用する。
        *
        * デモアカウントでは表示期間を
        * 2026-05-28〜2026-06-27に固定しているため、
        * ここではカテゴリ単位の集計用レコードとして登録する。
        */
        $historicalData = [
            /*
            * 2025-11-28〜2025-12-27
            * 合計: 129,800円
            */
            '2025-11-28 - 2025-12-27' => [
                [
                    'date' => '2025-12-01',
                    'category' => 'Rent',
                    'amount' => 42_000,
                ],
                [
                    'date' => '2025-12-05',
                    'category' => 'Food',
                    'amount' => 46_200,
                ],
                [
                    'date' => '2025-12-07',
                    'category' => 'Utilities',
                    'amount' => 11_600,
                ],
                [
                    'date' => '2025-12-15',
                    'category' => 'Entertainment',
                    'amount' => 15_400,
                ],
                [
                    'date' => '2025-12-20',
                    'category' => 'Transport',
                    'amount' => 14_600,
                ],
            ],

            /*
            * 2025-12-28〜2026-01-27
            * 合計: 137,600円
            */
            '2025-12-28 - 2026-01-27' => [
                [
                    'date' => '2026-01-01',
                    'category' => 'Rent',
                    'amount' => 42_000,
                ],
                [
                    'date' => '2026-01-05',
                    'category' => 'Food',
                    'amount' => 48_300,
                ],
                [
                    'date' => '2026-01-07',
                    'category' => 'Utilities',
                    'amount' => 14_200,
                ],
                [
                    'date' => '2026-01-15',
                    'category' => 'Entertainment',
                    'amount' => 18_100,
                ],
                [
                    'date' => '2026-01-20',
                    'category' => 'Transport',
                    'amount' => 15_000,
                ],
            ],

            /*
            * 2026-01-28〜2026-02-27
            * 合計: 130,900円
            */
            '2026-01-28 - 2026-02-27' => [
                [
                    'date' => '2026-02-01',
                    'category' => 'Rent',
                    'amount' => 42_000,
                ],
                [
                    'date' => '2026-02-05',
                    'category' => 'Food',
                    'amount' => 45_700,
                ],
                [
                    'date' => '2026-02-07',
                    'category' => 'Utilities',
                    'amount' => 12_000,
                ],
                [
                    'date' => '2026-02-15',
                    'category' => 'Entertainment',
                    'amount' => 16_600,
                ],
                [
                    'date' => '2026-02-20',
                    'category' => 'Transport',
                    'amount' => 14_600,
                ],
            ],

            /*
            * 2026-02-28〜2026-03-27
            * 合計: 143,200円
            */
            '2026-02-28 - 2026-03-27' => [
                [
                    'date' => '2026-03-01',
                    'category' => 'Rent',
                    'amount' => 42_000,
                ],
                [
                    'date' => '2026-03-05',
                    'category' => 'Food',
                    'amount' => 50_600,
                ],
                [
                    'date' => '2026-03-07',
                    'category' => 'Utilities',
                    'amount' => 14_500,
                ],
                [
                    'date' => '2026-03-15',
                    'category' => 'Entertainment',
                    'amount' => 20_500,
                ],
                [
                    'date' => '2026-03-20',
                    'category' => 'Transport',
                    'amount' => 15_600,
                ],
            ],

            /*
            * 2026-03-28〜2026-04-27
            * 合計: 132,600円
            */
            '2026-03-28 - 2026-04-27' => [
                [
                    'date' => '2026-04-01',
                    'category' => 'Rent',
                    'amount' => 42_000,
                ],
                [
                    'date' => '2026-04-05',
                    'category' => 'Food',
                    'amount' => 47_100,
                ],
                [
                    'date' => '2026-04-07',
                    'category' => 'Utilities',
                    'amount' => 11_800,
                ],
                [
                    'date' => '2026-04-15',
                    'category' => 'Entertainment',
                    'amount' => 17_400,
                ],
                [
                    'date' => '2026-04-20',
                    'category' => 'Transport',
                    'amount' => 14_300,
                ],
            ],

            /*
            * 2026-04-28〜2026-05-27
            * 合計: 121,500円
            */
            '2026-04-28 - 2026-05-27' => [
                [
                    'date' => '2026-05-01',
                    'category' => 'Rent',
                    'amount' => 42_000,
                ],
                [
                    'date' => '2026-05-05',
                    'category' => 'Food',
                    'amount' => 44_000,
                ],
                [
                    'date' => '2026-05-07',
                    'category' => 'Utilities',
                    'amount' => 7_400,
                ],
                [
                    'date' => '2026-05-15',
                    'category' => 'Entertainment',
                    'amount' => 14_900,
                ],
                [
                    'date' => '2026-05-20',
                    'category' => 'Transport',
                    'amount' => 13_200,
                ],
            ],
        ];

        $expenseRows = [];

        foreach ($historicalData as $periodExpenses) {
            foreach ($periodExpenses as $expense) {
                $expenseRows[] = [
                    'user_id' => $userId,
                    'category_id' =>
                        $categoryIds[$expense['category']],
                    'amount' => $expense['amount'],
                    'expense_date' => $expense['date'],
                    'memo' => null,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
        }

        DB::table('expenses')->insert($expenseRows);
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