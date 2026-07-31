import { Calendar } from 'fullcalendar';
import dayGridPlugin from 'fullcalendar/daygrid';
import interactionPlugin from 'fullcalendar/interaction';
import themePlugin from 'fullcalendar/themes/classic';

import 'fullcalendar/skeleton.css';
import 'fullcalendar/themes/classic/theme.css';
import 'fullcalendar/themes/classic/palette.css';

/**
 * Laravelから返されたstatusと、
 * 日付セルへ付与するSCSSクラスの対応表。
 */
const STATUS_CLASS_MAP = Object.freeze({
    all_good: 'is-all-good',
    slightly_high: 'is-slightly-high',
    over_budget: 'is-over-budget',
    over_limit: 'is-over-limit',
});

const STATUS_CLASSES = Object.values(STATUS_CLASS_MAP);

/**
 * 金額を日本円表示へ変換する。
 *
 * @param {number} amount
 * @returns {string}
 */
const formatCurrency = (amount) => {
    return `¥${amount.toLocaleString('ja-JP')}`;
};

/**
 * FullCalendarから渡されたISO 8601形式の日時を、
 * Laravelへ送るYYYY-MM-DD形式へ変換する。
 *
 * @param {string} dateTime
 * @returns {string}
 */
const toRequestDateString = (dateTime) => {
    return dateTime.slice(0, 10);
};

/**
 * JavaScriptのDateをYYYY-MM-DD形式へ変換する。
 *
 * toISOString()はUTCへ変換されて日付がずれる可能性があるため、
 * ローカル時間の年月日を個別に取得する。
 *
 * @param {Date} date
 * @returns {string}
 */
const toLocalDateString = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
};

/**
 * 日付セルから既存のステータスクラスを削除する。
 *
 * @param {HTMLElement} element
 */
const clearStatusClasses = (element) => {
    element.classList.remove(...STATUS_CLASSES);
};

