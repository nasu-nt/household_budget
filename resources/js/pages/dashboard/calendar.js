import { Calendar } from 'fullcalendar';

import 'fullcalendar/skeleton.css';
import 'fullcalendar/themes/classic/theme.css';
import 'fullcalendar/themes/classic/palette.css';

import {
    fetchCalendarDays,
} from './calendar-api';

import {
    createCalendarOptions,
} from './calendar-options';

import {
    STATUS_CLASS_MAP,
    addMonthsClamped,
    clearStatusClasses,
    formatCurrency,
    toLocalDateString,
} from './calendar-utils';

/**
 * 日付入力のカレンダーピッカーを開く。
 *
 * showPicker()に対応していないブラウザでは、
 * focus()とclick()へ切り替える。
 */
const openDatePicker = (dateInput) => {
    if (!dateInput) {
        return;
    }

    if (
        typeof dateInput.showPicker
        === 'function'
    ) {
        dateInput.showPicker();

        return;
    }

    dateInput.focus();
    dateInput.click();
};

/**
 * ダッシュボードの月間カレンダーを初期化する。
 */
export const initDashboardCalendar = () => {
    /*
     * ========================================
     * 1. 必要なDOM要素を取得する
     * ========================================
     */

    const calendarElement = document.querySelector(
        '[data-dashboard-calendar]',
    );

    const monthLink = document.querySelector(
        '[data-dashboard-calendar-month-link]',
    );

    const spendingDateElement = document.querySelector(
        '[data-dashboard-calendar-spending-date]',
    );

    /*
     * ダッシュボード以外の画面では、
     * カレンダー要素が存在しないため処理しない。
     */
    if (!calendarElement) {
        return;
    }

    /*
     * Bladeのdata属性から値を取得する。
     */
    const calendarUrl =
        calendarElement.dataset.dashboardCalendarUrl;

    const demoDate =
        calendarElement.dataset.demoDate;

    if (!calendarUrl) {
        console.error(
            'Calendar data URL is not defined.',
        );

        return;
    }

    const dateInput = document.querySelector(
        '[data-dashboard-calendar-date]',
    );

    const datePickerButton =
        document.querySelector(
            '[data-dashboard-calendar-date-picker]',
        );

    const prevButton = document.querySelector(
        '[data-dashboard-calendar-prev]',
    );

    const nextButton = document.querySelector(
        '[data-dashboard-calendar-next]',
    );

    const spendingElement =
        document.querySelector(
            '[data-dashboard-calendar-spending]',
        );

    /*
     * ========================================
     * 2. カレンダーの状態を保持する
     * ========================================
     */

    /*
     * 日付ごとのFullCalendarセル要素。
     *
     * キー:
     * 2027-07-27
     *
     * 値:
     * 対応する<td>要素
     */
    const dayCellElements = new Map();

    /*
     * 日付ごとの支出合計。
     *
     * キー:
     * 2027-07-27
     *
     * 値:
     * 2680
     */
    const dailySpending = new Map();

    /*
     * 日付ごとの予算ステータス。
     *
     * キー:
     * 2027-07-27
     *
     * 値:
     * all_goodなど
     */
    const dailyStatuses = new Map();

    /*
     * 現在選択されている日付。
     *
     * 優先順位:
     * 1. デモアカウント用日付
     * 2. 日付入力欄の値
     * 3. 実際の今日
     */
    let selectedDate = demoDate
        || dateInput?.value
        || toLocalDateString(new Date());

    /*
     * ========================================
     * 3. 日付セルと支出表示を更新する
     * ========================================
     */

    /**
     * 指定した日付の日付セルへ、
     * 予算ステータス用クラスを付ける。
     */
    const applyStatusToDayCell = (date) => {
        const dayCell =
            dayCellElements.get(date);

        /*
         * 対象月がまだ表示されていない場合など、
         * セルが存在しなければ何もしない。
         */
        if (!dayCell) {
            return;
        }

        /*
         * 古いステータスクラスを削除する。
         */
        clearStatusClasses(dayCell);

        const status =
            dailyStatuses.get(date);

        const statusClass =
            STATUS_CLASS_MAP[status];

        if (statusClass) {
            dayCell.classList.add(
                statusClass,
            );
        }
    };

    /**
     * 選択日の支出額を画面へ表示する。
     */
    const updateSpending = (date) => {
        if (!spendingElement) {
            return;
        }

        /*
         * 支出データがない日は0円にする。
         */
        const amount =
            dailySpending.get(date) ?? 0;

        spendingElement.textContent =
            formatCurrency(amount);
    };

    /**
     * 選択中の日付セルへ
     * is-selectedクラスを付ける。
     */
    const applySelectedDate = () => {
        dayCellElements.forEach(
            (dayCell, date) => {
                dayCell.classList.toggle(
                    'is-selected',
                    date === selectedDate,
                );
            },
        );
    };

    /**
     * YYYY-MM-DDをローカルのDateへ変換する。
     */
    const parseDateString = (dateString) => {
        const [year, month, day] = dateString
            .split('-')
            .map(Number);

        return new Date(
            year,
            month - 1,
            day,
        );
    };

    /*
     * 月次Insightsリンクと選択日ラベルを更新する。
     */
    const updateDateLabels = (dateString) => {
        const date = parseDateString(dateString);

        const monthName = new Intl.DateTimeFormat(
            'en-US',
            {
                month: 'long',
                year: 'numeric',
            },
        ).format(date);

        if (monthLink) {
            monthLink.textContent = monthName;

            monthLink.setAttribute(
                'aria-label',
                `View monthly insights for ${monthName}`,
            );
        }

        if (spendingDateElement) {
            const shortMonthName = new Intl.DateTimeFormat(
                'en-US',
                {
                    month: 'short',
                },
            ).format(date);

            spendingDateElement.textContent =
                `${shortMonthName} ${date.getDate()}`;
        }
    };

    /*
     * 選択日を変更する。
     */
    const selectDate = (date) => {
        selectedDate = date;

        if (dateInput) {
            dateInput.value = date;
        }

        updateDateLabels(date);
        applySelectedDate();
        updateSpending(date);
    };

    /*
     * ========================================
     * 4. APIデータをカレンダーへ反映する
     * ========================================
     */

    /**
     * FullCalendarから呼び出される
     * イベント取得関数。
     */
    const fetchCalendarEvents = async (
        fetchInfo,
        successCallback,
        failureCallback,
    ) => {
        try {
            /*
             * 表示期間の日別支出をAPIから取得する。
             */
            const calendarDays =
                await fetchCalendarDays(
                    calendarUrl,
                    fetchInfo,
                );

            /*
             * 前回表示していた月の情報を削除する。
             */
            dailySpending.clear();
            dailyStatuses.clear();

            dayCellElements.forEach(
                (dayCell) => {
                    clearStatusClasses(
                        dayCell,
                    );
                },
            );

            /*
             * APIレスポンスを
             * FullCalendarのイベント形式へ変換する。
             */
            const events = calendarDays.map(
                (calendarDay) => {
                    const total = Number(
                        calendarDay.total,
                    );

                    const status = String(
                        calendarDay.status,
                    );

                    /*
                    * 選択日の支出表示や
                    * セルの色分けに使用するため保存する。
                    */
                    dailySpending.set(
                        calendarDay.date,
                        total,
                    );

                    dailyStatuses.set(
                        calendarDay.date,
                        status,
                    );

                    applyStatusToDayCell(
                        calendarDay.date,
                    );

                    return {
                        id:
                            `daily-spending-${calendarDay.date}`,

                        /*
                        * eventContentで独自表示するため、
                        * titleは空にする。
                        */
                        title: '',

                        start: calendarDay.date,
                        allDay: true,

                        extendedProps: {
                            total,
                            status,
                        },
                    };
                },
            );

            /*
             * 取得成功をFullCalendarへ通知する。
             */
            successCallback(events);

            /*
             * API取得後に選択日の支出額を更新する。
             */
            updateSpending(selectedDate);
        } catch (error) {
            console.error(error);

            /*
             * 取得失敗をFullCalendarへ通知する。
             */
            failureCallback(error);
        }
    };

    /*
     * ========================================
     * 5. FullCalendarを初期化する
     * ========================================
     */

    const initialDate = demoDate
        || dateInput?.value
        || new Date();

    const calendarOptions =
        createCalendarOptions({
            initialDate,
            demoDate,
            fetchCalendarEvents,
            dayCellElements,
            applyStatusToDayCell,

            /*
             * calendar-options.jsから
             * 現在の選択日を取得するための関数。
             */
            getSelectedDate: () => {
                return selectedDate;
            },

            selectDate,
        });

    const calendar = new Calendar(
        calendarElement,
        calendarOptions,
    );

    calendar.render();

    /*
     * 初期選択日を入力欄・セル・支出額へ反映する。
     */
    selectDate(selectedDate);

    /*
     * ========================================
     * 6. カレンダー外の操作を設定する
     * ========================================
     */

    /**
     * 日付入力欄から日付を変更した場合。
     */
    dateInput?.addEventListener(
        'change',
        (event) => {
            const date =
                event.currentTarget.value;

            if (!date) {
                return;
            }

            selectDate(date);

            /*
             * 選択した日付の月へ移動する。
             */
            calendar.gotoDate(date);
        },
    );

    /**
     * カレンダーアイコンを押した場合。
     */
    datePickerButton?.addEventListener(
        'click',
        () => {
            openDatePicker(dateInput);
        },
    );

    /**
     * 前月ボタンを押した場合。
     */
    prevButton?.addEventListener(
        'click',
        () => {
            const previousDate =
                addMonthsClamped(
                    selectedDate,
                    -1,
                );

            selectDate(previousDate);
            calendar.gotoDate(
                previousDate,
            );
        },
    );

    /**
     * 翌月ボタンを押した場合。
     */
    nextButton?.addEventListener(
        'click',
        () => {
            const nextDate =
                addMonthsClamped(
                    selectedDate,
                    1,
                );

            selectDate(nextDate);
            calendar.gotoDate(
                nextDate,
            );
        },
    );

    monthLink?.addEventListener('click', (event) => {
        /*
        * TODO:
        * Monthly Insights完成後に削除し、
        * 実際のURLへ遷移させる。
        */
        event.preventDefault();
    });
};