/**
 * Bloomberg HT — global çerçeve etkileşimleri (Bölüm 2, 10, 12).
 * Çerez onayı, mobil menü, arama yönlendirmesi (/arama/{q}), yukarı dön.
 */
(function () {
    'use strict';

    var CONSENT_KEY = 'bht-cookie-consent';

    function initCookie() {
        var bar = document.querySelector('[data-cookie-bar]');
        if (!bar) {
            return;
        }

        var accepted;
        try {
            accepted = window.localStorage.getItem(CONSENT_KEY);
        } catch (e) {
            accepted = '1';
        }

        if (!accepted) {
            bar.removeAttribute('hidden');
        }

        function dismiss() {
            try {
                window.localStorage.setItem(CONSENT_KEY, '1');
            } catch (e) {
                /* yoksay */
            }
            bar.setAttribute('hidden', '');
        }

        var accept = bar.querySelector('[data-cookie-accept]');
        var close = bar.querySelector('[data-cookie-close]');

        if (accept) {
            accept.addEventListener('click', dismiss);
        }
        if (close) {
            close.addEventListener('click', dismiss);
        }
    }

    function initMenu() {
        var toggle = document.querySelector('[data-menu-toggle]');
        if (!toggle) {
            return;
        }

        var nav = document.getElementById(toggle.getAttribute('aria-controls'));

        toggle.addEventListener('click', function () {
            var open = document.body.classList.toggle('bht-nav-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    function initSearch() {
        var form = document.querySelector('[data-search-form]');
        if (!form) {
            return;
        }

        form.addEventListener('submit', function (event) {
            var input = form.querySelector('input[name="q"]');
            var value = input ? input.value.trim() : '';

            if (!value) {
                event.preventDefault();
                return;
            }

            event.preventDefault();
            window.location.href = '/arama/' + encodeURIComponent(value);
        });
    }

    function initScrollTop() {
        var button = document.querySelector('[data-scroll-top]');
        if (!button) {
            return;
        }

        function toggle() {
            button.classList.toggle('is-visible', window.scrollY > 400);
        }

        window.addEventListener('scroll', toggle, { passive: true });
        toggle();

        button.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    function init() {
        initCookie();
        initMenu();
        initSearch();
        initScrollTop();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();