export const initDashboardCalendar = () => {
    const calendarElement = document.querySelector(
        '[data-dashboard-calendar]',
    );

    if (!calendarElement) {
        return;
    }

    const calendarUrl = calendarElement.dataset.dashboardCalendarUrl;

    if (!calendarUrl) {
        console.error('Calendar data URL is not defined.');

        return;
    }

    const dateInput = document.querySelector(
        '[data-dashboard-calendar-date]',
    );

    const spendingElement = document.querySelector(
        '[data-dashboard-calendar-spending]',
    );

    /**
     * 表示中の日付セルを、日付をキーとして保持する。
     *
     * @type {Map<string, HTMLElement>}
     */
    const dayCellElements = new Map();

    /**
     * APIから取得した日付ごとの支出合計。
     *
     * @type {Map<string, number>}
     */
    const dailySpending = new Map();

    /**
     * APIから取得した日付ごとのステータス。
     *
     * @type {Map<string, string>}
     */
    const dailyStatuses = new Map();

    /**
     * 現在選択されている日付。
     *
     * @type {string}
     */
    let selectedDate = dateInput?.value
        || toLocalDateString(new Date());

    /**
     * 指定した日付セルへステータスクラスを反映する。
     *
     * @param {string} date
     */
    const applyStatusToDayCell = (date) => {
        const dayCell = dayCellElements.get(date);

        if (!dayCell) {
            return;
        }

        clearStatusClasses(dayCell);

        const status = dailyStatuses.get(date);
        const statusClass = STATUS_CLASS_MAP[status];

        if (statusClass) {
            dayCell.classList.add(statusClass);
        }
    };

    /**
     * 選択日の支出額を上部へ反映する。
     *
     * @param {string} date
     */
    const updateSpending = (date) => {
        if (!spendingElement) {
            return;
        }

        const amount = dailySpending.get(date) ?? 0;

        spendingElement.textContent = formatCurrency(amount);
    };

    /**
     * 表示中の日付セルへ選択状態を反映する。
     */
    const applySelectedDate = () => {
        dayCellElements.forEach((dayCell, date) => {
            dayCell.classList.toggle(
                'is-selected',
                date === selectedDate,
            );
        });
    };

    /**
     * 選択日を変更する。
     *
     * @param {string} date
     */
    const selectDate = (date) => {
        selectedDate = date;

        if (dateInput) {
            dateInput.value = date;
        }

        applySelectedDate();
        updateSpending(date);
    };

    /**
     * Laravelから表示期間の日別支出を取得する。
     *
     * @param {Object} fetchInfo
     * @param {string} fetchInfo.startStr
     * @param {string} fetchInfo.endStr
     * @param {Function} successCallback
     * @param {Function} failureCallback
     */
    const fetchCalendarEvents = async (
        fetchInfo,
        successCallback,
        failureCallback,
    ) => {
        const url = new URL(calendarUrl, window.location.origin);

        url.searchParams.set(
            'start',
            toRequestDateString(fetchInfo.startStr),
        );

        url.searchParams.set(
            'end',
            toRequestDateString(fetchInfo.endStr),
        );

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error(
                    `Calendar data request failed: ${response.status}`,
                );
            }

            const calendarDays = await response.json();

            dailySpending.clear();
            dailyStatuses.clear();

            dayCellElements.forEach((dayCell) => {
                clearStatusClasses(dayCell);
            });

            const events = calendarDays.map((calendarDay) => {
                const total = Number(calendarDay.total);
                const status = String(calendarDay.status);

                dailySpending.set(calendarDay.date, total);
                dailyStatuses.set(calendarDay.date, status);

                applyStatusToDayCell(calendarDay.date);

                return {
                    id: `daily-spending-${calendarDay.date}`,
                    title: '',
                    start: calendarDay.date,
                    allDay: true,
                    extendedProps: {
                        total,
                        status,
                    },
                };
            });

            successCallback(events);
            updateSpending(selectedDate);

        } catch (error) {
            console.error(error);

            failureCallback(error);
        }
    };

    const initialDate = dateInput?.value || new Date();

    const calendar = new Calendar(calendarElement, {
        plugins: [
            themePlugin,
            dayGridPlugin,
            interactionPlugin,
        ],

        initialView: 'dayGridMonth',
        initialDate,

        headerToolbar: false,

        firstDay: 1,
        fixedWeekCount: false,
        showNonCurrentDates: false,
        height: 'auto',
        displayEventTime: false,

        events: fetchCalendarEvents,

        /*
         * 日付セルが表示されたときにHTMLElementを保存し、
         * APIから取得済みのstatusがあればクラスを反映する。
         */
        dayCellDidMount: (info) => {
            const date = toLocalDateString(info.date);

            dayCellElements.set(date, info.el);
            applyStatusToDayCell(date);

            info.el.classList.toggle(
                'is-selected',
                date === selectedDate,
            );
        },

        /*
         * 月移動などで日付セルが破棄されたら、
         * Mapからも削除する。
         */
        dayCellWillUnmount: (info) => {
            const date = toLocalDateString(info.date);

            dayCellElements.delete(date);
        },

        /*
        * 日付セルをクリックしたときに選択日を更新する。
        */
        dateClick: (info) => {
            selectDate(info.dateStr);
        },

        /*
         * FullCalendar標準のイベントタイトルではなく、
         * 金額だけを表示する。
         */
        eventContent: (info) => {
            const amountElement = document.createElement('span');

            amountElement.className = 'dashboard-calendar__amount';
            amountElement.textContent = formatCurrency(
                Number(info.event.extendedProps.total),
            );

            return {
                domNodes: [amountElement],
            };
        },

        /*
         * FullCalendar内部のクラスではなく、
         * 自分たちのクラスで金額表示を装飾する。
         */
        eventClass: 'dashboard-calendar__amount-event',
    });

    calendar.render();

    dateInput?.addEventListener('change', (event) => {
        const date = event.currentTarget.value;

        if (!date) {
            return;
        }

        selectDate(date);
        calendar.gotoDate(date);
    });
};