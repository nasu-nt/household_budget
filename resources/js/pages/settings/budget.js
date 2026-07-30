const MILLISECONDS_PER_DAY = 86_400_000;

export function budgetSettingsForm(initialValues) {
    return {
        monthlyBudget: Number(initialValues.monthlyBudget) || 0,
        monthlyLimit: Number(initialValues.monthlyLimit) || 0,
        monthlyBudgetInput: '',
        monthlyLimitInput: '',
        isEndOfMonth: Boolean(initialValues.isEndOfMonth),
        closingDay: Number(initialValues.closingDay) || 27,

        init() {
            this.monthlyBudgetInput = this.formatNumber(
                this.monthlyBudget,
            );

            this.monthlyLimitInput = this.formatNumber(
                this.monthlyLimit,
            );
        },

        toInteger(value) {
            const digits = String(value).replace(/[^0-9]/g, '');

            return digits === '' ? 0 : Number(digits);
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
            this.monthlyBudget = this.toInteger(value);
            this.monthlyBudgetInput = this.formatNumber(
                this.monthlyBudget,
            );
        },

        updateMonthlyLimit(value) {
            this.monthlyLimit = this.toInteger(value);
            this.monthlyLimitInput = this.formatNumber(
                this.monthlyLimit,
            );
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
                Date.UTC(year, month, now.getDate()),
            );

            const currentClosingDate = this.closingDate(
                year,
                month,
                this.closingDay,
            );

            let startDate;
            let endDate;

            if (today.getTime() <= currentClosingDate.getTime()) {
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
