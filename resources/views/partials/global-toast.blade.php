{{-- Global toast notifications — showSuccess(title, subtitle) / showError(title, subtitle) --}}
<style>
    #caymark-toast-host {
        position: fixed;
        top: 1.25rem;
        right: 1.25rem;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.75rem;
        max-width: calc(100vw - 2rem);
        pointer-events: none;
    }

    .caymark-toast {
        pointer-events: auto;
        position: relative;
        width: 100%;
        min-width: 300px;
        max-width: 400px;
        padding: 1rem 2.75rem 1rem 1rem;
        border-radius: 14px;
        box-shadow: 0 10px 40px rgba(15, 23, 42, 0.12), 0 2px 8px rgba(15, 23, 42, 0.06);
        display: flex;
        align-items: flex-start;
        gap: 0.875rem;
        transform: translateX(110%);
        opacity: 0;
        animation: caymarkToastIn 0.4s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }

    .caymark-toast.is-leaving {
        animation: caymarkToastOut 0.32s cubic-bezier(0.4, 0, 1, 1) forwards;
    }

    .caymark-toast--success {
        background: #e6f4ea;
        border: 1px solid #ceead6;
    }

    .caymark-toast--error {
        background: #fdecea;
        border: 1px solid #fad4cf;
    }

    .caymark-toast__icon {
        flex-shrink: 0;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .caymark-toast--success .caymark-toast__icon {
        background: #34a853;
    }

    .caymark-toast--error .caymark-toast__icon {
        background: #e8714a;
    }

    .caymark-toast__icon svg {
        width: 1.125rem;
        height: 1.125rem;
        color: #fff;
    }

    .caymark-toast__body {
        flex: 1;
        min-width: 0;
        padding-top: 0.125rem;
    }

    .caymark-toast__title {
        font-size: 0.9375rem;
        font-weight: 700;
        color: #1a1a1a;
        line-height: 1.35;
        margin: 0;
    }

    .caymark-toast__subtitle {
        font-size: 0.8125rem;
        font-weight: 400;
        color: #6b7280;
        line-height: 1.45;
        margin: 0.25rem 0 0;
    }

    .caymark-toast__subtitle:empty {
        display: none;
    }

    .caymark-toast__close {
        position: absolute;
        top: 0.625rem;
        right: 0.625rem;
        width: 1.75rem;
        height: 1.75rem;
        border: none;
        background: transparent;
        color: #9ca3af;
        cursor: pointer;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        line-height: 1;
        transition: color 0.15s ease, background 0.15s ease;
    }

    .caymark-toast__close:hover {
        color: #4b5563;
        background: rgba(0, 0, 0, 0.05);
    }

    .caymark-toast__close svg {
        width: 1rem;
        height: 1rem;
    }

    @keyframes caymarkToastIn {
        from {
            transform: translateX(110%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes caymarkToastOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(110%);
            opacity: 0;
        }
    }

    @media (max-width: 480px) {
        #caymark-toast-host {
            left: 1rem;
            right: 1rem;
            align-items: stretch;
        }
        .caymark-toast {
            min-width: 0;
            max-width: none;
        }
    }
</style>

<div id="caymark-toast-host" aria-live="polite" aria-relevant="additions"></div>

<script>
(function () {
    var DISMISS_MS = 3500;

    function getHost() {
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

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function iconSvg(type) {
        if (type === 'success') {
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 13l4 4L19 7"/></svg>';
        }
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 9v4m0 4h.01M12 3a9 9 0 100 18 9 9 0 000-18z"/></svg>';
    }

    function showToast(type, title, subtitle) {
        title = title != null ? String(title) : '';
        subtitle = subtitle != null ? String(subtitle) : '';

        var host = getHost();
        var toast = document.createElement('div');
        toast.className = 'caymark-toast caymark-toast--' + type;
        toast.setAttribute('role', 'alert');

        toast.innerHTML =
            '<div class="caymark-toast__icon" aria-hidden="true">' + iconSvg(type) + '</div>' +
            '<div class="caymark-toast__body">' +
                '<p class="caymark-toast__title">' + escapeHtml(title) + '</p>' +
                '<p class="caymark-toast__subtitle">' + escapeHtml(subtitle) + '</p>' +
            '</div>' +
            '<button type="button" class="caymark-toast__close" aria-label="Dismiss notification">' +
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>' +
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

        var timer = window.setTimeout(dismiss, DISMISS_MS);
        var closeBtn = toast.querySelector('.caymark-toast__close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function () {
                window.clearTimeout(timer);
                dismiss();
            });
        }

        return toast;
    }

    window.showSuccess = function (title, subtitle) {
        return showToast('success', title, subtitle || '');
    };

    window.showError = function (title, subtitle) {
        return showToast('error', title, subtitle || '');
    };

    window.showToast = showToast;

    @php
        $toastSuccess = session('success') ?? session('status');
        $toastError = session('error');
        if (is_array($toastError)) {
            $toastError = implode(' ', $toastError);
        }
    @endphp

    @if (!empty($toastSuccess))
    document.addEventListener('DOMContentLoaded', function () {
        showSuccess(@json($toastSuccess), '');
    });
    @endif

    @if (!empty($toastError))
    document.addEventListener('DOMContentLoaded', function () {
        showError(@json($toastError), '');
    });
    @endif
})();
</script>
