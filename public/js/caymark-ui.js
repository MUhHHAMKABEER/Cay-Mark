/**
 * CayMark global UI — Section 1: Feedback & Notifications
 * CaymarkUI.showSuccess / showError / confirm / skeleton / progress
 */
(function (global) {
    'use strict';

    var DISMISS_MS = 3500;

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    /* ─── Toast ─── */
    function getToastHost() {
        var host = document.getElementById('caymark-toast-host');
        if (!host) {
            host = document.createElement('div');
            host.id = 'caymark-toast-host';
            host.setAttribute('aria-live', 'polite');
            host.setAttribute('aria-relevant', 'additions');
            document.body.appendChild(host);
        }
        return host;
    }

    function iconSvg(type) {
        if (type === 'success') {
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>';
        }
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4m0 4h.01M12 3a9 9 0 100 18 9 9 0 000-18z"/></svg>';
    }

    function supportsToastSwipe() {
        if (global.matchMedia) {
            return global.matchMedia('(max-width: 768px), (pointer: coarse)').matches;
        }
        return 'ontouchstart' in global;
    }

    function bindToastSwipe(toast, dismiss) {
        var startX = 0;
        var startY = 0;
        var currentX = 0;
        var dragging = false;
        var axis = null;

        toast.addEventListener('touchstart', function (e) {
            if (!e.touches || !e.touches[0]) return;
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            currentX = startX;
            dragging = true;
            axis = null;
            toast.classList.add('is-swiping');
        }, { passive: true });

        toast.addEventListener('touchmove', function (e) {
            if (!dragging || !e.touches || !e.touches[0]) return;
            var dx = e.touches[0].clientX - startX;
            var dy = e.touches[0].clientY - startY;
            if (!axis) {
                if (Math.abs(dx) < 8 && Math.abs(dy) < 8) return;
                axis = Math.abs(dx) >= Math.abs(dy) ? 'x' : 'y';
            }
            if (axis !== 'x') return;
            currentX = e.touches[0].clientX;
            dx = currentX - startX;
            toast.style.transform = 'translateX(' + dx + 'px)';
            toast.style.opacity = String(Math.max(0.3, 1 - Math.abs(dx) / 180));
            if (e.cancelable) e.preventDefault();
        }, { passive: false });

        toast.addEventListener('touchend', function () {
            if (!dragging) return;
            dragging = false;
            var dx = currentX - startX;
            toast.classList.remove('is-swiping');
            toast.style.transform = '';
            toast.style.opacity = '';
            if (axis === 'x' && Math.abs(dx) > 80) {
                dismiss();
            }
            axis = null;
        });

        toast.addEventListener('touchcancel', function () {
            dragging = false;
            axis = null;
            toast.classList.remove('is-swiping');
            toast.style.transform = '';
            toast.style.opacity = '';
        });
    }

    function showToast(type, title, subtitle) {
        title = title != null ? String(title) : '';
        subtitle = subtitle != null ? String(subtitle) : '';

        var host = getToastHost();
        var toast = document.createElement('div');
        toast.className = 'caymark-toast caymark-toast--' + type;
        toast.setAttribute('role', 'alert');

        toast.innerHTML =
            '<div class="caymark-toast__icon" aria-hidden="true">' + iconSvg(type) + '</div>' +
            '<div class="caymark-toast__body">' +
                '<p class="caymark-toast__title">' + escapeHtml(title) + '</p>' +
                '<p class="caymark-toast__subtitle">' + escapeHtml(subtitle) + '</p>' +
            '</div>' +
            '<button type="button" class="caymark-toast__close" aria-label="Dismiss">' +
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>' +
            '</button>';

        host.appendChild(toast);

        var dismissed = false;
        function dismiss() {
            if (dismissed) return;
            dismissed = true;
            toast.classList.add('is-leaving');
            toast.addEventListener('animationend', function () {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, { once: true });
        }

        var timer = global.setTimeout(dismiss, DISMISS_MS);
        var closeBtn = toast.querySelector('.caymark-toast__close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                global.clearTimeout(timer);
                dismiss();
            });
        }

        if (supportsToastSwipe()) {
            bindToastSwipe(toast, function () {
                global.clearTimeout(timer);
                dismiss();
            });
        }

        return toast;
    }

    /* ─── Confirm dialog ─── */
    var confirmResolve = null;

    function getConfirmOverlay() {
        return document.getElementById('cm-confirm-overlay');
    }

    function confirm(options) {
        options = options || {};
        var overlay = getConfirmOverlay();
        if (!overlay) {
            return Promise.resolve(global.confirm(options.title || 'Are you sure?'));
        }

        var titleEl = overlay.querySelector('[data-cm-confirm-title]');
        var descEl = overlay.querySelector('[data-cm-confirm-desc]');
        var confirmBtn = overlay.querySelector('[data-cm-confirm-ok]');
        var cancelBtn = overlay.querySelector('[data-cm-confirm-cancel]');

        if (titleEl) titleEl.textContent = options.title || 'Confirm';
        if (descEl) descEl.textContent = options.description || options.message || '';
        if (confirmBtn) {
            confirmBtn.textContent = options.confirmText || options.confirmLabel || 'Confirm';
            confirmBtn.className = 'cm-confirm-dialog__btn ' + (options.danger ? 'cm-confirm-dialog__btn--danger' : 'cm-confirm-dialog__btn--confirm');
        }
        if (cancelBtn) cancelBtn.textContent = options.cancelText || options.cancelLabel || 'Cancel';

        overlay.classList.add('is-open');
        overlay.setAttribute('aria-hidden', 'false');

        return new Promise(function (resolve) {
            confirmResolve = resolve;
        });
    }

    function closeConfirm(result) {
        var overlay = getConfirmOverlay();
        if (overlay) {
            overlay.classList.remove('is-open');
            overlay.setAttribute('aria-hidden', 'true');
        }
        if (confirmResolve) {
            var fn = confirmResolve;
            confirmResolve = null;
            fn(result);
        }
    }

    function initConfirmDialog() {
        var overlay = getConfirmOverlay();
        if (!overlay || overlay.dataset.cmBound === '1') return;
        overlay.dataset.cmBound = '1';

        overlay.querySelector('[data-cm-confirm-ok]')?.addEventListener('click', function () {
            closeConfirm(true);
        });
        overlay.querySelector('[data-cm-confirm-cancel]')?.addEventListener('click', function () {
            closeConfirm(false);
        });
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) closeConfirm(false);
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && overlay.classList.contains('is-open')) {
                closeConfirm(false);
            }
        });
    }

    /* ─── Skeleton loading ─── */
    function skeletonHtml(variant, count) {
        count = count || 3;
        var items = '';
        var i;

        if (variant === 'table') {
            for (i = 0; i < count; i++) {
                items += '<div class="cm-skeleton-table-row">' +
                    '<span class="cm-skeleton cm-skeleton-block" style="height:1rem"></span>' +
                    '<span class="cm-skeleton cm-skeleton-block" style="height:1rem"></span>' +
                    '<span class="cm-skeleton cm-skeleton-block" style="height:1rem"></span>' +
                    '<span class="cm-skeleton cm-skeleton-block" style="height:1rem"></span>' +
                    '</div>';
            }
            return '<div class="cm-skeleton-table">' + items + '</div>';
        }

        if (variant === 'profile') {
            return '<div class="cm-skeleton-profile">' +
                '<div class="cm-skeleton cm-skeleton-profile__avatar"></div>' +
                '<div class="cm-skeleton-profile__lines">' +
                '<span class="cm-skeleton cm-skeleton-block" style="height:1.25rem;width:60%"></span>' +
                '<span class="cm-skeleton cm-skeleton-block" style="height:1rem;width:80%"></span>' +
                '<span class="cm-skeleton cm-skeleton-block" style="height:1rem;width:45%"></span>' +
                '</div></div>';
        }

        var rowClass = variant === 'auction-row' ? ' cm-skeleton-auction-card--row' : '';
        var gridClass = variant === 'auction-grid' ? ' cm-skeleton-grid--cards' : '';

        for (i = 0; i < count; i++) {
            items += '<div class="cm-skeleton-auction-card' + rowClass + '">' +
                '<div class="cm-skeleton cm-skeleton-auction-card__media"></div>' +
                '<div class="cm-skeleton-auction-card__body">' +
                '<span class="cm-skeleton cm-skeleton-block" style="height:1.25rem;width:75%"></span>' +
                '<span class="cm-skeleton cm-skeleton-block" style="height:1rem;width:50%"></span>' +
                '<span class="cm-skeleton cm-skeleton-block" style="height:1rem;width:35%"></span>' +
                '<span class="cm-skeleton cm-skeleton-block" style="height:2rem;width:40%;margin-top:0.5rem"></span>' +
                '</div></div>';
        }

        return '<div class="cm-skeleton-grid' + gridClass + '">' + items + '</div>';
    }

    function setLoading(host, isLoading, options) {
        if (!host) return;
        options = options || {};
        var variant = options.variant || 'auction-row';
        var count = options.count || 3;

        if (typeof host === 'string') {
            host = document.querySelector(host);
        }
        if (!host) return;

        if (!host.classList.contains('cm-skeleton-host')) {
            host.classList.add('cm-skeleton-host');
        }

        var skel = host.querySelector('[data-cm-skeleton]');
        if (!skel) {
            skel = document.createElement('div');
            skel.setAttribute('data-cm-skeleton', '');
            skel.innerHTML = skeletonHtml(variant, count);
            host.insertBefore(skel, host.firstChild);
        } else if (isLoading) {
            skel.innerHTML = skeletonHtml(variant, count);
        }

        if (isLoading) {
            host.setAttribute('data-cm-loading', '1');
        } else {
            host.removeAttribute('data-cm-loading');
        }
    }

    /**
     * Fetch wrapper: shows skeleton in host until promise settles.
     */
    function fetchWithSkeleton(host, fetchPromise, options) {
        setLoading(host, true, options);
        return Promise.resolve(fetchPromise).finally(function () {
            setLoading(host, false, options);
        });
    }

    /* ─── Progress steps (DOM update helper) ─── */
    function updateProgressSteps(root, current, total) {
        if (typeof root === 'string') root = document.querySelector(root);
        if (!root) return;

        current = Math.max(1, Math.min(current, total));
        var pct = total > 0 ? Math.round((current / total) * 100) : 0;

        var fill = root.querySelector('[data-cm-progress-fill]');
        var meta = root.querySelector('[data-cm-progress-meta]');
        if (fill) fill.style.width = pct + '%';
        if (meta) meta.textContent = 'Step ' + current + ' of ' + total;

        root.querySelectorAll('[data-cm-progress-label]').forEach(function (el, idx) {
            var step = idx + 1;
            el.classList.remove('is-active', 'is-done');
            if (step < current) el.classList.add('is-done');
            else if (step === current) el.classList.add('is-active');
        });
    }

    /* ─── Public API ─── */
    var CaymarkUI = {
        showSuccess: function (title, subtitle) {
            return showToast('success', title, subtitle || '');
        },
        showError: function (title, subtitle) {
            return showToast('error', title, subtitle || '');
        },
        showToast: showToast,
        confirm: confirm,
        skeleton: {
            show: function (host, options) { setLoading(host, true, options); },
            hide: function (host) { setLoading(host, false); },
            html: skeletonHtml,
            fetch: fetchWithSkeleton,
        },
        setLoading: setLoading,
        updateProgress: updateProgressSteps,
    };

    global.CaymarkUI = CaymarkUI;
    global.showSuccess = CaymarkUI.showSuccess;
    global.showError = CaymarkUI.showError;

    document.addEventListener('DOMContentLoaded', function () {
        initConfirmDialog();

        if (global.CaymarkUIFlash) {
            if (CaymarkUIFlash.success) CaymarkUI.showSuccess(CaymarkUIFlash.success, CaymarkUIFlash.successSubtitle || '');
            if (CaymarkUIFlash.error) CaymarkUI.showError(CaymarkUIFlash.error, CaymarkUIFlash.errorSubtitle || '');
        }
    });
})(typeof window !== 'undefined' ? window : this);
