import dayGridPlugin from 'fullcalendar/daygrid';
import interactionPlugin from 'fullcalendar/interaction';
import themePlugin from 'fullcalendar/themes/classic';

import {
    formatCurrency,
    toLocalDateString,
} from './calendar-utils';

/**
 * 日別支出を、Daily Insightsへのリンクとして表示する。
 */
const createAmountEventContent = (info) => {
    const amount = Number(
        info.event.extendedProps.total,
    );

    const dailyInsightsUrl = String(
        info.event.extendedProps.dailyInsightsUrl,
    );

    const amountLink =
        document.createElement('a');

    amountLink.className =
        'dashboard-calendar__amount-link';

    amountLink.href = dailyInsightsUrl;

    amountLink.textContent =
        formatCurrency(amount);

    amountLink.setAttribute(
        'aria-label',
        `${info.event.startStr} spending ${formatCurrency(amount)}. View daily insights.`,
    );

    return {
        domNodes: [amountLink],
    };
};

/**
 * FullCalendarへ渡す設定オブジェクトを作成する。
 *
 * FullCalendar固有の設定をcalendar.jsから分離し、
 * メインファイルを読みやすくしている。
 */
export const createCalendarOptions = ({
    initialDate,
    demoDate,
    fetchCalendarEvents,
    dayCellElements,
    applyStatusToDayCell,
    getSelectedDate,
    selectDate,
}) => {
    const options = {
        plugins: [
            themePlugin,
            dayGridPlugin,
            interactionPlugin,
        ],

        /*
         * 月単位のカレンダーを表示する。
         */
        initialView: 'dayGridMonth',

        /*
         * 最初に表示する日付。
         *
         * デモユーザーの場合は固定日、
         * 通常ユーザーの場合は実際の日付が渡される。
         */
        initialDate,

        /*
         * FullCalendar標準のヘッダーは使わず、
         * Blade側に作成した操作ボタンを使う。
         */
        headerToolbar: false,

        /*
         * 曜日ヘッダー用のCSSクラス。
         */
        dayHeaderClass:
            'dashboard-calendar__weekday',

        dayHeaderInnerClass:
            'dashboard-calendar__weekday-inner',

        /*
         * 日付セル右上の数字部分へ、
         * アプリ側で管理するクラスを付ける。
         */
        dayCellTopClass:
            'dashboard-calendar__day-top',

        dayCellTopInnerClass:
            'dashboard-calendar__day-number',

        /*
         * 月曜日を週の開始日にする。
         *
         * 0: 日曜日
         * 1: 月曜日
         */
        firstDay: 1,

        /*
         * 常に6週間分を表示せず、
         * その月に必要な週数だけ表示する。
         */
        fixedWeekCount: false,

        /*
         * 前月・翌月の日付を表示しない。
         */
        showNonCurrentDates: false,

        /*
         * 内容に合わせて高さを自動調整する。
         */
        height: 'auto',

        /*
         * 支出イベントに時刻を表示しない。
         */
        displayEventTime: false,

        /*
         * カレンダーへ表示するイベントを
         * APIから取得する関数。
         */
        events: fetchCalendarEvents,

        /**
         * 曜日ヘッダーが画面へ追加されたときの処理。
         *
         * 土曜日と日曜日へ専用クラスを追加する。
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
                info.el.classList.add(
                    'is-saturday',
                );
            }

            if (dayOfWeek === 0) {
                info.el.classList.add(
                    'is-sunday',
                );
            }
        },

        /**
         * 日付セルが画面へ追加されたときの処理。
         */
        dayCellDidMount: (info) => {
            const date = toLocalDateString(
                info.date,
            );

            /*
             * 日付からセル要素を取得できるように、
             * Mapへ保存する。
             */
            dayCellElements.set(
                date,
                info.el,
            );

            /*
             * APIから取得済みのステータスがあれば、
             * セルへ色分け用クラスを付ける。
             */
            applyStatusToDayCell(date);

            /*
             * 現在選択中の日付であれば、
             * 選択状態のクラスを付ける。
             */
            info.el.classList.toggle(
                'is-selected',
                date === getSelectedDate(),
            );
        },

        /**
         * 日付セルが画面から削除される前の処理。
         *
         * 月移動後に古いDOM要素を保持し続けないよう、
         * Mapから削除する。
         */
        dayCellWillUnmount: (info) => {
            const date = toLocalDateString(
                info.date,
            );

            dayCellElements.delete(date);
        },

        /**
         * 日付をクリックしたときの処理。
         */
        dateClick: (info) => {
            selectDate(info.dateStr);
        },

        eventDidMount: (info) => {
            const eventElement = info.el;

            eventElement.style.setProperty(
                '--fc-event-bg-color',
                'transparent',
            );

            eventElement.style.setProperty(
                '--fc-event-border-color',
                'transparent',
            );

            eventElement.style.setProperty(
                '--fc-event-text-color',
                'var(--c-text)',
            );

            eventElement.style.setProperty(
                '--fc-event-contrast-color',
                'var(--c-text)',
            );

            eventElement.style.setProperty(
                'background',
                'transparent',
                'important',
            );

            eventElement.style.setProperty(
                'background-color',
                'transparent',
                'important',
            );

            eventElement.style.setProperty(
                'border',
                '0',
                'important',
            );

            eventElement.style.setProperty(
                'box-shadow',
                'none',
                'important',
            );

            const eventMain =
                eventElement.querySelector(
                    '.fc-event-main',
                );

            if (eventMain) {
                eventMain.style.setProperty(
                    'background',
                    'transparent',
                    'important',
                );

                eventMain.style.setProperty(
                    'background-color',
                    'transparent',
                    'important',
                );

                eventMain.style.setProperty(
                    'color',
                    'var(--c-text)',
                    'important',
                );
            }
        },

        eventContent: createAmountEventContent,

        eventClass:
            'dashboard-calendar__amount-event',
    };

    /*
     * デモユーザーだけ、
     * FullCalendar内部の「今日」を固定する。
     */
    if (demoDate) {
        options.now = demoDate;
    }

    return options;
};