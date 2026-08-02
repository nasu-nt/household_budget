import {
    toRequestDateString,
} from './calendar-utils';

/**
 * 表示対象期間の日別支出データをAPIから取得する。
 *
 * このファイルはAPI通信だけを担当する。
 * 取得したデータの画面表示や、
 * FullCalendar用イベントへの変換はcalendar.jsで行う。
 */
export const fetchCalendarDays = async (
    calendarUrl,
    fetchInfo,
) => {
    /*
     * Bladeから渡されたURLを基準に、
     * APIへアクセスするURLを作成する。
     */
    const url = new URL(
        calendarUrl,
        window.location.origin,
    );

    /*
     * FullCalendarが要求している表示期間を
     * クエリパラメータとして追加する。
     */
    url.searchParams.set(
        'start',
        toRequestDateString(fetchInfo.startStr),
    );

    url.searchParams.set(
        'end',
        toRequestDateString(fetchInfo.endStr),
    );

    const response = await fetch(url, {
        method: 'GET',
        headers: {
            Accept: 'application/json',
        },
    });

    /*
     * 400・404・500などの場合、
     * fetch自体は例外にならない。
     *
     * response.okを確認して、
     * HTTPエラーを明示的に例外として扱う。
     */
    if (!response.ok) {
        throw new Error(
            `Calendar data request failed: ${response.status}`,
        );
    }

    return response.json();
};