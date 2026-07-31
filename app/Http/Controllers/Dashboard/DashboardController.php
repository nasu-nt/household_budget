<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BudgetSetting;
use App\Models\Expense;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboardを表示する。
     */
    public function index(Request $request): View
    {
        $categories = $request->user()
            ->categories()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('dashboard.index', [
            'categories' => $categories,
        ]);
    }

    /**
     * 指定された期間の日別支出合計を返す。
     *
     * FullCalendarのendは終了日を含まないため、
     * start以上、end未満のデータを取得する。
     */
    public function calendar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start' => [
                'required',
                'date_format:Y-m-d',
            ],
            'end' => [
                'required',
                'date_format:Y-m-d',
                'after:start',
            ],
        ]);

        $start = CarbonImmutable::parse($validated['start'])
            ->startOfDay();

        $end = CarbonImmutable::parse($validated['end'])
            ->startOfDay();

        if ($start->diffInDays($end) > 62) {
            throw ValidationException::withMessages([
                'end' => 'The calendar period must be 62 days or less.',
            ]);
        }

        $userId = (int) $request->user()->getAuthIdentifier();

        $budgetSetting = BudgetSetting::query()
            ->where('user_id', $userId)
            ->first();

        $monthlyBudget = $budgetSetting?->monthly_budget
            ?? BudgetSetting::DEFAULT_VALUES['monthly_budget'];

        $monthlyLimit = $budgetSetting?->monthly_limit
            ?? BudgetSetting::DEFAULT_VALUES['monthly_limit'];

        $isEndOfMonth = $budgetSetting?->is_end_of_month
            ?? BudgetSetting::DEFAULT_VALUES['is_end_of_month'];

        $closingDay = $budgetSetting?->closing_day
            ?? BudgetSetting::DEFAULT_VALUES['closing_day'];

        $dailyTotals = Expense::query()
            ->where('user_id', $userId)
            ->where('expense_date', '>=', $start->toDateString())
            ->where('expense_date', '<', $end->toDateString())
            ->selectRaw('expense_date, SUM(amount) AS total')
            ->groupBy('expense_date')
            ->orderBy('expense_date')
            ->get();

        $calendarDays = $dailyTotals->map(function (
            Expense $expense
        ) use (
            $monthlyBudget,
            $monthlyLimit,
            $isEndOfMonth,
            $closingDay
        ): array {
            $date = CarbonImmutable::instance(
                $expense->expense_date,
            );

            $total = (int) $expense->getAttribute('total');

            $periodDays = $this->periodDaysFor(
                $date,
                $isEndOfMonth,
                $closingDay,
            );

            $dailyBudget = (int) round(
                $monthlyBudget / $periodDays,
            );

            $dailyLimit = (int) round(
                $monthlyLimit / $periodDays,
            );

            return [
                'date' => $date->toDateString(),
                'total' => $total,
                'status' => $this->resolveStatus(
                    $total,
                    $dailyBudget,
                    $dailyLimit,
                ),
            ];
        });

        return response()->json($calendarDays->values());
    }

    /**
     * 指定日の属する予算期間の日数を求める。
     */
    private function periodDaysFor(
        CarbonImmutable $date,
        bool $isEndOfMonth,
        ?int $closingDay,
    ): int {
        if ($isEndOfMonth || $closingDay === null) {
            return $date->daysInMonth;
        }

        $currentMonthClosing = $this->closingDateForMonth(
            $date,
            $closingDay,
        );

        if ($date->lessThanOrEqualTo($currentMonthClosing)) {
            $periodEnd = $currentMonthClosing;
        } else {
            $periodEnd = $this->closingDateForMonth(
                $date->addMonthNoOverflow(),
                $closingDay,
            );
        }

        $previousClosing = $this->closingDateForMonth(
            $periodEnd->subMonthNoOverflow(),
            $closingDay,
        );

        $periodStart = $previousClosing->addDay();

        return (int) $periodStart->diffInDays($periodEnd) + 1;
    }

    /**
     * 指定した年月の締め日を取得する。
     *
     * 31日を指定していて対象月が30日までの場合などは、
     * その月の末日を締め日として扱う。
     */
    private function closingDateForMonth(
        CarbonImmutable $date,
        int $closingDay,
    ): CarbonImmutable {
        $month = $date->startOfMonth();

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
     * 日別支出額から表示状態を判定する。
     */
    private function resolveStatus(
        int $total,
        int $dailyBudget,
        int $dailyLimit,
    ): string {
        $slightlyHighThreshold = $dailyBudget * 0.8;

        if ($total < $slightlyHighThreshold) {
            return 'all_good';
        }

        if ($total <= $dailyBudget) {
            return 'slightly_high';
        }

        if ($total <= $dailyLimit) {
            return 'over_budget';
        }

        return 'over_limit';
    }
}