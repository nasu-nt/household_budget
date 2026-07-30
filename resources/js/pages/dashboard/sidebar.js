const MAX_EXPENSE_COUNT = 5;

export function initDashboardSidebar() {
    initSidebarToggle();
    initExpenseForm();
}

function initSidebarToggle() {
    const sidebar = document.getElementById('sidebar');
    const button = document.getElementById('sidebarToggle');

    if (!sidebar || !button) {
        return;
    }

    // a11y: 初期値と切り替えを管理
    const setAccessibilityState = (isClosed) => {
        button.setAttribute('aria-expanded', String(!isClosed));

        button.setAttribute(
            'aria-label',
            isClosed
                ? 'Open add expense form'
                : 'Close add expense form'
        );
    };

    // 初期状態反映
    setAccessibilityState(
        sidebar.classList.contains('is-closed')
    );

    button.addEventListener('click', () => {
        const isClosed = sidebar.classList.toggle('is-closed');

        button.classList.toggle('is-closed', isClosed);

        // 開閉に合わせて更新
        setAccessibilityState(isClosed);
    });
}

function initExpenseForm() {
    const form = document.querySelector('[data-expense-form]');

    if (!form || form.dataset.initialized === 'true') {
        return;
    }

    const list = form.querySelector('[data-expense-list]');
    const addButton = form.querySelector('[data-add-expense]');
    const limitMessage = form.querySelector('[data-expense-limit]');
    const countMessage = form.querySelector('[data-expense-count]');

    if (!list || !addButton || !limitMessage || !countMessage) {
        return;
    }

    form.dataset.initialized = 'true';

    const getCards = () => {
        return Array.from(
            list.querySelectorAll('[data-expense-card]')
        );
    };

    const updateCardIndexes = () => {
        getCards().forEach((card, index) => {
            const legend = card.querySelector(
                '[data-expense-legend]'
            );

            if (legend) {
                legend.textContent = `Expense ${index + 1}`;
            }

            const fields = card.querySelectorAll(
                '[data-expense-field]'
            );

            fields.forEach((field) => {
                const fieldName = field.dataset.expenseField;

                if (!fieldName) {
                    return;
                }

                const fieldId = `expense_${fieldName}_${index}`;

                field.id = fieldId;
                field.name = `expenses[${index}][${fieldName}]`;

                const label = card.querySelector(
                    `[data-expense-label="${fieldName}"]`
                );

                if (label) {
                    label.setAttribute('for', fieldId);
                }

                const error = card.querySelector(
                    `[data-expense-error="${fieldName}"]`
                );

                if (error) {
                    const errorId = `${fieldId}_error`;

                    error.id = errorId;

                    field.setAttribute(
                        'aria-describedby',
                        errorId
                    );

                    field.setAttribute(
                        'aria-invalid',
                        'true'
                    );
                }
            });
        });
    };

    const updateControls = () => {
        const cards = getCards();
        const count = cards.length;
        const hasOnlyOneCard = count === 1;
        const hasReachedLimit = count >= MAX_EXPENSE_COUNT;

        cards.forEach((card) => {
            const removeButton = card.querySelector(
                '[data-remove-expense]'
            );

            if (removeButton) {
                removeButton.hidden = hasOnlyOneCard;
            }
        });

        addButton.hidden = hasReachedLimit;
        limitMessage.hidden = !hasReachedLimit;

        countMessage.textContent =
            `${count} of ${MAX_EXPENSE_COUNT} expense forms`;
    };

    const clearClonedCard = (card, inheritedDate) => {
        card.querySelectorAll('[data-expense-error]')
            .forEach((error) => error.remove());

        card.querySelectorAll('[data-expense-field]')
            .forEach((field) => {
                const fieldName = field.dataset.expenseField;

                field.classList.remove('is-invalid');
                field.removeAttribute('aria-invalid');
                field.removeAttribute('aria-describedby');

                switch (fieldName) {
                    case 'expense_date':
                        field.value = inheritedDate;
                        break;

                    case 'category_id':
                        field.selectedIndex = 0;
                        break;

                    case 'amount':
                    case 'memo':
                        field.value = '';
                        break;

                    default:
                        break;
                }
            });
    };

    addButton.addEventListener('click', () => {
        const cards = getCards();

        if (cards.length >= MAX_EXPENSE_COUNT) {
            return;
        }

        const sourceCard = cards.at(-1);

        if (!sourceCard) {
            return;
        }

        const sourceDate = sourceCard.querySelector(
            '[data-expense-field="expense_date"]'
        )?.value ?? '';

        const newCard = sourceCard.cloneNode(true);

        clearClonedCard(newCard, sourceDate);

        list.append(newCard);

        updateCardIndexes();
        updateControls();

        newCard.querySelector(
            '[data-expense-field="category_id"]'
        )?.focus();
    });

    list.addEventListener('click', (event) => {
        if (!(event.target instanceof Element)) {
            return;
        }

        const removeButton = event.target.closest(
            '[data-remove-expense]'
        );

        if (!removeButton) {
            return;
        }

        const cards = getCards();

        if (cards.length <= 1) {
            return;
        }

        const card = removeButton.closest(
            '[data-expense-card]'
        );

        if (!card) {
            return;
        }

        card.remove();

        updateCardIndexes();
        updateControls();
    });

    list.addEventListener('input', (event) => {
        if (!(event.target instanceof HTMLInputElement)) {
            return;
        }

        if (!event.target.matches('[data-expense-amount]')) {
            return;
        }

        event.target.value = event.target.value
            .replace(/[^\d]/g, '')
            .slice(0, 10);
    });

    form.addEventListener('submit', () => {
        form.querySelectorAll('[data-expense-amount]')
            .forEach((input) => {
                input.value = input.value.replaceAll(',', '');
            });
    });

    updateCardIndexes();
    updateControls();
}