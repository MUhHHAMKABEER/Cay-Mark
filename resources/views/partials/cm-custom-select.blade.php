{{--
    Global Portal-based Custom Select
    ────────────────────────────────────────────────────────────────────────
    Upgrades every <select class="form-input"> on the page.

    PORTAL APPROACH — panel is created as a direct child of <body> on open
    and completely removed on close. This means the panel can NEVER be
    clipped by overflow:hidden, overflow:auto, or any CSS stacking context
    (transform, filter, will-change, contain, etc.) on a parent element.
    position:fixed + getBoundingClientRect() pins it to the exact viewport
    position of the trigger at all times.

    Public API:
      window.cmInitSelects()          — init any un-enhanced selects
      window.cmSelRefreshTrigger(el)  — refresh trigger after native value change
--}}

<style>
/* ── Trigger & wrapper ─────────────────────────────────────────── */
.cm-sel {
    position: relative;
    width: 100%;
}
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
    box-sizing: border-box;
}
.cm-sel__trigger:hover                   { border-color: #cbd5e1; }
.cm-sel__trigger:focus,
.cm-sel__trigger.is-open                 { border-color: #063466; box-shadow: 0 0 0 3px rgba(6,52,102,0.1); }
.cm-sel__trigger.is-placeholder
    span.cm-sel__label                   { color: #9ca3af; }

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
.cm-sel__chevron {
    flex-shrink: 0;
    color: #94a3b8;
    transition: transform 0.2s ease, color 0.15s;
}
.cm-sel__trigger.is-open .cm-sel__chevron {
    transform: rotate(180deg);
    color: #063466;
}
.cm-sel__swatch {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 1.5px solid rgba(0,0,0,0.12);
    flex-shrink: 0;
    display: inline-block;
}

/* ── Portal panel (lives in <body>, NEVER inside a parent container) ── */
/* These rules must be global; the panel is outside any scoped container. */
.cmselp {
    position: fixed !important;
    z-index: 99999 !important;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 12px 36px rgba(6,52,102,0.18);
    max-height: 240px;
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
    animation: cmselp-in 0.13s ease;
    box-sizing: border-box;
}
.cmselp.opens-up { animation: cmselp-up 0.13s ease; }
.cmselp::-webkit-scrollbar       { width: 4px; }
.cmselp::-webkit-scrollbar-track { background: transparent; }
.cmselp::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

@keyframes cmselp-in { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }
@keyframes cmselp-up { from { opacity:0; transform:translateY(4px);  } to { opacity:1; transform:translateY(0); } }

.cmselp ul {
    list-style: none;
    margin: 0;
    padding: 0.3rem;
}
.cmselp li {
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
    font-family: inherit;
}
.cmselp li:hover                { background: #f1f5f9; }
.cmselp li.is-sel               { background: #eef4ff; color: #063466; font-weight: 600; }
.cmselp li.is-ph                { color: #9ca3af; cursor: default; }
.cmselp li.is-ph:hover          { background: transparent; }
.cmselp .cmselp-txt             { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cmselp .cmselp-chk             { flex-shrink:0; color:#063466; opacity:0; transition:opacity 0.1s; }
.cmselp li.is-sel .cmselp-chk  { opacity: 1; }
</style>

<script>
(function () {
    'use strict';

    /* ── Color swatch map ───────────────────────────────────────── */
    var COLORS = {
        BLACK:'#111111', WHITE:'#f8f8f8', SILVER:'#c0c0c0', GRAY:'#808080',
        GREY:'#808080',  RED:'#dc2626',   BLUE:'#2563eb',   GREEN:'#16a34a',
        YELLOW:'#eab308',ORANGE:'#ea580c',BROWN:'#92400e',  GOLD:'#b45309',
        BEIGE:'#d4b483', PURPLE:'#9333ea',PINK:'#ec4899',   MAROON:'#7f1d1d',
        NAVY:'#1e3a5f',  TAN:'#d4b483',   CREAM:'#fffdd0',  BURGUNDY:'#800020',
        CHAMPAGNE:'#f7e7ce', BRONZE:'#cd7f32', SILVER:'#c0c0c0',
    };

    var SVG_CHEVRON =
        '<svg class="cm-sel__chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16"' +
        ' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"' +
        ' stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
        '<polyline points="6 9 12 15 18 9"/></svg>';

    var SVG_CHECK =
        '<svg class="cmselp-chk" xmlns="http://www.w3.org/2000/svg" width="13" height="13"' +
        ' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"' +
        ' stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
        '<polyline points="20 6 9 17 4 12"/></svg>';

    function isColor(name) { return name === 'color' || name === 'interior_color'; }

    function swatch(value) {
        var hex = COLORS[(value || '').toUpperCase()];
        if (!hex) return '';
        return '<span class="cm-sel__swatch" style="background:' + hex + ';' +
               (hex === '#f8f8f8' ? 'border-color:#d1d5db;' : '') + '"></span>';
    }

    /* ── Single active portal state ────────────────────────────── */
    var _portal  = null;   // current open panel DOM node
    var _trigger = null;   // trigger button that owns the portal

    function killPortal() {
        if (_portal)  { _portal.remove();  _portal  = null; }
        if (_trigger) {
            _trigger.classList.remove('is-open');
            _trigger.setAttribute('aria-expanded', 'false');
            _trigger = null;
        }
    }

    function spawnPortal(nativeSel, trigger, colorField) {
        killPortal(); // close any existing panel first

        /* Measure trigger in the viewport */
        var r          = trigger.getBoundingClientRect();
        var spaceBelow = window.innerHeight - r.bottom;
        var openUp     = spaceBelow < 260 && r.top > 260;

        /* Build panel */
        var panel = document.createElement('div');
        panel.className = 'cmselp' + (openUp ? ' opens-up' : '');

        /* position:fixed — coords are viewport-relative, no scrollY needed */
        panel.style.cssText =
            'width:'  + r.width + 'px;' +
            'left:'   + r.left  + 'px;' +
            (openUp
                ? 'bottom:' + (window.innerHeight - r.top + 4) + 'px;top:auto;'
                : 'top:'    + (r.bottom + 4) + 'px;bottom:auto;');

        /* Build option list */
        var ul = document.createElement('ul');
        ul.setAttribute('role', 'listbox');

        Array.from(nativeSel.options).forEach(function (opt) {
            var li = document.createElement('li');
            li.setAttribute('role', 'option');
            li.setAttribute('aria-selected', opt.selected ? 'true' : 'false');
            li.dataset.v = opt.value;

            if (!opt.value)   li.className = 'is-ph';
            else if (opt.selected) li.className = 'is-sel';

            li.innerHTML =
                (colorField && opt.value ? swatch(opt.value) : '') +
                '<span class="cmselp-txt">' + opt.text + '</span>' +
                SVG_CHECK;

            /* mousedown fires before the trigger's blur, so the panel
               is still alive when we need to read the selection */
            li.addEventListener('mousedown', function (e) {
                e.preventDefault();
                if (!opt.value) { killPortal(); return; }

                /* 1 — update native select */
                nativeSel.value = opt.value;
                nativeSel.dispatchEvent(new Event('change', { bubbles: true }));

                /* 2 — refresh trigger text/swatch */
                refreshTrigger(trigger, colorField, opt.value, opt.text);

                /* 3 — destroy portal */
                killPortal();
            });

            ul.appendChild(li);
        });

        panel.appendChild(ul);

        /* Portal → directly inside <body>, outside ALL form containers */
        document.body.appendChild(panel);

        _portal  = panel;
        _trigger = trigger;
        trigger.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
    }

    /* Update trigger button text & swatch after a selection (or VIN decode) */
    function refreshTrigger(trigger, colorField, value, text) {
        var labelEl = trigger.querySelector('.cm-sel__label');
        var valEl   = trigger.querySelector('.cm-sel__val');
        if (labelEl) labelEl.textContent = text;
        if (colorField && valEl) {
            var old = valEl.querySelector('.cm-sel__swatch');
            if (old) old.remove();
            if (value) valEl.insertAdjacentHTML('afterbegin', swatch(value));
        }
        if (value) trigger.classList.remove('is-placeholder');
    }

    /* ── Per-select initialisation ─────────────────────────────── */
    function initSelect(nativeSel) {
        if (nativeSel.dataset.cmInit) return;
        if (nativeSel.dataset.cmSkip) return;
        nativeSel.dataset.cmInit = '1';

        var colorField = isColor(nativeSel.name);

        /* Hide native select (keep in DOM for form POST) */
        nativeSel.style.cssText =
            'position:absolute;opacity:0;pointer-events:none;width:1px;height:1px;overflow:hidden;';
        nativeSel.tabIndex = -1;

        /* Wrapper */
        var wrapper = document.createElement('div');
        wrapper.className = 'cm-sel';

        /* Trigger */
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
                (colorField && !isEmpty ? swatch(selVal) : '') +
                '<span class="cm-sel__label">' + selTxt + '</span>' +
            '</span>' +
            SVG_CHEVRON;

        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            if (_trigger === trigger) { killPortal(); }          // toggle closed
            else { spawnPortal(nativeSel, trigger, colorField); } // open
        });

        trigger.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                if (_trigger === trigger) killPortal();
                else spawnPortal(nativeSel, trigger, colorField);
            }
            if (e.key === 'Escape') killPortal();
            if ((e.key === 'ArrowDown' || e.key === 'ArrowUp') && _trigger !== trigger) {
                e.preventDefault();
                spawnPortal(nativeSel, trigger, colorField);
            }
        });

        /* Place wrapper where the native select was; keep native inside wrapper */
        nativeSel.parentNode.insertBefore(wrapper, nativeSel);
        wrapper.appendChild(trigger);
        wrapper.appendChild(nativeSel);
        /* NOTE: portal panel is NOT added here — created fresh each time */
    }

    /* ── Global listeners ──────────────────────────────────────── */

    /* Click outside trigger or portal → close */
    document.addEventListener('click', function (e) {
        if (!_portal) return;
        if (_trigger && _trigger.contains(e.target)) return;
        if (_portal.contains(e.target)) return;
        killPortal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') killPortal();
    });

    /* Scroll or resize → close (stale position would misalign panel) */
    window.addEventListener('scroll', killPortal, true);
    window.addEventListener('resize', killPortal);

    /* ── Init ──────────────────────────────────────────────────── */
    function initAll() {
        document.querySelectorAll('select.form-input').forEach(initSelect);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    /* ── Public API ────────────────────────────────────────────── */

    /* Re-initialise any newly added selects (e.g. after Alpine/AJAX re-render) */
    window.cmInitSelects = initAll;

    /* Called by VIN decoder in scripts.blade.php after setting a native value */
    window.cmSelRefreshTrigger = function (nativeSel) {
        var wrapper = nativeSel.parentNode;
        if (!wrapper || !wrapper.classList.contains('cm-sel')) return;
        var trig = wrapper.querySelector('.cm-sel__trigger');
        if (!trig) return;
        var opt = nativeSel.options[nativeSel.selectedIndex];
        if (!opt || !opt.value) return;
        refreshTrigger(trig, isColor(nativeSel.name), opt.value, opt.text);
    };

})();
</script>
