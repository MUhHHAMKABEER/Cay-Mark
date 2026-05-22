/**
 * CayMark global UI — Section 6: Mobile-specific
 * Pull-to-refresh, filter bottom sheet, auto-init hooks
 */
(function (global) {
    'use strict';

    var MOBILE_MQ = '(max-width: 768px)';
    var openSheetId = null;
    var sheetEscapeBound = false;

    function isMobileViewport() {
        return global.matchMedia && global.matchMedia(MOBILE_MQ).matches;
    }

    function lockBodyScroll(lock) {
        document.body.classList.toggle('cm-sheet-open', !!lock);
    }

    function getSheet(id) {
        if (!id) return null;
        return document.getElementById(id);
    }

    function bindSheetInteractions(sheet) {
        if (!sheet || sheet.dataset.cmSheetBound === '1') return;
        sheet.dataset.cmSheetBound = '1';

        sheet.querySelectorAll('[data-cm-sheet-close]').forEach(function (el) {
            el.addEventListener('click', function () {
                closeBottomSheet(sheet.id);
            });
        });

        var applyBtn = sheet.querySelector('[data-cm-sheet-apply]');
        if (applyBtn) {
            applyBtn.addEventListener('click', function () {
                var sheetId = applyBtn.getAttribute('data-cm-sheet-apply') || sheet.id;
                var onApply = sheet.dataset.cmSheetOnApply;
                if (onApply && typeof global[onApply] === 'function') {
                    global[onApply](sheetId);
                }
                document.dispatchEvent(new CustomEvent('cm:bottom-sheet-apply', {
                    detail: { id: sheetId },
                }));
                closeBottomSheet(sheetId);
            });
        }

        var handle = sheet.querySelector('[data-cm-sheet-handle]');
        if (handle) {
            bindSheetSwipe(handle, sheet);
        }
    }

    function bindSheetSwipe(handle, sheet) {
        var startY = 0;
        var currentY = 0;
        var dragging = false;
        var panel = sheet.querySelector('.cm-bottom-sheet__panel');

        handle.addEventListener('touchstart', function (e) {
            if (!e.touches || !e.touches[0]) return;
            startY = e.touches[0].clientY;
            currentY = startY;
            dragging = true;
        }, { passive: true });

        handle.addEventListener('touchmove', function (e) {
            if (!dragging || !e.touches || !e.touches[0] || !panel) return;
            currentY = e.touches[0].clientY;
            var dy = Math.max(0, currentY - startY);
            panel.style.transform = 'translateY(' + dy + 'px)';
        }, { passive: true });

        handle.addEventListener('touchend', function () {
            if (!dragging || !panel) return;
            dragging = false;
            var dy = currentY - startY;
            panel.style.transform = '';
            if (dy > 72) {
                closeBottomSheet(sheet.id);
            }
        });
    }

    function bindSheetEscape() {
        if (sheetEscapeBound) return;
        sheetEscapeBound = true;
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && openSheetId) {
                closeBottomSheet(openSheetId);
            }
        });
    }

    function openBottomSheet(id) {
        var sheet = getSheet(id);
        if (!sheet) return;
        bindSheetInteractions(sheet);
        bindSheetEscape();
        sheet.classList.add('cm-bottom-sheet--open', 'is-open');
        sheet.setAttribute('aria-hidden', 'false');
        openSheetId = id;
        lockBodyScroll(true);
        var panel = sheet.querySelector('.cm-bottom-sheet__panel');
        if (panel) panel.focus();
    }

    function closeBottomSheet(id) {
        var sheet = getSheet(id || openSheetId);
        if (!sheet) return;
        sheet.classList.remove('cm-bottom-sheet--open', 'is-open');
        sheet.setAttribute('aria-hidden', 'true');
        var panel = sheet.querySelector('.cm-bottom-sheet__panel');
        if (panel) panel.style.transform = '';
        if (openSheetId === sheet.id) {
            openSheetId = null;
            lockBodyScroll(false);
        }
    }

    function initBottomSheets() {
        document.querySelectorAll('.cm-bottom-sheet').forEach(bindSheetInteractions);
    }

    /* ─── Pull to refresh ─── */
    function initPullToRefresh(containerSelector, onRefresh) {
        var container = typeof containerSelector === 'string'
            ? document.querySelector(containerSelector)
            : containerSelector;
        if (!container || container.dataset.cmPullBound === '1') return;
        container.dataset.cmPullBound = '1';

        var indicator = document.createElement('div');
        indicator.className = 'cm-pull-refresh';
        indicator.setAttribute('aria-hidden', 'true');
        indicator.innerHTML = '<span class="cm-pull-refresh__spinner"></span><span class="cm-pull-refresh__label">Release to refresh</span>';
        container.insertBefore(indicator, container.firstChild);

        var startY = 0;
        var pulling = false;
        var refreshing = false;
        var pullDistance = 0;
        var threshold = 72;

        function atScrollTop() {
            var rect = container.getBoundingClientRect();
            var scrollParent = container;
            if (container.scrollTop <= 2) return true;
            var node = container.parentElement;
            while (node && node !== document.body) {
                if (node.scrollTop > 2) return false;
                node = node.parentElement;
            }
            return window.scrollY <= rect.top + 2;
        }

        function setPullState(distance, isRefreshing) {
            var clamped = Math.min(distance, threshold * 1.4);
            container.style.setProperty('--cm-pull-offset', clamped + 'px');
            indicator.classList.toggle('cm-pull-refresh--active', clamped > 8);
            indicator.classList.toggle('cm-pull-refresh--ready', clamped >= threshold && !isRefreshing);
            indicator.classList.toggle('cm-pull-refresh--loading', !!isRefreshing);
        }

        function resetPull() {
            pullDistance = 0;
            pulling = false;
            setPullState(0, false);
            container.classList.remove('cm-pull-refresh-host--pulling');
        }

        function runRefresh() {
            if (refreshing) return;
            refreshing = true;
            setPullState(threshold, true);
            container.classList.add('cm-pull-refresh-host--refreshing');

            var done = function () {
                refreshing = false;
                container.classList.remove('cm-pull-refresh-host--refreshing');
                resetPull();
            };

            var result;
            try {
                result = onRefresh ? onRefresh() : null;
            } catch (err) {
                console.error('Pull refresh error:', err);
                done();
                return;
            }

            if (result && typeof result.then === 'function') {
                result.then(done).catch(function () { done(); });
            } else {
                global.setTimeout(done, 600);
            }
        }

        container.addEventListener('touchstart', function (e) {
            if (!isMobileViewport() || refreshing || !e.touches || !e.touches[0]) return;
            if (!atScrollTop()) return;
            startY = e.touches[0].clientY;
            pulling = true;
        }, { passive: true });

        container.addEventListener('touchmove', function (e) {
            if (!pulling || refreshing || !e.touches || !e.touches[0]) return;
            var dy = e.touches[0].clientY - startY;
            if (dy <= 0) {
                resetPull();
                return;
            }
            if (!atScrollTop()) {
                resetPull();
                return;
            }
            pullDistance = dy;
            container.classList.add('cm-pull-refresh-host--pulling');
            setPullState(pullDistance, false);
            if (pullDistance > 10 && e.cancelable) {
                e.preventDefault();
            }
        }, { passive: false });

        container.addEventListener('touchend', function () {
            if (!pulling || refreshing) return;
            if (pullDistance >= threshold) {
                runRefresh();
            } else {
                resetPull();
            }
        });

        container.addEventListener('touchcancel', resetPull);
    }

    function initOpenTriggers() {
        document.querySelectorAll('[data-cm-open-sheet]').forEach(function (btn) {
            if (btn.dataset.cmSheetTriggerBound === '1') return;
            btn.dataset.cmSheetTriggerBound = '1';
            btn.addEventListener('click', function () {
                var id = btn.getAttribute('data-cm-open-sheet');
                if (id) openBottomSheet(id);
            });
        });
    }

    var mobileApi = {
        initPullToRefresh: initPullToRefresh,
        openBottomSheet: openBottomSheet,
        closeBottomSheet: closeBottomSheet,
        initBottomSheets: initBottomSheets,
        isMobileViewport: isMobileViewport,
    };

    global.CaymarkUI = global.CaymarkUI || {};
    global.CaymarkUI.mobile = mobileApi;

    document.addEventListener('DOMContentLoaded', function () {
        initBottomSheets();
        initOpenTriggers();

        document.querySelectorAll('[data-cm-pull-refresh]').forEach(function (el) {
            var fnName = el.getAttribute('data-cm-pull-on-refresh');
            var handler = fnName && typeof global[fnName] === 'function'
                ? global[fnName]
                : function () { global.location.reload(); };
            initPullToRefresh(el, handler);
        });
    });
})(typeof window !== 'undefined' ? window : this);
