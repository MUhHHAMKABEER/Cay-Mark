{{--
    Global Custom Select
    ─────────────────────────────────────────────────────────────────────
    Upgrades every <select class="form-input"> on the page to the branded
    CayMark dropdown. Functionality is identical to the original listing-
    submission version — only the z-index has been adjusted (1000) so the
    panel sits above modals and sticky headers without conflicting with
    higher-priority overlays.

    Included once in every layout. The data-cmInit guard prevents
    double-initialisation on pages that still carry the old include.
--}}

<style>
/* ─── Custom Select Wrapper ─────────────────────────────── */
.cm-sel {
    position: relative;
    width: 100%;
}

/* Trigger button — matches form-input style */
.cm-sel__trigger {
    width: 100%;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.575rem 0.85rem;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.875rem;
    color: #111827;
    cursor: pointer;
    text-align: left;
    transition: border-color 0.15s, box-shadow 0.15s;
    min-height: 42px;
    font-family: inherit;
    line-height: 1.4;
    outline: none;
}
.cm-sel__trigger:hover                                      { border-color: #cbd5e1; }
.cm-sel__trigger:focus,
.cm-sel__trigger.is-open                                    { border-color: #063466; box-shadow: 0 0 0 3px rgba(6,52,102,0.1); }
.cm-sel__trigger.is-placeholder span.cm-sel__label          { color: #9ca3af; }

/* Value area */
.cm-sel__val {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    flex: 1;
    min-width: 0;
    overflow: hidden;
}
.cm-sel__label {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex: 1;
}

/* Chevron */
.cm-sel__chevron {
    flex-shrink: 0;
    color: #94a3b8;
    transition: transform 0.2s ease, color 0.15s;
}
.cm-sel__trigger.is-open .cm-sel__chevron { transform: rotate(180deg); color: #063466; }

/* Dropdown panel */
.cm-sel__panel {
    position: absolute;
    top: calc(100% + 5px);
    left: 0;
    right: 0;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 10px 32px rgba(6,52,102,0.13);
    z-index: 1000;
    overflow: hidden;
    max-height: 230px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
}
.cm-sel__panel.opens-up {
    top: auto;
    bottom: calc(100% + 5px);
}
.cm-sel__panel::-webkit-scrollbar       { width: 4px; }
.cm-sel__panel::-webkit-scrollbar-track { background: transparent; }
.cm-sel__panel::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

/* Options list */
.cm-sel__list {
    list-style: none;
    margin: 0;
    padding: 0.3rem;
}
.cm-sel__item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.7rem;
    border-radius: 7px;
    cursor: pointer;
    font-size: 0.875rem;
    color: #374151;
    transition: background 0.1s;
    user-select: none;
}
.cm-sel__item:hover                    { background: #f1f5f9; }
.cm-sel__item.is-selected              { background: #eef4ff; color: #063466; font-weight: 600; }
.cm-sel__item.is-placeholder           { color: #9ca3af; }
.cm-sel__item.is-placeholder:hover     { background: transparent; cursor: default; }

/* Item text */
.cm-sel__item-text { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

/* Check icon (right side of selected item) */
.cm-sel__check {
    flex-shrink: 0;
    color: #063466;
    opacity: 0;
    transition: opacity 0.1s;
}
.cm-sel__item.is-selected .cm-sel__check { opacity: 1; }

/* Color swatch */
.cm-sel__swatch {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 1.5px solid rgba(0,0,0,0.12);
    flex-shrink: 0;
    display: inline-block;
}

/* Panel open animation */
@keyframes cmSelOpen   { from { opacity:0; transform:translateY(-5px); } to { opacity:1; transform:translateY(0); } }
@keyframes cmSelOpenUp { from { opacity:0; transform:translateY(5px);  } to { opacity:1; transform:translateY(0); } }
.cm-sel__panel          { animation: cmSelOpen   0.15s ease; }
.cm-sel__panel.opens-up { animation: cmSelOpenUp 0.15s ease; }
</style>

<script>
(function () {
    'use strict';

    /* ── Color swatch map ──────────────────────────────────── */
    var COLORS = {
        BLACK:'#111111', WHITE:'#f8f8f8', SILVER:'#c0c0c0', GRAY:'#808080',
        GREY:'#808080', RED:'#dc2626', BLUE:'#2563eb', GREEN:'#16a34a',
        YELLOW:'#eab308', ORANGE:'#ea580c', BROWN:'#92400e', GOLD:'#b45309',
        BEIGE:'#d4b483', PURPLE:'#9333ea', PINK:'#ec4899', MAROON:'#7f1d1d',
        NAVY:'#1e3a5f', TAN:'#d4b483', CREAM:'#fffdd0', BURGUNDY:'#800020',
        CHAMPAGNE:'#f7e7ce', BRONZE:'#cd7f32',
    };

    var SVG_CHEVRON =
        '<svg class="cm-sel__chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16" ' +
        'viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" ' +
        'stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
        '<polyline points="6 9 12 15 18 9"/></svg>';

    var SVG_CHECK =
        '<svg class="cm-sel__check" xmlns="http://www.w3.org/2000/svg" width="14" height="14" ' +
        'viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" ' +
        'stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
        '<polyline points="20 6 9 17 4 12"/></svg>';

    function isColorField(sel) {
        return sel.name === 'color' || sel.name === 'interior_color';
    }

    function swatchHtml(value) {
        var hex = COLORS[(value || '').toUpperCase()];
        if (!hex) return '';
        var extra = (hex === '#f8f8f8') ? 'border-color:#d1d5db;' : '';
        return '<span class="cm-sel__swatch" style="background:' + hex + ';' + extra + '"></span>';
    }

    function closeAll(except) {
        document.querySelectorAll('.cm-sel__panel').forEach(function (p) {
            if (p !== except) {
                p.style.display = 'none';
                var t = p.closest('.cm-sel') && p.closest('.cm-sel').querySelector('.cm-sel__trigger');
                if (t) t.classList.remove('is-open');
            }
        });
    }

    function positionPanel(panel, wrapper) {
        var rect = wrapper.getBoundingClientRect();
        var spaceBelow = window.innerHeight - rect.bottom;
        if (spaceBelow < 250 && rect.top > 250) {
            panel.classList.add('opens-up');
        } else {
            panel.classList.remove('opens-up');
        }
    }

    function initSelect(nativeSel) {
        /* Guard — skip if already enhanced or explicitly opted out */
        if (nativeSel.dataset.cmInit) return;
        if (nativeSel.dataset.cmSkip)  return;
        nativeSel.dataset.cmInit = '1';

        var isColor = isColorField(nativeSel);

        /* Hide native select (keep in DOM for form submission) */
        nativeSel.style.cssText = 'position:absolute;opacity:0;pointer-events:none;width:1px;height:1px;overflow:hidden;';
        nativeSel.tabIndex = -1;

        /* Build wrapper */
        var wrapper = document.createElement('div');
        wrapper.className = 'cm-sel';

        /* Build trigger */
        var selOpt  = nativeSel.options[nativeSel.selectedIndex] || nativeSel.options[0];
        var selVal  = selOpt ? selOpt.value : '';
        var selTxt  = selOpt ? selOpt.text  : 'Select';
        var isEmpty = !selVal;

        var trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'cm-sel__trigger' + (isEmpty ? ' is-placeholder' : '');
        trigger.setAttribute('aria-haspopup', 'listbox');
        trigger.setAttribute('aria-expanded', 'false');
        trigger.innerHTML =
            '<span class="cm-sel__val">' +
                (isColor && !isEmpty ? swatchHtml(selVal) : '') +
                '<span class="cm-sel__label">' + selTxt + '</span>' +
            '</span>' +
            SVG_CHEVRON;

        /* Build panel */
        var panel = document.createElement('div');
        panel.className = 'cm-sel__panel';
        panel.setAttribute('role', 'listbox');
        panel.style.display = 'none';

        var list = document.createElement('ul');
        list.className = 'cm-sel__list';

        Array.from(nativeSel.options).forEach(function (opt) {
            var li = document.createElement('li');
            li.className = 'cm-sel__item' +
                (!opt.value ? ' is-placeholder' : '') +
                (opt.selected ? ' is-selected' : '');
            li.setAttribute('role', 'option');
            li.setAttribute('aria-selected', opt.selected ? 'true' : 'false');
            li.dataset.value = opt.value;

            li.innerHTML =
                (isColor && opt.value ? swatchHtml(opt.value) : '') +
                '<span class="cm-sel__item-text">' + opt.text + '</span>' +
                SVG_CHECK;

            if (!opt.value) {
                li.addEventListener('click', function () { closePanel(); });
            } else {
                li.addEventListener('click', function () {
                    /* Update native select value + fire change */
                    nativeSel.value = opt.value;
                    nativeSel.dispatchEvent(new Event('change', { bubbles: true }));

                    /* Update trigger display */
                    var labelEl = trigger.querySelector('.cm-sel__label');
                    var valEl   = trigger.querySelector('.cm-sel__val');
                    if (labelEl) labelEl.textContent = opt.text;

                    if (isColor) {
                        var oldSwatch = valEl.querySelector('.cm-sel__swatch');
                        if (oldSwatch) oldSwatch.remove();
                        if (opt.value) valEl.insertAdjacentHTML('afterbegin', swatchHtml(opt.value));
                    }

                    trigger.classList.remove('is-placeholder');

                    list.querySelectorAll('.cm-sel__item').forEach(function (it) {
                        it.classList.remove('is-selected');
                        it.setAttribute('aria-selected', 'false');
                    });
                    li.classList.add('is-selected');
                    li.setAttribute('aria-selected', 'true');

                    closePanel();
                });
            }

            list.appendChild(li);
        });

        panel.appendChild(list);

        /* Open / Close helpers */
        function openPanel() {
            positionPanel(panel, wrapper);
            panel.style.display = 'block';
            trigger.classList.add('is-open');
            trigger.setAttribute('aria-expanded', 'true');
        }
        function closePanel() {
            panel.style.display = 'none';
            trigger.classList.remove('is-open');
            trigger.setAttribute('aria-expanded', 'false');
        }

        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            if (panel.style.display === 'none') { closeAll(panel); openPanel(); }
            else { closePanel(); }
        });

        /* Keyboard navigation */
        trigger.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                if (panel.style.display === 'none') { closeAll(panel); openPanel(); }
                else { closePanel(); }
            }
            if (e.key === 'Escape') closePanel();
            if ((e.key === 'ArrowDown' || e.key === 'ArrowUp') && panel.style.display === 'none') {
                e.preventDefault(); closeAll(panel); openPanel();
            }
        });

        /* Assemble */
        nativeSel.parentNode.insertBefore(wrapper, nativeSel);
        wrapper.appendChild(trigger);
        wrapper.appendChild(panel);
        wrapper.appendChild(nativeSel);
    }

    /* ── Run on DOMContentLoaded ── */
    function initAll() {
        document.querySelectorAll('select.form-input').forEach(initSelect);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    /* ── Re-run for dynamically added selects (e.g. Vue/Alpine components) ── */
    window.cmInitSelects = initAll;

    /* ── Global dismiss on outside click or Escape ── */
    document.addEventListener('click', function () { closeAll(null); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeAll(null);
    });
})();
</script>
