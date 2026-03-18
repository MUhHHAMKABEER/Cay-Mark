/**
 * Admin table filter – client-side filtering for admin list tables.
 * Use: form.js-admin-filter-form + data-admin-filter-target="#tbody-id"
 * Rows: data-filter-{inputName}="value" (e.g. data-filter-search, data-filter-role, data-filter-status).
 * Search fields: match substring (case-insensitive). Selects: exact match (empty = show all).
 */
(function() {
    function initAdminTableFilters() {
        document.querySelectorAll('.js-admin-filter-form').forEach(function(form) {
            var targetId = form.getAttribute('data-admin-filter-target');
            if (!targetId) return;
            var tbody = document.querySelector(targetId);
            if (!tbody) return;

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                runFilter(form, tbody);
                return false;
            });

            // Optional: Filter on input/change for live filtering
            var filterBtn = form.querySelector('button[type="submit"]');
            var clearLink = form.querySelector('a[href][data-admin-filter-clear]');
            if (clearLink) {
                clearLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    form.querySelectorAll('input[type="text"], input[type="date"]').forEach(function(inp) { inp.value = ''; });
                    form.querySelectorAll('select').forEach(function(sel) { sel.selectedIndex = 0; });
                    runFilter(form, tbody);
                });
            }
        });
    }

    function runFilter(form, tbody) {
        var rows = tbody.querySelectorAll('tr');
        var emptyRow = tbody.querySelector('tr.js-admin-empty-row');
        var dataRows = [];
        rows.forEach(function(tr) {
            if (tr.classList.contains('js-admin-empty-row')) return;
            dataRows.push(tr);
        });

        var visible = 0;
        dataRows.forEach(function(tr) {
            var show = true;
            form.querySelectorAll('input[name], select[name]').forEach(function(input) {
                var name = (input.getAttribute('name') || '').replace(/\[\]$/, '');
                if (!name) return;
                var key = 'filter-' + name;
                var val = (input.value || '').trim();
                var rowVal = (tr.getAttribute('data-' + key) || '').trim().toLowerCase();
                var type = (input.type || '').toLowerCase();

                if (type === 'text' || type === 'search') {
                    if (val && rowVal.indexOf(val.toLowerCase()) === -1) show = false;
                } else if (input.tagName === 'SELECT') {
                    if (val && rowVal !== val.toLowerCase()) show = false;
                } else if (type === 'date') {
                    var rowDate = tr.getAttribute('data-filter-date') || tr.getAttribute('data-' + key);
                    if (!rowDate) return;
                    if (name === 'date_from' && val && rowDate < val) show = false;
                    if (name === 'date_to' && val && rowDate > val) show = false;
                }
            });
            tr.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        if (emptyRow) {
            emptyRow.style.display = visible === 0 ? '' : 'none';
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdminTableFilters);
    } else {
        initAdminTableFilters();
    }
})();
