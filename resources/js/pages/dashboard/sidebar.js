const MAX_EXPENSES = 5;

const FIELD_NAMES = [
    'expense_date',
    'recorded_time',
    'category_id',
    'amount',
    'memo',
];

function updateSidebarA11y(sidebar, button, isClosed) {
    button.setAttribute('aria-expanded', String(!isClosed));
    button.setAttribute(
        'aria-label',
        isClosed ? 'Open expense sidebar' : 'Close expense sidebar',
    );

    sidebar.setAttribute(
        'data-sidebar-state',
        isClosed ? 'closed' : 'open',
    );
}

function initSidebarToggle() {
    const sidebar = document.querySelector(
        '[data-dashboard-sidebar]',
    );

    const button = document.querySelector(
        '[data-dashboard-sidebar-toggle]',
    );

    if (!sidebar || !button) {
        return;
    }

    updateSidebarA11y(
        sidebar,
        button,
        sidebar.classList.contains('is-closed'),
    );

    button.addEventListener('click', () => {
        const isClosed = sidebar.classList.toggle('is-closed');

        updateSidebarA11y(
            sidebar,
            button,
            isClosed,
        );
    });
}

function removeValidationState(card) {
    card.querySelectorAll(
        '[data-expense-error]',
    ).forEach((error) => {
        error.remove();
    });

    card.querySelectorAll(
        '.is-invalid',
    ).forEach((field) => {
        field.classList.remove('is-invalid');
        field.removeAttribute('aria-invalid');
        field.removeAttribute('aria-describedby');
    });
}

/**
 * 現在時刻をinput[type="time"]用のHH:MM形式で返す。
 */
function getCurrentTime() {
    const now = new Date();

    const hours = String(
        now.getHours(),
    ).padStart(2, '0');

    const minutes = String(
        now.getMinutes(),
    ).padStart(2, '0');

    return `${hours}:${minutes}`;
}

/**
 * 複製した支出入力欄を新規入力状態へ戻す。
 */
function resetCardValues(card, defaultDate) {
    card.querySelectorAll(
        '[data-expense-field]',
    ).forEach((field) => {
        const fieldName = field.dataset.expenseField;

        if (fieldName === 'expense_date') {
            field.value = defaultDate;
            return;
        }

        if (fieldName === 'recorded_time') {
            field.value = getCurrentTime();
            return;
        }

        field.value = '';
    });
}

function updateCardIndex(card, index) {
    const legend = card.querySelector(
        '[data-expense-legend]',
    );

    if (legend) {
        legend.textContent = `Expense ${index + 1}`;
    }

    FIELD_NAMES.forEach((fieldName) => {
        const field = card.querySelector(
            `[data-expense-field="${fieldName}"]`,
        );

        const label = card.querySelector(
            `[data-expense-label="${fieldName}"]`,
        );

        if (!field) {
            return;
        }

        const id = `expense_${fieldName}_${index}`;

        field.id = id;
        field.name = `expenses[${index}][${fieldName}]`;

        if (label) {
            label.htmlFor = id;
        }
    });
}

function normalizeAmountInput(input) {
    const maxDigits = Number.parseInt(
        input.dataset.maxDigits ?? '10',
        10,
    );

    const digits = input.value
        .replace(/\D/g, '')
        .slice(0, maxDigits);

    input.value = digits === ''
        ? ''
        : Number(digits).toLocaleString('en-US');
}

function updateExpenseControls(form) {
    const list = form.querySelector(
        '[data-expense-list]',
    );

    const addButton = form.querySelector(
        '[data-add-expense]',
    );

    const limitMessage = form.querySelector(
        '[data-expense-limit]',
    );

    const countMessage = form.querySelector(
        '[data-expense-count]',
    );

    const cards = [
        ...list.querySelectorAll('[data-expense-card]'),
    ];

    cards.forEach((card, index) => {
        updateCardIndex(card, index);

        const removeButton = card.querySelector(
            '[data-remove-expense]',
        );

        if (removeButton) {
            removeButton.hidden = cards.length === 1;
        }
    });

    const reachedLimit = cards.length >= MAX_EXPENSES;

    if (addButton) {
        addButton.hidden = reachedLimit;
    }

    if (limitMessage) {
        limitMessage.hidden = !reachedLimit;
    }

    if (countMessage) {
        countMessage.textContent =
            `${cards.length} expense form${
                cards.length === 1 ? '' : 's'
            } displayed.`;
    }
}

function initExpenseForm() {
    const form = document.querySelector(
        '[data-expense-form]',
    );

    if (!form) {
        return;
    }

    const list = form.querySelector(
        '[data-expense-list]',
    );

    const addButton = form.querySelector(
        '[data-add-expense]',
    );

    const defaultDate = form.dataset.defaultDate ?? '';

    if (!list || !addButton) {
        return;
    }

    form.querySelectorAll(
        '[data-expense-amount]',
    ).forEach((input) => {
        normalizeAmountInput(input);
    });

    form.querySelectorAll(
        '[data-expense-field="recorded_time"]',
    ).forEach((input) => {
        if (input.value === '') {
            input.value = getCurrentTime();
        }
    });

    updateExpenseControls(form);

    addButton.addEventListener('click', () => {
        const cards = list.querySelectorAll(
            '[data-expense-card]',
        );

        if (cards.length >= MAX_EXPENSES) {
            return;
        }

        const sourceCard = cards[0];

        if (!sourceCard) {
            return;
        }

        const newCard = sourceCard.cloneNode(true);

        removeValidationState(newCard);
        resetCardValues(newCard, defaultDate);

        list.append(newCard);

        updateExpenseControls(form);

        newCard.querySelector(
            '[data-expense-field]',
        )?.focus();
    });

    list.addEventListener('click', (event) => {
        const removeButton = event.target.closest(
            '[data-remove-expense]',
        );

        if (!removeButton) {
            return;
        }

        const cards = list.querySelectorAll(
            '[data-expense-card]',
        );

        if (cards.length <= 1) {
            return;
        }

        removeButton
            .closest('[data-expense-card]')
            ?.remove();

        updateExpenseControls(form);
    });

    form.addEventListener('input', (event) => {
        const amountInput = event.target.closest(
            '[data-expense-amount]',
        );

        if (amountInput) {
            normalizeAmountInput(amountInput);
        }
    });

    form.addEventListener('submit', () => {
        form.querySelectorAll(
            '[data-expense-amount]',
        ).forEach((input) => {
            input.value = input.value.replace(/,/g, '');
        });
    });
}

export function initDashboardSidebar() {
    initSidebarToggle();
    initExpenseForm();
}

const sidebarScrollArea = document.querySelector(
    '[data-dashboard-sidebar-scroll]',
);

const sidebarScrollButtons = document.querySelectorAll(
    '[data-dashboard-sidebar-scroll-button]',
);

sidebarScrollButtons.forEach((button) => {
    button.addEventListener('click', () => {
        if (!sidebarScrollArea) {
            return;
        }

        const direction = Number(
            button.dataset.scrollDirection,
        );

        sidebarScrollArea.scrollBy({
            top: direction * 160,
            behavior: 'smooth',
        });
    });
});