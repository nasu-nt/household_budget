import Chart from 'chart.js/auto';

/**
 * 金額を日本円表示へ整形する。
 */
const yenFormatter = new Intl.NumberFormat('ja-JP');

/**
 * Monthly Insightsの日別支出グラフを初期化する。
 */
export default function initMonthlySpendingChart() {
    const section = document.querySelector(
        '[data-monthly-spending-trend]',
    );

    /*
     * Monthly Insights以外の画面では何もしない。
     */
    if (!section) {
        return;
    }

    const canvas = section.querySelector(
        '[data-monthly-spending-chart]',
    );

    const dataElement = section.querySelector(
        '[data-monthly-spending-trend-data]',
    );

    if (
        !(canvas instanceof HTMLCanvasElement)
        || !(dataElement instanceof HTMLScriptElement)
    ) {
        console.error(
            'Monthly spending chart elements were not found.',
        );

        return;
    }

    let dailyData;

    try {
        dailyData = JSON.parse(
            dataElement.textContent?.trim() || '[]',
        );
    } catch (error) {
        console.error(
            'Failed to parse monthly spending trend data.',
            error,
        );

        return;
    }

    if (
        !Array.isArray(dailyData)
        || dailyData.length === 0
    ) {
        console.error(
            'Monthly spending trend data is empty.',
        );

        return;
    }

    const dailyUrlTemplate =
        canvas.dataset.dailyUrlTemplate;

    if (!dailyUrlTemplate) {
        console.error(
            'Daily Insights URL template was not found.',
        );

        return;
    }

    /*
     * Highest、Lowest、通常日の色を決める。
     */
    const barColors = dailyData.map((day) => {
        if (day.status === 'highest') {
            return '#ef1717';
        }

        if (day.status === 'lowest') {
            return '#2563eb';
        }

        return '#949494';
    });

    /*
     * グラフで使用する色をCSS変数から取得する。
     * CSS変数が取得できない場合は、
     * 右側のフォールバック色を使用する。
     */
    const rootStyles = window.getComputedStyle(
        document.documentElement,
    );

    const textColor =
        rootStyles
            .getPropertyValue('--c-text')
            .trim()
        || '#333333';

    const gridColor =
        rootStyles
            .getPropertyValue('--c-border')
            .trim()
        || 'rgba(0, 0, 0, 0.12)';

    const axisColor =
        rootStyles
            .getPropertyValue('--c-border-strong')
            .trim()
        || '#666666';

    /*
     * Viteの再読み込みなどによる二重生成を防ぐ。
     */
    const existingChart = Chart.getChart(canvas);

    if (existingChart) {
        existingChart.destroy();
    }

    new Chart(canvas, {
        type: 'bar',

        data: {
            labels: dailyData.map(
                (day) => day.label,
            ),

            datasets: [
                {
                    data: dailyData.map(
                        (day) => day.amount,
                    ),

                    backgroundColor: barColors,
                    borderColor: barColors,
                    borderWidth: 0,
                    borderRadius: 1,
                    borderSkipped: false,
                    barPercentage: 0.65,
                    categoryPercentage: 0.85,
                    maxBarThickness: 24,
                },
            ],
        },

        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 3,

            animation: {
                duration: 250,
            },

            plugins: {
                legend: {
                    display: false,
                },

                tooltip: {
                    callbacks: {
                        title(items) {
                            const dataIndex =
                                items[0]?.dataIndex;

                            return dailyData[
                                dataIndex
                            ]?.label ?? '';
                        },

                        label(context) {
                            const amount = Number(
                                context.parsed.y,
                            );

                            return `¥${yenFormatter.format(
                                amount,
                            )}`;
                        },
                    },
                },
            },

            scales: {
                x: {
                    offset: true,

                    /*
                     * グラフ下部の横軸。
                     * widthで線の太さを変更する。
                     */
                    border: {
                        display: true,
                        color: axisColor,
                        width: 2,
                    },

                    ticks: {
                        autoSkip: false,
                        color: textColor,
                        minRotation: 90,
                        maxRotation: 90,

                        /*
                         * X軸の日付文字を大きくする。
                         */
                        font: {
                            size: 14,
                        },

                        /*
                         * 期間開始日を0番目として、
                         * 0・7・14・21・28番目だけ表示する。
                         */
                        callback(value, index) {
                            if (index % 7 !== 0) {
                                return '';
                            }

                            return dailyData[
                                index
                            ]?.label ?? '';
                        },
                    },
                },

                y: {
                    beginAtZero: true,

                    /*
                     * グラフ左側の縦軸。
                     * widthで線の太さを変更する。
                     */
                    border: {
                        display: true,
                        color: axisColor,
                        width: 2,
                    },

                    ticks: {
                        color: textColor,

                        callback(value) {
                            return `¥${yenFormatter.format(
                                Number(value),
                            )}`;
                        },
                    },
                },
            },

            /**
             * 棒の上にカーソルがある場合だけ
             * pointerを表示する。
             */
            onHover(event, elements) {
                const target = event.native?.target;

                if (
                    !(target instanceof HTMLCanvasElement)
                ) {
                    return;
                }

                target.style.cursor =
                    elements.length > 0
                        ? 'pointer'
                        : 'default';
            },

            /**
             * クリックした棒の日付の
             * Daily Insightsへ移動する。
             */
            onClick(event, elements) {
                const clickedBar = elements[0];

                if (!clickedBar) {
                    return;
                }

                const clickedDay =
                    dailyData[clickedBar.index];

                if (!clickedDay?.date) {
                    return;
                }

                const destinationUrl =
                    dailyUrlTemplate.replace(
                        '__DATE__',
                        encodeURIComponent(
                            clickedDay.date,
                        ),
                    );

                window.location.assign(
                    destinationUrl,
                );
            },
        },
    });
}