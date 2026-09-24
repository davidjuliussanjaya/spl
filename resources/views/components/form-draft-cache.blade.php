@props(['clearOnSuccess' => false])

<script>
    (function () {
        const pendingKey = 'spl:draft:pending';
        const canUseStorage = function () {
            try {
                localStorage.setItem('__spl_draft_test__', '1');
                localStorage.removeItem('__spl_draft_test__');
                return true;
            } catch (error) {
                return false;
            }
        };

        if (!canUseStorage()) return;

        if (@json($clearOnSuccess)) {
            const completedDraft = sessionStorage.getItem(pendingKey);
            if (completedDraft) {
                localStorage.removeItem(completedDraft);
                sessionStorage.removeItem(pendingKey);
            }
        }

        const readDraft = function (key) {
            try {
                return JSON.parse(localStorage.getItem(key) || 'null');
            } catch (error) {
                localStorage.removeItem(key);
                return null;
            }
        };

        const collectValues = function (form) {
            const values = {};
            form.querySelectorAll('input, select, textarea').forEach(function (field) {
                if (!field.name || field.disabled || ['password', 'file', 'submit', 'button', 'reset'].includes(field.type)) return;
                if (field.type === 'hidden' && field.name !== 'kategori_urutan[]') return;
                if ((field.type === 'checkbox' || field.type === 'radio') && !field.checked) return;
                (values[field.name] ??= []).push(field.value);
            });
            return values;
        };

        const restoreValues = function (form, values) {
            const positions = {};
            form.querySelectorAll('input, select, textarea').forEach(function (field) {
                if (!field.name || ['password', 'file', 'submit', 'button', 'reset'].includes(field.type)) return;
                if (field.type === 'hidden' && field.name !== 'kategori_urutan[]') return;
                const savedValues = values[field.name];
                if (!savedValues) return;

                if (field.type === 'checkbox' || field.type === 'radio') {
                    field.checked = savedValues.includes(field.value);
                    return;
                }

                const index = positions[field.name] ?? 0;
                if (savedValues[index] !== undefined) field.value = savedValues[index];
                positions[field.name] = index + 1;

                if (field.tagName === 'SELECT' && window.jQuery && field.classList.contains('select2-hidden-accessible')) {
                    window.jQuery(field).trigger('change.select2');
                }
            });
        };

        const initializeDraft = function (form) {
            const key = form.dataset.draftKey;
            const draft = readDraft(key);

            if (draft?.values) {
                const savedOptionCount = draft.values['jawaban[]']?.length;
                const optionAddButton = form.querySelector('#addOption');
                if (savedOptionCount !== undefined && optionAddButton) {
                    while (form.querySelectorAll('[name="jawaban[]"]').length < savedOptionCount) {
                        optionAddButton.click();
                    }
                    while (form.querySelectorAll('[name="jawaban[]"]').length > savedOptionCount) {
                        form.querySelector('.option-item:last-child .removeOption')?.click();
                    }
                }

                restoreValues(form, draft.values);

                // Pemilih instrumen mengelola kartu kategori secara dinamis.
                // Tambahkan kategori sesuai draf, lalu pulihkan pilihan soalnya.
                (draft.values['kategori_urutan[]'] || []).forEach(function (categoryId) {
                    form.querySelector('[data-add-category="' + CSS.escape(categoryId) + '"]')?.click();
                });
                restoreValues(form, draft.values);
                form.dispatchEvent(new CustomEvent('spl:draft-restored'));
            }

            let saveTimer;
            const saveDraft = function () {
                localStorage.setItem(key, JSON.stringify({ values: collectValues(form), savedAt: Date.now() }));
            };
            const scheduleSave = function () {
                window.clearTimeout(saveTimer);
                saveTimer = window.setTimeout(saveDraft, 350);
            };
            form.addEventListener('input', scheduleSave);
            form.addEventListener('change', scheduleSave);
            form.addEventListener('click', scheduleSave);
            form.addEventListener('drop', scheduleSave);
            form.addEventListener('submit', function (event) {
                if (!event.defaultPrevented) {
                    saveDraft();
                    sessionStorage.setItem(pendingKey, key);
                }
            });
        };

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('form[data-draft-key]').forEach(initializeDraft);
        });
    })();
</script>
