<?php

namespace App\Http\Controllers\Insights;

use App\Http\Controllers\Controller;
use App\Models\BudgetSetting;
use App\Models\Category;
use App\Models\Expense;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DailyInsightController extends Controller
{
    public function show(
        Request $request,
        string $date
    ): View|RedirectResponse {
        $isDemoUser = $request->user()?->email
            === config('demo.email', 'demo@example.com');

        $demoDate = '2026-06-19';

        /*
         * デモアカウントでは表示日を固定する。
         */
        if ($isDemoUser && $date !== $demoDate) {
            return redirect()->route('insights.daily', [
                'date' => $demoDate,
            ]);
        }

        try {
            $selectedDate = CarbonImmutable::createFromFormat(
                '!Y-m-d',
                $date,
            );
        } catch (\Throwable) {
            abort(404);
        }

        if ($selectedDate->format('Y-m-d') !== $date) {
            abort(404);
        }

        $userId = (int) $request
            ->user()
            ->getAuthIdentifier();

        $previousDateObject = $selectedDate->subDay();

        /*
         * 選択日の支出合計。
         *
         * 対象データがない場合、sum()は0になる。
         */
        $currentDayTotal = (int) Expense::query()
            ->where('user_id', $userId)
            ->where(
                'expense_date',
                $selectedDate->toDateString(),
            )
            ->sum('amount');

        /*
         * 選択日の支出をカテゴリ別に集計する。
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
            ->where('expenses.user_id', $userId)
            ->where(
                'expenses.expense_date',
                $selectedDate->toDateString(),
            )
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
            ->map(function ($category) use ($currentDayTotal): array {
                $amount = (int) $category->total;

                $percentage = $currentDayTotal > 0
                    ? ($amount / $currentDayTotal) * 100
                    : 0;

                return [
                    'id' => (int) $category->id,
                    'name' => (string) $category->name,
                    'color' => (string) $category->color_code,
                    'amount' => $amount,

                    /*
                    * 画面に表示する整数の割合。
                    */
                    'percentage' => (int) round($percentage),

                    /*
                    * 棒グラフの幅には、丸める前に近い値を使う。
                    */
                    'barPercentage' => round(
                        min(max($percentage, 0), 100),
                        4,
                    ),
                ];
            })->all();

        /*
         * Records一覧の並び順。
         *
         * URL例:
         * ?sort=amount&direction=asc
         */
        $recordSortColumns = [
            'recorded_time' => 'expenses.recorded_time',
            'category' => 'categories.name',
            'amount' => 'expenses.amount',
        ];

        $recordSort = (string) $request->query(
            'sort',
            'recorded_time',
        );

        $recordDirection = strtolower(
            (string) $request->query(
                'direction',
                'desc',
            ),
        );

        /*
        * 許可していない並び替え項目は、
        * Recorded timeへ戻す。
        */
        if (! array_key_exists(
            $recordSort,
            $recordSortColumns,
        )) {
            $recordSort = 'recorded_time';
        }

        /*
        * asc・desc以外は使用しない。
        */
        if (! in_array(
            $recordDirection,
            ['asc', 'desc'],
            true,
        )) {
            $recordDirection = 'desc';
        }

        /*
        * 選択日の支出レコード一覧。
        */
        $dailyRecordsQuery = Expense::query()
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
            ->where(
                'expenses.expense_date',
                $selectedDate->toDateString(),
            )
            ->select([
                'expenses.id',
                'expenses.recorded_time',
                'expenses.category_id',
                'expenses.amount',
                'expenses.memo',
                'categories.name AS category_name',
                'categories.color_code AS category_color',
            ]);

        /*
        * Recorded timeの場合だけ、
        * 時刻未登録のデータを最後へ置く。
        */
        if ($recordSort === 'recorded_time') {
            $dailyRecordsQuery->orderByRaw(
                $recordDirection === 'asc'
                    ? 'expenses.recorded_time ASC NULLS LAST'
                    : 'expenses.recorded_time DESC NULLS LAST',
            );
        } else {
            $dailyRecordsQuery->orderBy(
                $recordSortColumns[$recordSort],
                $recordDirection,
            );
        }

        /*
        * 同じ値だった場合でも順番が安定するよう、
        * 最後にIDの新しい順を指定する。
        */
        $dailyRecords = $dailyRecordsQuery
            ->orderByDesc('expenses.id')
            ->get()
            ->map(function ($expense): array {
                $recordedTime = $expense->recorded_time;

                return [
                    'id' => (int) $expense->id,

                    'recordedTime' => $recordedTime === null
                        ? null
                        : substr(
                            (string) $recordedTime,
                            0,
                            5,
                        ),

                    'categoryId' =>
                        (int) $expense->category_id,

                    'categoryName' =>
                        (string) $expense->category_name,

                    'categoryColor' =>
                        (string) $expense->category_color,

                    'amount' =>
                        (int) $expense->amount,

                    'memo' => $expense->memo === null
                        ? ''
                        : (string) $expense->memo,
                ];
            })
            ->all();
        
        $recordCategories = Category::query()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'color_code',
            ]);

        /*
         * 編集対象のレコードID。
         *
         * この日・このユーザーのRecordsに存在するIDだけ許可する。
         */
        $requestedEditRecordId = $request->integer('edit');

        $dailyRecordIds = array_column(
            $dailyRecords,
            'id',
        );

        $editRecordId = in_array(
            $requestedEditRecordId,
            $dailyRecordIds,
            true,
        )
            ? $requestedEditRecordId
            : null;

        /*
        * 編集フォームで選択できる有効なカテゴリ。
        */
        $recordCategories = Category::query()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'color_code',
            ]);

        /*
         * 前日の支出合計。
         *
         * 対象データがない場合は0として比較する。
         */
        $previousDayTotal = (int) Expense::query()
            ->where('user_id', $userId)
            ->where(
                'expense_date',
                $previousDateObject->toDateString(),
            )
            ->sum('amount');

        $budgetSetting = BudgetSetting::query()
            ->where('user_id', $userId)
            ->first();

        $isEndOfMonth = (bool) (
            $budgetSetting?->is_end_of_month
            ?? BudgetSetting::DEFAULT_VALUES['is_end_of_month']
        );

        $closingDayValue = $budgetSetting?->closing_day
            ?? BudgetSetting::DEFAULT_VALUES['closing_day'];

        $closingDay = $closingDayValue === null
            ? null
            : (int) $closingDayValue;

        /*
         * 選択日が属する予算期間の開始日。
         */
        $periodStart = $this->periodStartFor(
            $selectedDate,
            $isEndOfMonth,
            $closingDay,
        );

        /*
         * 予算期間開始日から選択日までの支出合計。
         */
        $periodTotal = (int) Expense::query()
            ->where('user_id', $userId)
            ->whereBetween('expense_date', [
                $periodStart->toDateString(),
                $selectedDate->toDateString(),
            ])
            ->sum('amount');

        /*
         * 未入力の日も0円の日として含めるため、
         * 支出を登録した日数ではなく暦日数で割る。
         */
        $elapsedDays = (int) $periodStart
            ->diffInDays($selectedDate) + 1;

        $periodDailyAverage = (int) round(
            $periodTotal / max($elapsedDays, 1),
        );

        $previousDayDifference = $currentDayTotal
            - $previousDayTotal;

        $averageDifference = $currentDayTotal
            - $periodDailyAverage;

        $dailyNote = $request->user()
            ->dailyNotes()
            ->whereDate(
                'note_date',
                $selectedDate->toDateString(),
            )
            ->value('note');

        return view('insights.index', [
            'activeView' => 'daily',
            'date' => $selectedDate->format('Y-m-d'),
            'dateLabel' => $selectedDate->format('Y/m/d'),
            'previousDate' => $previousDateObject
                ->format('Y-m-d'),
            'nextDate' => $selectedDate
                ->addDay()
                ->format('Y-m-d'),
            'todayDate' => $isDemoUser
                ? $demoDate
                : now()->format('Y-m-d'),
            'isDemoUser' => $isDemoUser,

            'currentDayTotal' => $currentDayTotal,
            'categorySpending' => $categorySpending,
            'dailyRecords' => $dailyRecords,
            'recordSort' => $recordSort,
            'recordDirection' => $recordDirection,
            'editRecordId' => $editRecordId,
            'recordCategories' => $recordCategories,
            'previousDayTotal' => $previousDayTotal,
            'previousDayDifference' => $previousDayDifference,
            'previousDayDifferenceClass' =>
                $this->comparisonClass(
                    $previousDayDifference,
                ),

            'periodDailyAverage' => $periodDailyAverage,
            'averageDifference' => $averageDifference,
            'averageDifferenceClass' =>
                $this->comparisonClass(
                    $averageDifference,
                ),
            'dailyNote' => $dailyNote,
        ]);
    }

    /**
     * 指定日が属する予算期間の開始日を取得する。
     */
    private function periodStartFor(
        CarbonImmutable $date,
        bool $isEndOfMonth,
        ?int $closingDay,
    ): CarbonImmutable {
        if ($isEndOfMonth || $closingDay === null) {
            return $date->startOfMonth();
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
}