import './bootstrap';

import Alpine from 'alpinejs';

/**
 * x-soft-rule="uppercase|digits|digits-slash"
 *
 * Soft formatting rule for fill-the-blank inputs.
 *   - Normal typing: the rule auto-formats the character (e.g. lowercase -> uppercase).
 *   - Once the user presses Backspace/Delete on the field, the rule is marked
 *     "overridden" for that field, and subsequent typing is preserved as-is.
 *   - Clearing the field and blurring it resets the override.
 *
 * Implementation note: we intercept the `beforeinput` event, prevent the
 * browser's default insertion, and manually insert the transformed text.
 * This avoids all listener-ordering pitfalls with `x-model` (whose input
 * listener runs in the bubble phase): by the time any `input` event fires,
 * the DOM `value` is already the final value, so x-model reads the right
 * thing. After inserting we dispatch a synthetic `input` event to keep
 * x-model and any `@input` handlers in sync.
 */
Alpine.directive('soft-rule', (el, { expression }) => {
    const rule = expression;

    const transformChar = (ch) => {
        if (rule === 'uppercase') return ch.toUpperCase();
        if (rule === 'digits') return ch.replace(/\D/g, '');
        if (rule === 'digits-slash') return ch.replace(/[^0-9\/]/g, '');
        return ch;
    };

    const isOverridden = () => el.dataset.softRuleOverridden === '1';
    const setOverridden = () => { el.dataset.softRuleOverridden = '1'; };
    const clearOverridden = () => { delete el.dataset.softRuleOverridden; };

    // Replace the current selection range with `insert` and leave the caret
    // immediately after the inserted text. Dispatch a synthetic `input` event
    // so x-model / @input sync with the new value.
    const replaceSelection = (insert) => {
        const start = el.selectionStart ?? el.value.length;
        const end = el.selectionEnd ?? el.value.length;
        const before = el.value.slice(0, start);
        const after = el.value.slice(end);
        el.value = before + insert + after;
        const pos = start + insert.length;
        try { el.setSelectionRange(pos, pos); } catch (_) {}
        el.dispatchEvent(new Event('input', { bubbles: true }));
    };

    el.addEventListener('beforeinput', (e) => {
        // Any explicit deletion flips the field into override mode.
        if (typeof e.inputType === 'string' && e.inputType.startsWith('delete')) {
            setOverridden();
            return; // let the browser handle the delete normally
        }

        // Only intercept plain text insertions (typing / paste / drop).
        // Leave other inputTypes (composition, history, formatting) alone.
        const handled = [
            'insertText',
            'insertFromPaste',
            'insertFromDrop',
            'insertReplacementText',
        ];
        if (!handled.includes(e.inputType)) return;

        // If the field is already in override mode, let the browser insert
        // exactly what the user typed, untouched.
        if (isOverridden()) return;

        const raw = e.data ?? (e.dataTransfer && e.dataTransfer.getData('text')) ?? '';
        if (raw === '') return;

        const transformed = transformChar(raw);
        // If the rule would produce the same characters the browser is about
        // to insert, let the default proceed so the native caret handling is
        // preserved exactly.
        if (transformed === raw) return;

        // Otherwise, take over: prevent the default insertion and perform
        // the transformed insertion ourselves.
        e.preventDefault();
        if (transformed.length === 0) {
            // e.g. user typed a letter into a digits-only field: insert
            // nothing and leave the selection collapsed at the start.
            replaceSelection('');
        } else {
            replaceSelection(transformed);
        }
    });

    // Keyboard-based deletes don't always surface via beforeinput on all
    // browsers (e.g. older Safari). Catch them here as a safety net.
    el.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' || e.key === 'Delete') {
            setOverridden();
        }
    });

    // Reset override when the field ends up empty and loses focus, so the
    // next round of typing gets auto-formatting again.
    el.addEventListener('blur', () => {
        if (el.value === '') clearOverridden();
    });
});

window.Alpine = Alpine;

Alpine.start();
