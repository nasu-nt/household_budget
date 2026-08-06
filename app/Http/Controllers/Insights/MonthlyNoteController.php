<?php

namespace App\Http\Controllers\Insights;

use App\Http\Controllers\Controller;
use App\Http\Requests\Insights\SaveMonthlyNoteRequest;
use App\Models\BudgetSetting;
use App\Models\MonthlyNote;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;

class MonthlyNoteController extends Controller
{
    /**
     * 選択された予算期間のノートを保存する。
     */
    public function update(
        SaveMonthlyNoteRequest $request,
        string $month,
    ): RedirectResponse {
        /*
         * URLのmonthをYYYY-MM形式として
         * 厳密に確認する。
         */
        try {
            $selectedMonth =
                CarbonImmutable::createFromFormat(
                    '!Y-m',
                    $month,
                );
        } catch (\Throwable) {
            abort(404);
        }

        if (
            $selectedMonth === false
            || $selectedMonth->format('Y-m')
                !== $month
        ) {
            abort(404);
        }

        $userId = (int) $request
            ->user()
            ->getAuthIdentifier();

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

        /*
         * Monthly Insightsと同じ方法で、
         * URLの月から予算期間を計算する。
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

        $validated = $request->validated();

        $note = trim(
            (string) ($validated['note'] ?? ''),
        );

        /*
         * 空欄で保存した場合は、
         * その予算期間のノートを削除する。
         */
        if ($note === '') {
            MonthlyNote::query()
                ->where('user_id', $userId)
                ->where(
                    'period_start_date',
                    $periodStart->toDateString(),
                )
                ->where(
                    'period_end_date',
                    $periodEnd->toDateString(),
                )
                ->delete();
        } else {
            /*
             * 同じ期間のノートがあれば更新し、
             * なければ新しく作成する。
             */
            MonthlyNote::query()->updateOrCreate(
                [
                    'user_id' => $userId,
                    'period_start_date' =>
                        $periodStart->toDateString(),
                    'period_end_date' =>
                        $periodEnd->toDateString(),
                ],
                [
                    'note' => $note,
                ],
            );
        }

        return redirect()
            ->route('insights.monthly', [
                'month' => $month,
            ])
            ->with(
                'success',
                'Monthly note saved.',
            );
    }

    /**
     * 指定月を終了月とする予算期間の
     * 終了日を取得する。
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
            return $month
                ->endOfMonth()
                ->startOfDay();
        }

        return $this->closingDateForMonth(
            $month,
            $closingDay,
        );
    }

    /**
     * 予算期間の終了日から、
     * 開始日を取得する。
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

        $previousClosing =
            $this->closingDateForMonth(
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
         * 月末日に補正する。
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
}