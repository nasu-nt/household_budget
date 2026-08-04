const MILLISECONDS_PER_DAY = 86_400_000;

export function budgetSettingsForm(initialValues) {
    const monthlyBudget = Number(initialValues.monthlyBudget) || 0;
    const monthlyLimit = Number(initialValues.monthlyLimit) || 0;

    return {
        isComposing: false,
        monthlyBudget,
        monthlyLimit,

        monthlyBudgetInput: new Intl.NumberFormat('en-US').format(
            monthlyBudget,
        ),

        monthlyLimitInput: new Intl.NumberFormat('en-US').format(
            monthlyLimit,
        ),

        isEndOfMonth: Boolean(initialValues.isEndOfMonth),

        closingDay: Number(initialValues.closingDay) || 27,

        normalizeDigits(value) {
            return String(value)
                .normalize('NFKC')
                .replace(/[^0-9]/g, '')
                .slice(0, 10);
        },

        formatNumber(value) {
            return new Intl.NumberFormat('en-US').format(
                Number(value) || 0,
            );
        },

        formatCurrency(value) {
            return `¥${this.formatNumber(value)}`;
        },

        updateMonthlyBudget(value) {
            const digits = this.normalizeDigits(value);

            this.monthlyBudget =
                digits === '' ? 0 : Number(digits);

            this.monthlyBudgetInput =
                digits === ''
                    ? ''
                    : this.formatNumber(digits);
        },

        updateMonthlyLimit(value) {
            const digits = this.normalizeDigits(value);

            this.monthlyLimit =
                digits === '' ? 0 : Number(digits);

            this.monthlyLimitInput =
                digits === ''
                    ? ''
                    : this.formatNumber(digits);
        },

        daysInMonth(year, month) {
            return new Date(
                Date.UTC(year, month + 1, 0),
            ).getUTCDate();
        },

        closingDate(year, month, closingDay) {
            const actualDay = Math.min(
                Number(closingDay) || 1,
                this.daysInMonth(year, month),
            );

            return new Date(
                Date.UTC(year, month, actualDay),
            );
        },

        get periodDays() {
            const now = new Date();
            const year = now.getFullYear();
            const month = now.getMonth();

            if (this.isEndOfMonth) {
                return this.daysInMonth(year, month);
            }

            const today = new Date(
                Date.UTC(
                    year,
                    month,
                    now.getDate(),
                ),
            );

            const currentClosingDate = this.closingDate(
                year,
                month,
                this.closingDay,
            );

            let startDate;
            let endDate;

            if (
                today.getTime()
                <= currentClosingDate.getTime()
            ) {
                const previousMonth = new Date(
                    Date.UTC(year, month - 1, 1),
                );

                const previousClosingDate = this.closingDate(
                    previousMonth.getUTCFullYear(),
                    previousMonth.getUTCMonth(),
                    this.closingDay,
                );

                startDate = new Date(
                    previousClosingDate.getTime()
                    + MILLISECONDS_PER_DAY,
                );

                endDate = currentClosingDate;
            } else {
                const nextMonth = new Date(
                    Date.UTC(year, month + 1, 1),
                );

                startDate = new Date(
                    currentClosingDate.getTime()
                    + MILLISECONDS_PER_DAY,
                );

                endDate = this.closingDate(
                    nextMonth.getUTCFullYear(),
                    nextMonth.getUTCMonth(),
                    this.closingDay,
                );
            }

            return Math.round(
                (
                    endDate.getTime()
                    - startDate.getTime()
                ) / MILLISECONDS_PER_DAY,
            ) + 1;
        },

        get dailyGuideline() {
            return Math.round(
                this.monthlyBudget / this.periodDays,
            );
        },

        get dailyLimit() {
            return Math.round(
                this.monthlyLimit / this.periodDays,
            );
        },
    };
}