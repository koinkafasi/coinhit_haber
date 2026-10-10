/**
 * Bloomberg HT — canlı piyasa verisi polling (Bölüm 5, 10).
 *
 * `[data-market-code]` taşıyan her satır, `/piyasa/veri` ucundan periyodik
 * olarak güncellenir. Alt alanlar `[data-market-field]` ile işaretlenir:
 * last, change, change_percent, bid, ask, high, low, volume_lot, volume_tl,
 * volume_24h, market_cap, change_7d, time.
 */
(function () {
    'use strict';

    var ENDPOINT = '/piyasa/veri';
    var GROUPS = 'bist_index,world_index,stock,currency,parity,emerging_fx,gold,precious_metal,'
        + 'agriculture,industry,energy,bond_tr,repo,eurobond,intl_bond,alternative,baltic,crypto';
    var timer = null;

    function trNumber(value, decimals) {
        var n = Number(value);
        if (!isFinite(n)) {
            return '';
        }
        try {
            return n.toLocaleString('tr-TR', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
        } catch (e) {
            return n.toFixed(decimals).replace('.', ',');
        }
    }

    function formatter(quote) {
        var d = (typeof quote.decimals === 'number') ? quote.decimals : 2;
        var signed = function (v, dec) { return (Number(v) > 0 ? '+' : '') + trNumber(v, dec); };

        return {
            last: function (v) { return trNumber(v, d); },
            previous_close: function (v) { return trNumber(v, d); },
            open: function (v) { return trNumber(v, d); },
            high: function (v) { return trNumber(v, d); },
            low: function (v) { return trNumber(v, d); },
            bid: function (v) { return trNumber(v, d); },
            ask: function (v) { return trNumber(v, d); },
            change: function (v) { return signed(v, d); },
            change_percent: function (v) { return signed(v, 2) + '%'; },
            monthly_percent: function (v) { return signed(v, 2) + '%'; },
            yearly_percent: function (v) { return signed(v, 2) + '%'; },
            change_7d: function (v) { return signed(v, 2) + '%'; },
            volume_lot: function (v) { return trNumber(v, 0); },
            volume_tl: function (v) { return trNumber(v, 0); },
            volume_24h: function (v) { return '$' + trNumber(v, 0); },
            market_cap: function (v) { return '$' + trNumber(v, 0); },
            time: function (v) { return v; }
        };
    }

    function applyField(node, value) {
        if (value === undefined || value === null || value === '') {
            return;
        }

        node.textContent = value;
    }

    function directionClass(value) {
        if (value > 0) {
            return 'is-up';
        }
        if (value < 0) {
            return 'is-down';
        }

        return 'is-flat';
    }

    function render(quote) {
        var rows = document.querySelectorAll('[data-market-code="' + quote.code + '"]');
        var format = formatter(quote);

        rows.forEach(function (row) {
            if (quote.direction) {
                row.classList.remove('is-up', 'is-down', 'is-flat');
                row.classList.add('is-' + quote.direction);
            }

            row.querySelectorAll('[data-market-field]').forEach(function (node) {
                var field = node.getAttribute('data-market-field');

                if (field === 'time' || field === 'hour') {
                    applyField(node, quote.updated_at ? quote.updated_at.slice(-8) : '');
                    return;
                }

                var formatted = quote.formatted ? quote.formatted[field] : undefined;
                if (formatted !== undefined) {
                    applyField(node, formatted);
                    return;
                }

                if (typeof format[field] === 'function') {
                    applyField(node, format[field](quote[field]));
                    return;
                }

                applyField(node, quote[field]);
            });
        });
    }

    function poll() {
        fetch(ENDPOINT + '?groups=' + encodeURIComponent(GROUPS), {
            headers: { 'Accept': 'application/json' }
        })
            .then(function (response) { return response.ok ? response.json() : null; })
            .then(function (data) {
                if (!data) {
                    return;
                }

                var updated = document.querySelector('[data-market-updated]');
                if (updated && data.updated_at) {
                    updated.textContent = data.updated_at;
                }

                if (!data.groups) {
                    return;
                }

                Object.keys(data.groups).forEach(function (group) {
                    data.groups[group].forEach(render);
                });
            })
            .catch(function () { /* sessizce yoksay */ });
    }

    function init() {
        if (!document.querySelector('[data-market-code]')) {
            return;
        }

        poll();

        var seconds = 5;
        var hint = document.querySelector('[data-market-poll]');
        if (hint) {
            seconds = parseInt(hint.getAttribute('data-market-poll'), 10) || 5;
        }

        timer = window.setInterval(poll, seconds * 1000);

        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                window.clearInterval(timer);
            } else {
                poll();
                timer = window.setInterval(poll, seconds * 1000);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();