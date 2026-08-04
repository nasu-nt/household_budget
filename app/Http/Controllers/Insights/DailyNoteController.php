<?php

namespace App\Http\Controllers\Insights;

use App\Http\Controllers\Controller;
use App\Http\Requests\Insights\SaveDailyNoteRequest;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;

class DailyNoteController extends Controller
{
    public function update(
        SaveDailyNoteRequest $request,
        string $date
    ): RedirectResponse {
        try {
            $noteDate = CarbonImmutable::createFromFormat(
                '!Y-m-d',
                $date,
            );
        } catch (\Throwable) {
            abort(404);
        }

        if ($noteDate->format('Y-m-d') !== $date) {
            abort(404);
        }

        $validated = $request->validated();

        $note = trim(
            (string) ($validated['note'] ?? ''),
        );

        /*
         * 空欄で保存した場合は、
         * その日のノートを削除する。
         */
        if ($note === '') {
            $request->user()
                ->dailyNotes()
                ->whereDate('note_date', $date)
                ->delete();
        } else {
            /*
             * 同日のノートがあれば更新、
             * なければ新規作成する。
             */
            $request->user()
                ->dailyNotes()
                ->updateOrCreate(
                    [
                        'note_date' => $date,
                    ],
                    [
                        'note' => $note,
                    ],
                );
        }

        return redirect()
            ->route('insights.daily', [
                'date' => $date,
            ])
            ->with(
                'success',
                'Spending note saved.',
            );
    }
}