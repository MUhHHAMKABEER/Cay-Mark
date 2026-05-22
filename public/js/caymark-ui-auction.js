/**
 * CayMark global UI — Section 4: Auction-specific components
 * CaymarkUI.auction.initCountdowns / confirmBid / initWatchlistHearts
 */
(function (global) {
    'use strict';

    var countdownTimerId = null;

    function pad2(n) {
        return String(n).padStart(2, '0');
    }

    function getCsrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return (meta && meta.getAttribute('content')) || global.csrfToken || '';
    }

    function calcBuyerFee(amount, config) {
        config = config || global.CaymarkUIAuctionConfig || {};
        var rate = typeof config.buyerFeeRate === 'number' ? config.buyerFeeRate : 0.06;
        var min = typeof config.buyerFeeMin === 'number' ? config.buyerFeeMin : 100;
        var fee = Math.max(amount * rate, min);
        return Math.round(fee * 100) / 100;
    }

    function formatMoney(n) {
        return '$' + Number(n).toLocaleString(undefined, { maximumFractionDigits: 0 });
    }

    function tickCountdown(el) {
        var endStr = el.getAttribute('data-cm-countdown-end');
        if (!endStr) return;

        var endTime = new Date(endStr);
        var diff = Math.max(0, Math.floor((endTime - Date.now()) / 1000));
        var isGrid = el.classList.contains('cm-countdown--grid');
        var isDetail = el.classList.contains('cm-countdown--detail');

        if (diff <= 0) {
            el.classList.remove('cm-countdown--urgent');
            if (isGrid) {
                var ended = document.createElement('span');
                ended.className = 'cm-countdown-ended cm-countdown-ended--grid';
                ended.textContent = 'Ended';
                el.replaceWith(ended);
            } else if (isDetail) {
                el.classList.add('cm-countdown--ended');
                var display = el.querySelector('[data-cm-countdown-display]');
                if (display) display.textContent = 'Auction Ended';
            }
            return;
        }

        var days = Math.floor(diff / 86400);
        var hours = Math.floor((diff % 86400) / 3600);
        var minutes = Math.floor((diff % 3600) / 60);
        var seconds = diff % 60;

        if (diff < 3600) {
            el.classList.add('cm-countdown--urgent');
        } else {
            el.classList.remove('cm-countdown--urgent');
        }

        if (isGrid) {
            var units = { days: days, hours: hours, minutes: minutes, seconds: seconds };
            el.querySelectorAll('[data-cm-unit]').forEach(function (seg) {
                var unit = seg.getAttribute('data-cm-unit');
                if (units[unit] !== undefined) {
                    seg.textContent = pad2(units[unit]);
                }
            });
        } else if (isDetail) {
            var displayEl = el.querySelector('[data-cm-countdown-display]');
            if (!displayEl) return;
            if (days > 0) {
                displayEl.textContent = days + 'd : ' + hours + 'h : ' + minutes + 'm : ' + seconds + 's';
            } else if (hours > 0) {
                displayEl.textContent = hours + 'h : ' + minutes + 'm : ' + seconds + 's';
            } else {
                displayEl.textContent = minutes + 'm : ' + seconds + 's';
            }
        }
    }

    function initCountdowns(root) {
        root = root || document;
        root.querySelectorAll('[data-cm-countdown-end]').forEach(tickCountdown);

        if (!countdownTimerId) {
            countdownTimerId = setInterval(function () {
                document.querySelectorAll('[data-cm-countdown-end]').forEach(tickCountdown);
            }, 1000);
        }
    }

    function confirmBid(options) {
        options = options || {};
        var vehicleName = options.vehicleName != null ? String(options.vehicleName) : 'this vehicle';
        var amount = Number(options.amount) || 0;
        var buyerFee = options.buyerFee != null
            ? Number(options.buyerFee)
            : calcBuyerFee(amount, options.config);

        var description = 'Place a bid of ' + formatMoney(amount) + ' on ' + vehicleName.trim() + '? '
            + 'Estimated buyer fee: ' + formatMoney(buyerFee) + ' (6% of sale price, $100 minimum).';

        if (global.CaymarkUI && typeof global.CaymarkUI.confirm === 'function') {
            return global.CaymarkUI.confirm({
                title: options.title || 'Confirm your bid',
                description: description,
                confirmText: options.confirmText || 'Confirm Bid',
                cancelText: options.cancelText || 'Cancel',
                danger: false,
            });
        }

        return Promise.resolve(global.confirm(description));
    }

    function setWatchlistState(btn, inWatchlist) {
        var icon = btn.querySelector('[data-cm-watchlist-icon]');
        var label = btn.querySelector('[data-cm-watchlist-label]');
        var isButton = btn.getAttribute('data-variant') === 'button';

        btn.setAttribute('data-in-watchlist', inWatchlist ? '1' : '0');
        btn.setAttribute('aria-pressed', inWatchlist ? 'true' : 'false');

        if (icon) {
            icon.textContent = inWatchlist ? 'favorite' : 'favorite_border';
        }

        if (isButton) {
            btn.classList.toggle('active', inWatchlist);
            if (label) {
                label.textContent = inWatchlist ? 'Added to Watchlist' : 'Add to Watchlist';
            }
        } else {
            btn.classList.toggle('is-active', inWatchlist);
        }
    }

    function initWatchlistHearts(root) {
        root = root || document;
        root.querySelectorAll('[data-cm-watchlist-heart]').forEach(function (btn) {
            if (btn.dataset.cmWatchlistBound === '1') return;
            btn.dataset.cmWatchlistBound = '1';

            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                if (btn.getAttribute('data-auth') !== '1') {
                    var loginUrl = btn.getAttribute('data-login-url') || (global.loginUrl || '/login');
                    global.location.href = loginUrl;
                    return;
                }

                if (btn.classList.contains('is-saving')) return;
                btn.classList.add('is-saving');

                fetch(btn.getAttribute('data-url'), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                })
                    .then(function (response) {
                        if (!response.ok && response.status === 422) {
                            return response.json().then(function (err) {
                                throw err;
                            });
                        }
                        return response.json();
                    })
                    .then(function (data) {
                        var liked = !!data.in_watchlist;
                        setWatchlistState(btn, liked);
                        btn.classList.add('is-pop');
                        setTimeout(function () {
                            btn.classList.remove('is-pop');
                        }, 500);

                        var countEl = btn.querySelector('[data-cm-watchlist-count]');
                        if (countEl && typeof data.likes_count !== 'undefined') {
                            countEl.textContent = data.likes_count;
                        }

                        if (global.CaymarkUI && liked) {
                            global.CaymarkUI.showSuccess(
                                'Added to watchlist',
                                'You will be notified of updates on this listing.'
                            );
                        }
                    })
                    .catch(function (err) {
                        if (global.CaymarkUI && err && err.message) {
                            global.CaymarkUI.showError('Watchlist', err.message);
                        }
                    })
                    .finally(function () {
                        btn.classList.remove('is-saving');
                    });
            });
        });
    }

    function initOutbidBanners(root) {
        root = root || document;
        root.querySelectorAll('[data-cm-outbid-banner]').forEach(function (banner) {
            if (banner.dataset.cmOutbidBound === '1') return;
            banner.dataset.cmOutbidBound = '1';

            var dismiss = banner.querySelector('[data-cm-outbid-dismiss]');
            if (dismiss) {
                dismiss.addEventListener('click', function () {
                    banner.remove();
                });
            }
        });
    }

    var auctionApi = {
        initCountdowns: initCountdowns,
        confirmBid: confirmBid,
        initWatchlistHearts: initWatchlistHearts,
        initOutbidBanners: initOutbidBanners,
        calcBuyerFee: calcBuyerFee,
    };

    global.CaymarkUI = global.CaymarkUI || {};
    global.CaymarkUI.auction = auctionApi;

    document.addEventListener('DOMContentLoaded', function () {
        initCountdowns();
        initWatchlistHearts();
        initOutbidBanners();
    });
})(typeof window !== 'undefined' ? window : this);
