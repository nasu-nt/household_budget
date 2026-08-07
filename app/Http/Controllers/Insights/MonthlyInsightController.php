<?php

namespace App\Http\Controllers\Insights;

use App\Http\Controllers\Controller;
use App\Models\BudgetSetting;
use App\Models\Expense;
use App\Models\MonthlyNote;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonthlyInsightController extends Controller
{
    private const DEMO_MONTH = '2026-06';
    private const COMPARISON_PERIOD_COUNT = 6;

    /**
     * 現在の予算期間へ移動する。
     */
    public function current(
        Request $request
    ): RedirectResponse {
        /*
         * デモアカウントは表示期間を固定する。
         */
        if ($this->isDemoUser($request)) {
            return redirect()->route('insights.monthly', [
                'month' => self::DEMO_MONTH,
            ]);
        }

        $userId = (int) $request
            ->user()
            ->getAuthIdentifier();

        [
            $isEndOfMonth,
            $closingDay,
        ] = $this->budgetPeriodSettings($userId);

        /*
         * 今日が属する予算期間の終了日を取得する。
         */
        $currentPeriodEnd = $this->periodEndForDate(
            CarbonImmutable::now()->startOfDay(),
            $isEndOfMonth,
            $closingDay,
        );

        return redirect()->route('insights.monthly', [
            /*
             * URLのmonthは、予算期間の終了月を表す。
             */
            'month' => $currentPeriodEnd->format('Y-m'),
        ]);
    }

    /**
     * 指定された予算期間のMonthly Insightsを表示する。
     */
    public function show(
        Request $request,
        string $month
    ): View|RedirectResponse {
        $isDemoUser = $this->isDemoUser($request);

        /*
         * デモアカウントは2026年6月の期間へ固定する。
         */
        if (
            $isDemoUser
            && $month !== self::DEMO_MONTH
        ) {
            return redirect()->route('insights.monthly', [
                'month' => self::DEMO_MONTH,
            ]);
        }

        /*
         * YYYY-MM形式として厳密に確認する。
         */
        try {
            $selectedMonth = CarbonImmutable::createFromFormat(
                '!Y-m',
                $month,
            );
        } catch (\Throwable) {
            abort(404);
        }

        if (
            $selectedMonth === false
            || $selectedMonth->format('Y-m') !== $month
        ) {
            abort(404);
        }

        $userId = (int) $request
            ->user()
            ->getAuthIdentifier();

        [
            $isEndOfMonth,
            $closingDay,
            $monthlyBudget,
            $spendingLimit,
        ] = $this->budgetPeriodSettings($userId);

        if ($isDemoUser) {
            /*
             * デモアカウントは表示期間そのものも固定する。
             */
            $periodStart = CarbonImmutable::create(
                2026,
                5,
                28,
            )->startOfDay();

            $periodEnd = CarbonImmutable::create(
                2026,
                6,
                27,
            )->startOfDay();

            $currentPeriodMonth = self::DEMO_MONTH;
        } else {
            /*
             * URLで指定された月の締め日を、
             * 予算期間の終了日として扱う。
             */
            $periodEnd = $this->periodEndForMonth(
                $selectedMonth,
                $isEndOfMonth,
                $closingDay,
            );

            $periodStart = $this->periodStartForEnd(
                $periodEnd,
                $isEndOfMonth,
                $closingDay,
            );

            /*
             * Current periodボタン用。
             */
            $currentPeriodEnd = $this->periodEndForDate(
                CarbonImmutable::now()->startOfDay(),
                $isEndOfMonth,
                $closingDay,
            );

            $currentPeriodMonth = $currentPeriodEnd
                ->format('Y-m');
        }

        /*
        * 選択された予算期間のMonthly Noteを取得する。
        *
        * ノートが保存されていない場合はnullになる。
        */
        $monthlyNote = MonthlyNote::query()
            ->where('user_id', $userId)
            ->where(
                'period_start_date',
                $periodStart->toDateString(),
            )
            ->where(
                'period_end_date',
                $periodEnd->toDateString(),
            )
            ->value('note');

        /*
        * 選択された予算期間の支出合計。
        *
        * 対象データがない場合、sum()は0になる。
        */
        $currentPeriodTotal = (int) Expense::query()
            ->where('user_id', $userId)
            ->whereBetween('expense_date', [
                $periodStart->toDateString(),
                $periodEnd->toDateString(),
            ])
            ->sum('amount');

            /*
            * 予算期間の日数。
            *
            * 開始日と終了日の両方を含めるため、
            * 日付の差へ1日を加える。
            */
            $periodDays = (int) $periodStart
                ->diffInDays($periodEnd) + 1;

            /*
            * 予算の80%に当たる金額。
            *
            * Within budgetとSlightly highの
            * 境界として使用する。
            */
            $eightyPercentBudget = (int) round(
                $monthlyBudget * 0.8,
            );

            /*
            * 月間予算に対する現在の支出割合。
            *
            * 月間予算が0円の場合は、
            * 0除算を避けるため0%とする。
            */
            $budgetUsagePercentage = $monthlyBudget > 0
                ? (int) round(
                    ($currentPeriodTotal / $monthlyBudget)
                    * 100,
                )
                : 0;

            /*
            * 月間予算を超えた金額。
            *
            * 予算内の場合は0円。
            */
            $overBudgetAmount = max(
                $currentPeriodTotal - $monthlyBudget,
                0,
            );

            /*
            * Spending Limitまで残っている金額。
            *
            * 上限を超えている場合は0円。
            */
            $remainingToLimitAmount = max(
                $spendingLimit - $currentPeriodTotal,
                0,
            );

            /*
            * Spending Limitを超えた金額。
            *
            * 今後、上限超過時の表示切り替えに使用する。
            */
            $overLimitAmount = max(
                $currentPeriodTotal - $spendingLimit,
                0,
            );

            /*
            * プログレスバー全体の最大金額。
            *
            * Spending Limitの右側にも赤い領域を残すため、
            * 最大額より5%大きい値を表示範囲とする。
            *
            * 支出が上限額を超えた場合も、
            * 現在位置がバー外へ出ないようにする。
            */
            $progressMaximum = max(
                1,
                (int) ceil(
                    max(
                        $monthlyBudget,
                        $spendingLimit,
                        $currentPeriodTotal,
                    ) * 1.05,
                ),
            );

            /*
            * 各金額をプログレスバー上の位置へ変換する。
            */
            $eightyPercentPosition =
                $this->progressPosition(
                    $eightyPercentBudget,
                    $progressMaximum,
                );

            $monthlyBudgetPosition =
                $this->progressPosition(
                    $monthlyBudget,
                    $progressMaximum,
                );

            $spendingLimitPosition =
                $this->progressPosition(
                    $spendingLimit,
                    $progressMaximum,
                );

            $currentSpendingPosition =
                $this->progressPosition(
                    $currentPeriodTotal,
                    $progressMaximum,
                );

        /*
        * 選択期間の直前にある予算期間を取得する。
        *
        * 例:
        * 現在期間 2026-05-28〜2026-06-27
        * 前期間   2026-04-28〜2026-05-27
        */
        $previousPeriodEnd = $periodStart->subDay();

        $previousPeriodStart = $this->periodStartForEnd(
            $previousPeriodEnd,
            $isEndOfMonth,
            $closingDay,
        );

        /*
        * 前期間の支出合計。
        */
        $previousPeriodTotal = (int) Expense::query()
            ->where('user_id', $userId)
            ->whereBetween('expense_date', [
                $previousPeriodStart->toDateString(),
                $previousPeriodEnd->toDateString(),
            ])
            ->sum('amount');

        $previousPeriodDifference = $currentPeriodTotal
            - $previousPeriodTotal;

        $previousPeriodDifferencePercentage =
            $this->comparisonPercentage(
                $currentPeriodTotal,
                $previousPeriodTotal,
            );

        /*
        * 直前6期間の開始日を取得する。
        *
        * 現在期間が2026-05-28〜2026-06-27の場合:
        * 2025-11-28〜2026-05-27
        */
        $firstComparisonPeriodEnd = $periodEnd
            ->subMonthsNoOverflow(
                self::COMPARISON_PERIOD_COUNT,
            );

        $sixPeriodStart = $this->periodStartForEnd(
            $firstComparisonPeriodEnd,
            $isEndOfMonth,
            $closingDay,
        );

        $sixPeriodEnd = $previousPeriodEnd;

        /*
        * 直前6期間の支出合計と平均。
        *
        * 支出がない期間も0円の期間として扱うため、
        * 常に6で割る。
        */
        $sixPeriodTotal = (int) Expense::query()
            ->where('user_id', $userId)
            ->whereBetween('expense_date', [
                $sixPeriodStart->toDateString(),
                $sixPeriodEnd->toDateString(),
            ])
            ->sum('amount');

        $sixPeriodAverage = (int) round(
            $sixPeriodTotal
            / self::COMPARISON_PERIOD_COUNT,
        );

        $sixPeriodAverageDifference = $currentPeriodTotal
            - $sixPeriodAverage;

        $sixPeriodAverageDifferencePercentage =
            $this->comparisonPercentage(
                $currentPeriodTotal,
                $sixPeriodAverage,
            );

        /*
        * 前期間のカテゴリ別支出。
        *
        * 現在期間に存在するカテゴリとの比較に使用する。
        */
        $previousCategoryTotals = Expense::query()
            ->where('user_id', $userId)
            ->whereBetween('expense_date', [
                $previousPeriodStart->toDateString(),
                $previousPeriodEnd->toDateString(),
            ])
            ->select('category_id')
            ->selectRaw(
                'SUM(amount) AS total',
            )
            ->groupBy('category_id')
            ->pluck('total', 'category_id')
            ->map(
                fn ($amount): int => (int) $amount
            )
            ->all();

        /*
        * 選択された予算期間の支出をカテゴリ別に集計する。
        *
        * アーカイブ済みカテゴリも過去の支出には表示するため、
        * is_activeやarchived_atでは絞り込まない。
        */
        $categorySpending = Expense::query()
            ->join(
                'categories',
                'categories.id',
                '=',
                'expenses.category_id',
            )
            ->where(
                'expenses.user_id',
                $userId,
            )
            ->whereBetween('expenses.expense_date', [
                $periodStart->toDateString(),
                $periodEnd->toDateString(),
            ])
            ->select([
                'categories.id',
                'categories.name',
                'categories.color_code',
            ])
            ->selectRaw(
                'SUM(expenses.amount) AS total',
            )
            ->groupBy([
                'categories.id',
                'categories.name',
                'categories.color_code',
            ])
            ->orderByRaw(
                'SUM(expenses.amount) DESC',
            )
            ->get()
            ->map(function ($category) use (
                $currentPeriodTotal,
                $previousCategoryTotals,
            ): array {
                $categoryId = (int) $category->id;
                $amount = (int) $category->total;

                $previousAmount = (int) (
                    $previousCategoryTotals[$categoryId]
                    ?? 0
                );

                $difference = $amount - $previousAmount;

                $percentage = $currentPeriodTotal > 0
                    ? ($amount / $currentPeriodTotal) * 100
                    : 0;

                return [
                    'id' => $categoryId,
                    'name' => (string) $category->name,
                    'color' => (string) $category->color_code,
                    'amount' => $amount,

                    /*
                    * 画面に表示する整数の割合。
                    */
                    'percentage' => (int) round(
                        $percentage,
                    ),

                    /*
                    * 棒グラフには丸める前に近い割合を使う。
                    */
                    'barPercentage' => round(
                        min(
                            max($percentage, 0),
                            100,
                        ),
                        4,
                    ),

                    'previousAmount' => $previousAmount,
                    'difference' => $difference,
                    'differenceClass' =>
                        $this->comparisonClass($difference),
                ];
            })
            ->all();

        /*
        * 選択された予算期間の支出を日別に集計する。
        *
        * 支出が登録されていない日は、この取得結果には含まれない。
        */
        $dailyTotals = Expense::query()
            ->where('user_id', $userId)
            ->whereBetween('expense_date', [
                $periodStart->toDateString(),
                $periodEnd->toDateString(),
            ])
            ->select([
                'expense_date',
            ])
            ->selectRaw(
                'SUM(amount) AS total',
            )
            ->groupBy('expense_date')
            ->orderBy('expense_date')
            ->get()
            ->mapWithKeys(function ($expense): array {
                $date = CarbonImmutable::parse(
                    (string) $expense->expense_date,
                )->toDateString();

                return [
                    $date => (int) $expense->total,
                ];
            })
            ->all();

        /*
        * 支出がない日も0円として含め、
        * 予算期間内のすべての日付を作る。
        */
        $dailySpendingTrend = [];

        for (
            $dateCursor = $periodStart;
            $dateCursor->lessThanOrEqualTo($periodEnd);
            $dateCursor = $dateCursor->addDay()
        ) {
            $date = $dateCursor->toDateString();

            $dailySpendingTrend[] = [
                /*
                * Daily Insightsへの遷移に使用する日付。
                */
                'date' => $date,

                /*
                * グラフのX軸などに表示する短い日付。
                */
                'label' => $dateCursor->format('n/j'),

                /*
                * その日の支出合計。
                * 支出がない日は0円。
                */
                'amount' => (int) (
                    $dailyTotals[$date]
                    ?? 0
                ),
            ];
        }

        /*
        * Lowestの判定では、
        * 支出がない0円の日を対象外にする。
        */
        $spendingDays = array_values(
            array_filter(
                $dailySpendingTrend,
                fn (array $day): bool =>
                    $day['amount'] > 0,
            ),
        );

        $highestSpendingDay = null;
        $lowestSpendingDay = null;

        /*
        * 支出がある日から、
        * 最高額と最低額の日を取得する。
        *
        * 同額の日が複数ある場合は、
        * 期間内で最初の日を使用する。
        */
        foreach ($spendingDays as $day) {
            if (
                $highestSpendingDay === null
                || $day['amount']
                    > $highestSpendingDay['amount']
            ) {
                $highestSpendingDay = $day;
            }

            if (
                $lowestSpendingDay === null
                || $day['amount']
                    < $lowestSpendingDay['amount']
            ) {
                $lowestSpendingDay = $day;
            }
        }

        /*
        * Chart.jsで棒の色を変更できるように、
        * 各日へ状態を追加する。
        */
        $dailySpendingTrend = array_map(
            function (array $day) use (
                $highestSpendingDay,
                $lowestSpendingDay,
            ): array {
                $status = 'default';

                if (
                    $highestSpendingDay !== null
                    && $day['date']
                        === $highestSpendingDay['date']
                ) {
                    $status = 'highest';
                } elseif (
                    $lowestSpendingDay !== null
                    && $day['date']
                        === $lowestSpendingDay['date']
                ) {
                    $status = 'lowest';
                }

                $day['status'] = $status;

                return $day;
            },
            $dailySpendingTrend,
        );

        return view('insights.index', [
            'activeView' => 'monthly',
            'month' => $month,
            'isDemoUser' => $isDemoUser,

            'monthlyNote' => $monthlyNote,

            'periodStartDate' =>
                $periodStart->format('Y-m-d'),

            'periodStartDate' =>
                $periodStart->format('Y-m-d'),

            'periodEndDate' =>
                $periodEnd->format('Y-m-d'),

            'periodStartLabel' =>
                $periodStart->format('Y/m/d'),

            'periodEndLabel' =>
                $periodEnd->format('Y/m/d'),

            'previousMonth' => $selectedMonth
                ->subMonthNoOverflow()
                ->format('Y-m'),

            'nextMonth' => $selectedMonth
                ->addMonthNoOverflow()
                ->format('Y-m'),

            'currentPeriodMonth' =>
                $currentPeriodMonth,

            'currentPeriodTotal' =>
                $currentPeriodTotal,

            'periodDays' =>
                $periodDays,

            'monthlyBudget' =>
                $monthlyBudget,

            'spendingLimit' =>
                $spendingLimit,

            'eightyPercentBudget' =>
                $eightyPercentBudget,

            'budgetUsagePercentage' =>
                $budgetUsagePercentage,

            'overBudgetAmount' =>
                $overBudgetAmount,

            'remainingToLimitAmount' =>
                $remainingToLimitAmount,

            'overLimitAmount' =>
                $overLimitAmount,

            'eightyPercentPosition' =>
                $eightyPercentPosition,

            'monthlyBudgetPosition' =>
                $monthlyBudgetPosition,

            'spendingLimitPosition' =>
                $spendingLimitPosition,

            'currentSpendingPosition' =>
                $currentSpendingPosition,

            'previousPeriodTotal' =>
                $previousPeriodTotal,

            'previousPeriodDifference' =>
                $previousPeriodDifference,

            'previousPeriodDifferencePercentage' =>
                $previousPeriodDifferencePercentage,

            'previousPeriodDifferenceClass' =>
                $this->comparisonClass(
                    $previousPeriodDifference,
                ),

            'sixPeriodTotal' =>
                $sixPeriodTotal,

            'sixPeriodAverage' =>
                $sixPeriodAverage,

            'sixPeriodAverageDifference' =>
                $sixPeriodAverageDifference,

            'sixPeriodAverageDifferencePercentage' =>
                $sixPeriodAverageDifferencePercentage,

            'sixPeriodAverageDifferenceClass' =>
                $this->comparisonClass(
                    $sixPeriodAverageDifference,
                ),

            'categorySpending' =>
                $categorySpending,

            'dailySpendingTrend' =>
                $dailySpendingTrend,

            'highestSpendingDay' =>
                $highestSpendingDay,

            'lowestSpendingDay' =>
                $lowestSpendingDay,
        ]);
    }

    /**
     * ユーザーの予算期間と金額設定を取得する。
     *
     * @return array{
     *     0: bool,
     *     1: int|null,
     *     2: int,
     *     3: int
     * }
     */
    private function budgetPeriodSettings(
        int $userId
    ): array {
        $budgetSetting = BudgetSetting::query()
            ->where('user_id', $userId)
            ->first();

        $isEndOfMonth = (bool) (
            $budgetSetting?->is_end_of_month
            ?? BudgetSetting::DEFAULT_VALUES[
                'is_end_of_month'
            ]
        );

        $closingDayValue =
            $budgetSetting?->closing_day
            ?? BudgetSetting::DEFAULT_VALUES[
                'closing_day'
            ];

        $closingDay = $closingDayValue === null
            ? null
            : (int) $closingDayValue;

        $monthlyBudget = (int) (
            $budgetSetting?->monthly_budget
            ?? BudgetSetting::DEFAULT_VALUES[
                'monthly_budget'
            ]
        );

        $spendingLimit = (int) (
            $budgetSetting?->monthly_limit
            ?? BudgetSetting::DEFAULT_VALUES[
                'monthly_limit'
            ]
        );

        return [
            $isEndOfMonth,
            $closingDay,
            $monthlyBudget,
            $spendingLimit,
        ];
    }

    /**
     * 指定月を終了月とする予算期間の終了日を取得する。
     */
    private function periodEndForMonth(
        CarbonImmutable $month,
        bool $isEndOfMonth,
        ?int $closingDay,
    ): CarbonImmutable {
        if (
            $isEndOfMonth
            || $closingDay === null
        ) {
            return $month->endOfMonth()->startOfDay();
        }

        return $this->closingDateForMonth(
            $month,
            $closingDay,
        );
    }

    /**
     * 指定日が属する予算期間の終了日を取得する。
     */
    private function periodEndForDate(
        CarbonImmutable $date,
        bool $isEndOfMonth,
        ?int $closingDay,
    ): CarbonImmutable {
        $date = $date->startOfDay();

        if (
            $isEndOfMonth
            || $closingDay === null
        ) {
            return $date->endOfMonth()->startOfDay();
        }

        $currentMonthClosing = $this->closingDateForMonth(
            $date,
            $closingDay,
        );

        if ($date->lessThanOrEqualTo($currentMonthClosing)) {
            return $currentMonthClosing;
        }

        return $this->closingDateForMonth(
            $date->addMonthNoOverflow(),
            $closingDay,
        );
    }

    /**
     * 予算期間の終了日から開始日を取得する。
     */
    private function periodStartForEnd(
        CarbonImmutable $periodEnd,
        bool $isEndOfMonth,
        ?int $closingDay,
    ): CarbonImmutable {
        if (
            $isEndOfMonth
            || $closingDay === null
        ) {
            return $periodEnd->startOfMonth();
        }

        $previousClosing = $this->closingDateForMonth(
            $periodEnd->subMonthNoOverflow(),
            $closingDay,
        );

        return $previousClosing->addDay();
    }

    /**
     * 指定した年月の締め日を取得する。
     */
    private function closingDateForMonth(
        CarbonImmutable $date,
        int $closingDay,
    ): CarbonImmutable {
        $month = $date->startOfMonth();

        /*
         * 31日締めで2月を表示する場合などは、
         * その月の最終日に補正する。
         */
        $day = min(
            max($closingDay, 1),
            $month->daysInMonth,
        );

        return $month->setDate(
            $month->year,
            $month->month,
            $day,
        );
    }

    /**
     * 金額をプログレスバー上の割合へ変換する。
     *
     * 0%未満または100%を超える値にならないよう、
     * 表示範囲内へ補正する。
     */
    private function progressPosition(
        int $amount,
        int $maximum,
    ): float {
        if ($maximum <= 0) {
            return 0;
        }

        return round(
            min(
                max(
                    ($amount / $maximum) * 100,
                    0,
                ),
                100,
            ),
            4,
        );
    }

    /**
     * 比較対象との差額割合を取得する。
     *
     * 比較対象が0円の場合は割合を計算できないため、
     * nullを返す。
     */
    private function comparisonPercentage(
        int $currentAmount,
        int $comparisonAmount,
    ): ?float {
        if ($comparisonAmount === 0) {
            return null;
        }

        $difference = $currentAmount
            - $comparisonAmount;

        return round(
            ($difference / $comparisonAmount) * 100,
            1,
        );
    }

    /**
     * 支出差額に応じた表示クラスを返す。
     */
    private function comparisonClass(
        int $difference
    ): string {
        return match (true) {
            $difference > 0 => 'is-increase',
            $difference < 0 => 'is-decrease',
            default => 'is-neutral',
        };
    }

    /**
     * デモアカウントか判定する。
     */
    private function isDemoUser(
        Request $request
    ): bool {
        return $request->user()?->email
            === config(
                'demo.email',
                'demo@example.com',
            );
    }
}