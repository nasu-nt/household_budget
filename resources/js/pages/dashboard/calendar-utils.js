const STATUS_CLASS_MAP = Object.freeze({
    all_good: 'is-all-good',
    slightly_high: 'is-slightly-high',
    over_budget: 'is-over-budget',
    over_limit: 'is-over-limit',
});

const STATUS_CLASSES = Object.values(STATUS_CLASS_MAP);

/**
 * 金額を日本円表記へ変換する。
 *
 * 例:
 * 2680 → ¥2,680
 */
const formatCurrency = (amount) => {
    return `¥${amount.toLocaleString('ja-JP')}`;
};

/**
 * FullCalendarが返す日時文字列から、
 * APIへ送信するYYYY-MM-DD部分だけを取得する。
 *
 * 例:
 * 2027-07-01T00:00:00+09:00
 * ↓
 * 2027-07-01
 */
const toRequestDateString = (dateTime) => {
    return dateTime.slice(0, 10);
};

/**
 * Dateオブジェクトをローカル日付の
 * YYYY-MM-DD形式へ変換する。
 *
 * toISOString()はUTC（協定世界時）基準になるため、
 * 日本時間との時差で日付がずれる可能性がある。
 * そのため、年・月・日を個別に取得している。
 */
const toLocalDateString = (date) => {
    const year = date.getFullYear();

    const month = String(
        date.getMonth() + 1,
    ).padStart(2, '0');

    const day = String(
        date.getDate(),
    ).padStart(2, '0');

    return `${year}-${month}-${day}`;
};

/**
 * カレンダーの日付セルに付いている
 * 支出ステータス用クラスをすべて削除する。
 */
const clearStatusClasses = (element) => {
    element.classList.remove(...STATUS_CLASSES);
};

/**
 * 選択日を前月または翌月へ移動する。
 *
 * 31日から31日が存在しない月へ移動する場合は、
 * その月の最終日へ丸める。
 *
 * 例:
 * 2027-01-31の翌月
 * ↓
 * 2027-02-28
 */
const addMonthsClamped = (
    dateString,
    monthOffset,
) => {
    const [year, month, day] = dateString
        .split('-')
        .map(Number);

    /*
     * まず対象月の1日を作る。
     *
     * 直接31日のまま月を変更すると、
     * JavaScript側で翌月へはみ出す可能性があるため。
     */
    const baseDate = new Date(
        year,
        month - 1,
        1,
    );

    baseDate.setMonth(
        baseDate.getMonth() + monthOffset,
    );

    const targetYear = baseDate.getFullYear();
    const targetMonth = baseDate.getMonth();

    /*
     * 対象月の最終日を取得する。
     *
     * 翌月の0日を指定すると、
     * 対象月の最終日になる。
     */
    const lastDayOfMonth = new Date(
        targetYear,
        targetMonth + 1,
        0,
    ).getDate();

    const safeDay = Math.min(
        day,
        lastDayOfMonth,
    );

    const nextDate = new Date(
        targetYear,
        targetMonth,
        safeDay,
    );

    return toLocalDateString(nextDate);
};

export {
    STATUS_CLASS_MAP,
    addMonthsClamped,
    clearStatusClasses,
    formatCurrency,
    toLocalDateString,
    toRequestDateString,
};