/**
 * CayMark UI kit — Section 3: Navigation
 * Back-to-top button + sticky header scroll shadow
 */
(function () {
    'use strict';

    var BACK_TO_TOP_THRESHOLD = 300;
    var HEADER_SCROLL_THRESHOLD = 10;

    function initBackToTop() {
        var btn = document.getElementById('cm-back-to-top');
        if (!btn) {
            return;
        }

        function toggleVisibility() {
            if (window.scrollY > BACK_TO_TOP_THRESHOLD) {
                btn.classList.add('cm-back-to-top--visible');
            } else {
                btn.classList.remove('cm-back-to-top--visible');
            }
        }

        window.addEventListener('scroll', toggleVisibility, { passive: true });
        toggleVisibility();

        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    function initHeaderScrollShadow() {
        var header = document.querySelector('.cm-site-header');
        if (!header) {
            return;
        }

        function toggleScrolled() {
            if (window.scrollY > HEADER_SCROLL_THRESHOLD) {
                header.classList.add('cm-header--scrolled');
            } else {
                header.classList.remove('cm-header--scrolled');
            }
        }

        window.addEventListener('scroll', toggleScrolled, { passive: true });
        toggleScrolled();
    }

    function init() {
        initBackToTop();
        initHeaderScrollShadow();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
