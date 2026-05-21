<script>
(function () {
    function sanitize(el) {
        var max = el.maxLength > 0 ? el.maxLength : 999;
        var digits = String(el.value).replace(/\D/g, '');
        if (digits.length > max) digits = digits.slice(0, max);
        if (el.value !== digits) el.value = digits;
    }

    function bindDigitsOnly(el) {
        if (!el || el.dataset.digitsOnlyBound === '1') return;
        el.dataset.digitsOnlyBound = '1';

        el.addEventListener('input', function () { sanitize(this); });
        el.addEventListener('keydown', function (e) {
            if (e.ctrlKey || e.metaKey || e.altKey) return;
            var allowed = ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End'];
            if (allowed.indexOf(e.key) !== -1) return;
            if (e.key.length === 1 && !/\d/.test(e.key)) e.preventDefault();
        });
        el.addEventListener('paste', function (e) {
            e.preventDefault();
            var text = (e.clipboardData || window.clipboardData).getData('text') || '';
            var digits = text.replace(/\D/g, '');
            var start = this.selectionStart || 0;
            var end = this.selectionEnd || 0;
            var merged = (this.value.slice(0, start) + digits + this.value.slice(end)).replace(/\D/g, '');
            var max = this.maxLength > 0 ? this.maxLength : 999;
            this.value = merged.slice(0, max);
        });
    }

    function init() {
        document.querySelectorAll('.js-digits-only').forEach(bindDigitsOnly);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
