<?php

namespace App\Http\Controllers\Insights;

use App\Http\Controllers\Controller;
use App\Models\BudgetSetting;
use App\Models\Expense;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonthlyInsightController extends Controller
{
    private const DEMO_MONTH = '2026-06';

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
            [
                $isEndOfMonth,
                $closingDay,
            ] = $this->budgetPeriodSettings($userId);

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
                $currentPeriodTotal
            ): array {
                $amount = (int) $category->total;

                $percentage = $currentPeriodTotal > 0
                    ? ($amount / $currentPeriodTotal) * 100
                    : 0;

                return [
                    'id' => (int) $category->id,
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
                ];
            })
            ->all();

        return view('insights.index', [
            'activeView' => 'monthly',
            'month' => $month,
            'isDemoUser' => $isDemoUser,

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

            'categorySpending' =>
                $categorySpending,
        ]);
    }

    /**
     * ユーザーの締め日設定を取得する。
     *
     * @return array{0: bool, 1: int|null}
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

        return [
            $isEndOfMonth,
            $closingDay,
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