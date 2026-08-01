import { Calendar } from 'fullcalendar';
import dayGridPlugin from 'fullcalendar/daygrid';
import interactionPlugin from 'fullcalendar/interaction';
import themePlugin from 'fullcalendar/themes/classic';

import 'fullcalendar/skeleton.css';
import 'fullcalendar/themes/classic/theme.css';
import 'fullcalendar/themes/classic/palette.css';

const STATUS_CLASS_MAP = Object.freeze({
    all_good: 'is-all-good',
    slightly_high: 'is-slightly-high',
    over_budget: 'is-over-budget',
    over_limit: 'is-over-limit',
});

const STATUS_CLASSES = Object.values(STATUS_CLASS_MAP);

const formatCurrency = (amount) => {
    return `¥${amount.toLocaleString('ja-JP')}`;
};

const toRequestDateString = (dateTime) => {
    return dateTime.slice(0, 10);
};

const toLocalDateString = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
};

const formatDisplayDate = (date) => {
    const [year, month, day] = date.split('-');

    return `${year}/${month}/${day}`;
};

const clearStatusClasses = (element) => {
    element.classList.remove(...STATUS_CLASSES);
};

const addMonthsClamped = (dateString, monthOffset) => {
    const [year, month, day] = dateString.split('-').map(Number);

    const baseDate = new Date(year, month - 1, 1);
    baseDate.setMonth(baseDate.getMonth() + monthOffset);

    const targetYear = baseDate.getFullYear();
    const targetMonth = baseDate.getMonth();
    const lastDayOfMonth = new Date(
        targetYear,
        targetMonth + 1,
        0,
    ).getDate();

    const safeDay = Math.min(day, lastDayOfMonth);
    const nextDate = new Date(
        targetYear,
        targetMonth,
        safeDay,
    );

    return toLocalDateString(nextDate);
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

    const datePickerButton = document.querySelector(
    '[data-dashboard-calendar-date-picker]',
    );

    const dateDisplay = document.querySelector(
        '[data-dashboard-calendar-date-display]',
    );

    const pickerButton = document.querySelector(
        '[data-dashboard-calendar-picker-button]',
    );

    const prevButton = document.querySelector(
        '[data-dashboard-calendar-prev]',
    );

    const nextButton = document.querySelector(
        '[data-dashboard-calendar-next]',
    );

    const spendingElement = document.querySelector(
        '[data-dashboard-calendar-spending]',
    );

    const dayCellElements = new Map();
    const dailySpending = new Map();
    const dailyStatuses = new Map();

    let selectedDate = dateInput?.value
        || toLocalDateString(new Date());

    const updateDateDisplay = (date) => {
        if (!dateDisplay) {
            return;
        }

        dateDisplay.textContent = formatDisplayDate(date);
    };

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

    const updateSpending = (date) => {
        if (!spendingElement) {
            return;
        }

        const amount = dailySpending.get(date) ?? 0;

        spendingElement.textContent = formatCurrency(amount);
    };

    const applySelectedDate = () => {
        dayCellElements.forEach((dayCell, date) => {
            dayCell.classList.toggle(
                'is-selected',
                date === selectedDate,
            );
        });
    };

    const selectDate = (date) => {
        selectedDate = date;

        if (dateInput) {
            dateInput.value = date;
        }

        updateDateDisplay(date);
        applySelectedDate();
        updateSpending(date);
    };

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

        // 曜日ヘッダー用のクラス
        dayHeaderClass: 'dashboard-calendar__weekday',
        dayHeaderInnerClass: 'dashboard-calendar__weekday-inner',

        firstDay: 1,
        /*
        * 曜日ヘッダーへ曜日別のクラスを付ける。
        *
        * Date#getDay()
        * 0: 日曜日
        * 6: 土曜日
        */
        dayHeaderDidMount: (info) => {
            const dayOfWeek = info.date.getDay();

            info.el.classList.add(
                'dashboard-calendar__weekday',
            );

            if (dayOfWeek === 6) {
                info.el.classList.add('is-saturday');
            }

            if (dayOfWeek === 0) {
                info.el.classList.add('is-sunday');
            }
        },

        fixedWeekCount: false,

        fixedWeekCount: false,
        showNonCurrentDates: false,
        height: 'auto',
        displayEventTime: false,
        events: fetchCalendarEvents,

        dayCellDidMount: (info) => {
            const date = toLocalDateString(info.date);

            dayCellElements.set(date, info.el);
            applyStatusToDayCell(date);

            info.el.classList.toggle(
                'is-selected',
                date === selectedDate,
            );
        },

        dayCellWillUnmount: (info) => {
            const date = toLocalDateString(info.date);

            dayCellElements.delete(date);
        },

        dateClick: (info) => {
            selectDate(info.dateStr);
        },

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

        eventClass: 'dashboard-calendar__amount-event',
    });

    calendar.render();
    updateDateDisplay(selectedDate);

    dateInput?.addEventListener('change', (event) => {
        const date = event.currentTarget.value;

        if (!date) {
            return;
        }

        selectDate(date);
        calendar.gotoDate(date);
    });

datePickerButton?.addEventListener('click', () => {
    if (!dateInput) {
        return;
    }

    if (typeof dateInput.showPicker === 'function') {
        dateInput.showPicker();

        return;
    }

    /*
     * showPickerに未対応のブラウザ向け。
     */
    dateInput.focus();
    dateInput.click();
});

    pickerButton?.addEventListener('click', () => {
        if (!dateInput) {
            return;
        }

        if (typeof dateInput.showPicker === 'function') {
            dateInput.showPicker();

            return;
        }

        dateInput.click();
    });

    dateDisplay?.addEventListener('click', () => {
        if (!dateInput) {
            return;
        }

        if (typeof dateInput.showPicker === 'function') {
            dateInput.showPicker();

            return;
        }

        dateInput.click();
    });

    prevButton?.addEventListener('click', () => {
        const previousDate = addMonthsClamped(selectedDate, -1);

        selectDate(previousDate);
        calendar.gotoDate(previousDate);
    });

    nextButton?.addEventListener('click', () => {
        const nextDate = addMonthsClamped(selectedDate, 1);

        selectDate(nextDate);
        calendar.gotoDate(nextDate);
    });
};