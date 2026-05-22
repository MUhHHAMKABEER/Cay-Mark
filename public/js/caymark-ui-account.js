/**
 * CayMark UI kit — Section 5: User Account Area
 * Notification bell dropdown (toggle, outside click, escape)
 */
(function () {
    'use strict';

    function closeBell(bell) {
        var panel = bell.querySelector('.cm-notification-bell__panel');
        var trigger = bell.querySelector('.cm-notification-bell__trigger');
        if (!panel || !trigger) {
            return;
        }
        panel.hidden = true;
        panel.setAttribute('aria-hidden', 'true');
        trigger.setAttribute('aria-expanded', 'false');
        bell.classList.remove('is-open');
    }

    function openBell(bell) {
        document.querySelectorAll('[data-cm-notification-bell].is-open').forEach(function (other) {
            if (other !== bell) {
                closeBell(other);
            }
        });

        var panel = bell.querySelector('.cm-notification-bell__panel');
        var trigger = bell.querySelector('.cm-notification-bell__trigger');
        if (!panel || !trigger) {
            return;
        }
        panel.hidden = false;
        panel.setAttribute('aria-hidden', 'false');
        trigger.setAttribute('aria-expanded', 'true');
        bell.classList.add('is-open');
    }

    function toggleBell(bell) {
        if (bell.classList.contains('is-open')) {
            closeBell(bell);
        } else {
            openBell(bell);
        }
    }

    function markRead(bell, notificationId) {
        var template = bell.getAttribute('data-cm-mark-read-url');
        if (!template || !notificationId) {
            return;
        }

        var url = template.replace('__ID__', encodeURIComponent(notificationId));
        var token = document.querySelector('meta[name="csrf-token"]');
        var headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        };

        if (token) {
            headers['X-CSRF-TOKEN'] = token.getAttribute('content');
        }

        fetch(url, {
            method: 'POST',
            headers: headers,
            credentials: 'same-origin',
        }).catch(function () {
            /* non-blocking */
        });
    }

    function initBell(bell) {
        var trigger = bell.querySelector('.cm-notification-bell__trigger');
        if (!trigger) {
            return;
        }

        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            toggleBell(bell);
        });

        bell.querySelectorAll('[data-cm-mark-read-on-click]').forEach(function (link) {
            link.addEventListener('click', function () {
                var id = link.getAttribute('data-notification-id');
                if (id) {
                    markRead(bell, id);
                }
            });
        });
    }

    function init() {
        document.querySelectorAll('[data-cm-notification-bell]').forEach(initBell);

        document.addEventListener('click', function (e) {
            if (e.target.closest('[data-cm-notification-bell]')) {
                return;
            }
            document.querySelectorAll('[data-cm-notification-bell].is-open').forEach(closeBell);
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('[data-cm-notification-bell].is-open').forEach(closeBell);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
