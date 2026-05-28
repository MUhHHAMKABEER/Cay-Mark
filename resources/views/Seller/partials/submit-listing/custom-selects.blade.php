<style>
/* ─── Custom Select — trigger & wrapper only ────────────────── */
/* The dropdown panel is NOT styled here — it is created
   programmatically and injected into document.body as a portal. */

.cm-sel {
    position: relative;
    width: 100%;
}

/* Trigger button */
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
.cm-sel__trigger:hover            { border-color: #cbd5e1; }
.cm-sel__trigger:focus,
.cm-sel__trigger.is-open          { border-color: #063466; box-shadow: 0 0 0 3px rgba(6,52,102,0.1); }
.cm-sel__trigger.is-placeholder
    span.cm-sel__label            { color: #9ca3af; }

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
.cm-sel__trigger.is-open .cm-sel__chevron {
    transform: rotate(180deg);
    color: #063466;
}

/* Color swatch (in trigger) */
.cm-sel__swatch {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 1.5px solid rgba(0,0,0,0.12);
    flex-shrink: 0;
    display: inline-block;
}

/* ─── Portal panel (injected into <body>) ───────────────────── */
/* These styles must be on the global sheet because the panel
   lives outside any scoped container. */
.cmsel-portal {
    position: fixed;
    z-index: 99999;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    box-shadow: 0 10px 32px rgba(6,52,102,0.16);
    max-height: 240px;
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
    animation: cmselFadeIn 0.13s ease;
}
.cmsel-portal.opens-up { animation: cmselFadeUp 0.13s ease; }
.cmsel-portal::-webkit-scrollbar       { width: 4px; }
.cmsel-portal::-webkit-scrollbar-track { background: transparent; }
.cmsel-portal::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

@keyframes cmselFadeIn  { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }
@keyframes cmselFadeUp  { from { opacity:0; transform:translateY(4px);  } to { opacity:1; transform:translateY(0); } }

/* Options */
.cmsel-portal ul {
    list-style: none;
    margin: 0;
    padding: 0.3rem;
}
.cmsel-portal li {
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
.cmsel-portal li:hover               { background: #f1f5f9; }
.cmsel-portal li.is-selected         { background: #eef4ff; color: #063466; font-weight: 600; }
.cmsel-portal li.is-placeholder      { color: #9ca3af; cursor: default; }
.cmsel-portal li.is-placeholder:hover{ background: transparent; }
.cmsel-portal .cmsel-item-text       { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cmsel-portal .cmsel-check           {
    flex-shrink: 0;
    color: #063466;
    opacity: 0;
    transition: opacity 0.1s;
}
.cmsel-portal li.is-selected .cmsel-check { opacity: 1; }
.cmsel-portal .cm-sel__swatch        { flex-shrink: 0; }
</style>

<script>
(function () {
    'use strict';

    /* ── Color swatch map ─────────────────────────────────────── */
    var COLORS = {
        BLACK:'#111111', WHITE:'#f8f8f8', SILVER:'#c0c0c0', GRAY:'#808080',
        GREY:'#808080',  RED:'#dc2626',   BLUE:'#2563eb',   GREEN:'#16a34a',
        YELLOW:'#eab308',ORANGE:'#ea580c',BROWN:'#92400e',  GOLD:'#b45309',
        BEIGE:'#d4b483', PURPLE:'#9333ea',PINK:'#ec4899',   MAROON:'#7f1d1d',
        NAVY:'#1e3a5f',  TAN:'#d4b483',   CREAM:'#fffdd0',  BURGUNDY:'#800020',
        CHAMPAGNE:'#f7e7ce', BRONZE:'#cd7f32',
    };

    var SVG_CHEVRON =
        '<svg class="cm-sel__chevron" xmlns="http://www.w3.org/2000/svg" width="16" height="16"' +
        ' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"' +
        ' stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
        '<polyline points="6 9 12 15 18 9"/></svg>';

    var SVG_CHECK =
        '<svg class="cmsel-check" xmlns="http://www.w3.org/2000/svg" width="14" height="14"' +
        ' viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"' +
        ' stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
        '<polyline points="20 6 9 17 4 12"/></svg>';

    function isColorField(sel) {
        return sel.name === 'color' || sel.name === 'interior_color';
    }

    function swatchHtml(value) {
        var hex = COLORS[(value || '').toUpperCase()];
        if (!hex) return '';
        var border = (hex === '#f8f8f8') ? 'border-color:#d1d5db;' : '';
        return '<span class="cm-sel__swatch" style="background:' + hex + ';' + border + '"></span>';
    }

    /* ── Single active portal ─────────────────────────────────── */
    var _portal        = null;   // the currently-open panel DOM node
    var _activeTrigger = null;   // the trigger button that owns the portal
    var _activeNative  = null;   // the native <select> that owns the portal

    function destroyPortal() {
        if (_portal) {
            _portal.remove();
            _portal = null;
        }
        if (_activeTrigger) {
            _activeTrigger.classList.remove('is-open');
            _activeTrigger.setAttribute('aria-expanded', 'false');
            _activeTrigger = null;
        }
        _activeNative = null;
    }

    function buildPortal(nativeSel, trigger, isColor) {
        /* Position using viewport-fixed coords from getBoundingClientRect.
           position:fixed means top/left are relative to the viewport —
           no scrollY/scrollX needed. */
        var rect       = trigger.getBoundingClientRect();
        var spaceBelow = window.innerHeight - rect.bottom;
        var openUp     = spaceBelow < 260 && rect.top > 260;

        var panel = document.createElement('div');
        panel.className = 'cmsel-portal' + (openUp ? ' opens-up' : '');

        /* ── Exact fixed coordinates ── */
        panel.style.width = rect.width + 'px';
        panel.style.left  = rect.left  + 'px';
        if (openUp) {
            panel.style.bottom = (window.innerHeight - rect.top + 4) + 'px';
            panel.style.top    = 'auto';
        } else {
            panel.style.top    = (rect.bottom + 4) + 'px';
            panel.style.bottom = 'auto';
        }

        /* ── Build option list ── */
        var ul = document.createElement('ul');
        ul.setAttribute('role', 'listbox');

        Array.from(nativeSel.options).forEach(function (opt) {
            var li = document.createElement('li');
            li.setAttribute('role', 'option');
            li.setAttribute('aria-selected', opt.selected ? 'true' : 'false');
            li.dataset.value = opt.value;

            if (!opt.value) {
                li.className = 'is-placeholder';
            } else if (opt.selected) {
                li.className = 'is-selected';
            }

            li.innerHTML =
                (isColor && opt.value ? swatchHtml(opt.value) : '') +
                '<span class="cmsel-item-text">' + opt.text + '</span>' +
                SVG_CHECK;

            li.addEventListener('mousedown', function (e) {
                /* mousedown fires before the trigger's blur, preventing
                   a race where blur destroys the portal before click fires */
                e.preventDefault();

                if (!opt.value) {
                    destroyPortal();
                    return;
                }

                /* 1. Update native <select> */
                nativeSel.value = opt.value;
                nativeSel.dispatchEvent(new Event('change', { bubbles: true }));

                /* 2. Update trigger display */
                updateTriggerDisplay(trigger, isColor, opt.value, opt.text);

                /* 3. Remove portal */
                destroyPortal();
            });

            ul.appendChild(li);
        });

        panel.appendChild(ul);

        /* ── Inject into body — completely outside all form containers ── */
        document.body.appendChild(panel);

        _portal        = panel;
        _activeTrigger = trigger;
        _activeNative  = nativeSel;

        trigger.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
    }

    /* ── Trigger display helper (also called by VIN decoder) ─── */
    function updateTriggerDisplay(trigger, isColor, value, text) {
        var labelEl = trigger.querySelector('.cm-sel__label');
        var valEl   = trigger.querySelector('.cm-sel__val');
        if (labelEl) labelEl.textContent = text;
        if (isColor && valEl) {
            var old = valEl.querySelector('.cm-sel__swatch');
            if (old) old.remove();
            if (value) valEl.insertAdjacentHTML('afterbegin', swatchHtml(value));
        }
        if (value) trigger.classList.remove('is-placeholder');
    }

    /* ── Init a single <select> ─────────────────────────────── */
    function initSelect(nativeSel) {
        if (nativeSel.dataset.cmInit) return;
        nativeSel.dataset.cmInit = '1';

        var isColor = isColorField(nativeSel);

        /* Hide native select — keep it for form submission */
        nativeSel.style.cssText =
            'position:absolute;opacity:0;pointer-events:none;width:1px;height:1px;overflow:hidden;';
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

        /* Toggle on trigger click */
        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            if (_activeTrigger === trigger) {
                destroyPortal();
            } else {
                destroyPortal();            // close any other open panel first
                buildPortal(nativeSel, trigger, isColor);
            }
        });

        /* Keyboard: Enter/Space toggles, Escape closes, arrows open */
        trigger.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                if (_activeTrigger === trigger) destroyPortal();
                else { destroyPortal(); buildPortal(nativeSel, trigger, isColor); }
            }
            if (e.key === 'Escape') destroyPortal();
            if ((e.key === 'ArrowDown' || e.key === 'ArrowUp') && _activeTrigger !== trigger) {
                e.preventDefault();
                destroyPortal();
                buildPortal(nativeSel, trigger, isColor);
            }
        });

        /* Assemble: wrapper replaces native select in the DOM;
           native select is kept inside wrapper for form submission */
        nativeSel.parentNode.insertBefore(wrapper, nativeSel);
        wrapper.appendChild(trigger);
        wrapper.appendChild(nativeSel);
        /* NOTE: panel is NOT appended here — it is created fresh on each open */
    }

    /* ── Global close listeners ─────────────────────────────── */

    /* Click outside the portal or the active trigger → close */
    document.addEventListener('click', function (e) {
        if (!_portal) return;
        if (_activeTrigger && _activeTrigger.contains(e.target)) return;
        if (_portal.contains(e.target)) return;
        destroyPortal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') destroyPortal();
    });

    /* Scroll or resize → reposition is not worth the complexity;
       simply close to avoid a stale/misaligned panel */
    window.addEventListener('scroll', destroyPortal, true);
    window.addEventListener('resize', destroyPortal);

    /* ── Init on DOM ready ──────────────────────────────────── */
    function initAll() {
        document.querySelectorAll('select.form-input').forEach(initSelect);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    /* ── Public API used by VIN decoder (scripts.blade.php) ─── */
    window.cmSelRefreshTrigger = function (nativeSel) {
        var wrapper = nativeSel.parentNode;
        if (!wrapper || !wrapper.classList.contains('cm-sel')) return;
        var trigger = wrapper.querySelector('.cm-sel__trigger');
        if (!trigger) return;
        var selOpt = nativeSel.options[nativeSel.selectedIndex];
        if (!selOpt || !selOpt.value) return;
        var isColor = nativeSel.name === 'color' || nativeSel.name === 'interior_color';
        updateTriggerDisplay(trigger, isColor, selOpt.value, selOpt.text);
    };

})();
</script>
