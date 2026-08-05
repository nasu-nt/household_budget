/**
 * 現在時刻をinput[type="time"]用の
 * HH:MM形式で返す。
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
 * バリデーション表示を削除する。
 */
function clearValidationState(row) {
    row.querySelectorAll(
        '.daily-insights__record-error',
    ).forEach((error) => {
        error.remove();
    });

    row.querySelectorAll(
        '.is-invalid',
    ).forEach((field) => {
        field.classList.remove('is-invalid');
        field.removeAttribute('aria-invalid');
        field.removeAttribute('aria-describedby');
    });
}

/**
 * 編集行を閉じて通常行へ戻す。
 */
function closeEditRow(editRow) {
    const displayRow =
        editRow.previousElementSibling;

    if (
        !displayRow
        || !displayRow.matches(
            '[data-record-display-row]',
        )
    ) {
        return;
    }

    editRow.querySelector('form')?.reset();
    clearValidationState(editRow);

    editRow.hidden = true;
    displayRow.hidden = false;
}

/**
 * 開いている編集行をすべて閉じる。
 */
function closeAllEditRows(recordsTable) {
    recordsTable.querySelectorAll(
        '[data-record-edit-row]:not([hidden])',
    ).forEach((editRow) => {
        closeEditRow(editRow);
    });
}

/**
 * 新規追加行を閉じる。
 */
function closeCreateRow(recordsSection) {
    const recordsTable = recordsSection.querySelector(
        '[data-daily-records]',
    );

    const createRow = recordsSection.querySelector(
        '[data-record-create-row]',
    );

    const addArea = recordsSection.querySelector(
        '[data-record-add-row]',
    );

    const emptyMessage = recordsSection.querySelector(
        '[data-records-empty]',
    );

    if (
        !recordsTable
        || !createRow
        || !addArea
    ) {
        return;
    }

    createRow.querySelector('form')?.reset();
    clearValidationState(createRow);

    createRow.hidden = true;
    addArea.hidden = false;

    /*
     * 通常のRecordが1件もない場合は、
     * テーブルを閉じて0件メッセージを表示する。
     */
    const hasRecords = recordsTable.querySelector(
        '[data-record-display-row]',
    ) !== null;

    if (!hasRecords) {
        recordsTable.hidden = true;

        if (emptyMessage) {
            emptyMessage.hidden = false;
        }
    }
}

/**
 * 新規追加行を開く。
 */
function openCreateRow(
    recordsTable,
    recordsSection,
) {
    const createRow = recordsSection.querySelector(
        '[data-record-create-row]',
    );

    const addArea = recordsSection.querySelector(
        '[data-record-add-row]',
    );

    const emptyMessage = recordsSection.querySelector(
        '[data-records-empty]',
    );

    if (!createRow || !addArea) {
        return;
    }

    closeAllEditRows(recordsTable);

    /*
     * 0件時に非表示だったテーブルを表示する。
     */
    recordsTable.hidden = false;

    if (emptyMessage) {
        emptyMessage.hidden = true;
    }

    addArea.hidden = true;
    createRow.hidden = false;

    const timeInput = createRow.querySelector(
        '[data-record-create-time]',
    );

    if (
        timeInput
        && timeInput.value === ''
    ) {
        timeInput.value = getCurrentTime();
    }

    timeInput?.focus();
}

/**
 * Daily InsightsのRecords機能を初期化する。
 */
export function initDailyInsightsRecords() {
    const recordsSection = document.querySelector(
        '[data-daily-records-section]',
    );

    if (!recordsSection) {
        return;
    }

    const recordsTable = recordsSection.querySelector(
        '[data-daily-records]',
    );

    if (!recordsTable) {
        return;
    }

    /*
     * Add another recordはテーブルの外にあるため、
     * テーブルではなくRecordsセクション全体を監視する。
     */
    recordsSection.addEventListener(
        'click',
        (event) => {
            if (!(event.target instanceof Element)) {
                return;
            }

            /*
             * Edit
             */
            const editButton = event.target.closest(
                '[data-record-edit]',
            );

            if (editButton) {
                const displayRow = editButton.closest(
                    '[data-record-display-row]',
                );

                const editRow =
                    displayRow?.nextElementSibling;

                if (
                    !displayRow
                    || !editRow
                    || !editRow.matches(
                        '[data-record-edit-row]',
                    )
                ) {
                    return;
                }

                closeCreateRow(recordsSection);
                closeAllEditRows(recordsTable);

                displayRow.hidden = true;
                editRow.hidden = false;

                editRow.querySelector(
                    'input:not([type="hidden"]), select',
                )?.focus();

                return;
            }

            /*
             * 編集行のCancel
             */
            const editCancelButton =
                event.target.closest(
                    '[data-record-cancel]',
                );

            if (editCancelButton) {
                const editRow =
                    editCancelButton.closest(
                        '[data-record-edit-row]',
                    );

                if (editRow) {
                    closeEditRow(editRow);
                }

                return;
            }

            /*
             * Add another record
             */
            const addButton = event.target.closest(
                '[data-record-add]',
            );

            if (addButton) {
                openCreateRow(
                    recordsTable,
                    recordsSection,
                );

                return;
            }

            /*
             * 新規追加行のCancel
             */
            const createCancelButton =
                event.target.closest(
                    '[data-record-create-cancel]',
                );

            if (!createCancelButton) {
                return;
            }

            closeCreateRow(recordsSection);
        },
    );
}