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

    /**
     * Sekme grupları (finance-slider, market widget, en çok okunanlar).
     */
    function bindTabs(container, buttonSelector, contentSelector) {
        if (!container) {
            return;
        }

        var buttons = container.querySelectorAll(buttonSelector);
        var contents = container.querySelectorAll(contentSelector);

        if (!buttons.length) {
            return;
        }

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                var key = button.getAttribute('data-tab') || button.getAttribute('data-subtab');

                buttons.forEach(function (other) {
                    var active = other === button;
                    other.classList.toggle('is-active', active);
                    if (other.hasAttribute('aria-selected')) {
                        other.setAttribute('aria-selected', active ? 'true' : 'false');
                    }
                });

                contents.forEach(function (content) {
                    var contentKey = content.getAttribute('data-id') || content.getAttribute('data-subcontent');
                    content.classList.toggle('hidden', contentKey !== key);
                });
            });
        });
    }

    function initTabs() {
        document.querySelectorAll('[data-type="finance-slider"]').forEach(function (container) {
            bindTabs(container, '.finance-slider-tab', '.finance-slider-track');
        });

        document.querySelectorAll('[data-widget-type="market-graphic-tab"]').forEach(function (container) {
            bindTabs(container, '.market-widget-tabs > [data-tab]', '.market-widget-panels > [data-id]');

            container.querySelectorAll('.market-widget-panel').forEach(function (panel) {
                bindTabs(panel, '.market-subtab', '.market-widget-chart');
            });
        });

        document.querySelectorAll('[data-widget-type="most-read"]').forEach(function (container) {
            bindTabs(container, '.most-read-tabs > [data-tab]', '[data-id]');
        });
    }

    /**
     * Hero main-slider (grid-cols-12 swiper karşılığı).
     */
    function initHeroSlider() {
        document.querySelectorAll('[data-hero-slider]').forEach(function (slider) {
            var track = slider.querySelector('[data-hero-track]');
            if (!track) {
                return;
            }

            var slides = Array.prototype.slice.call(track.children);
            if (slides.length < 2) {
                return;
            }

            var pagination = slider.querySelector('[data-hero-pagination]');
            var current = 0;
            var timer = null;

            function render() {
                track.style.transform = 'translateX(-' + current * 100 + '%)';

                if (pagination) {
                    pagination.querySelectorAll('.dot').forEach(function (dot, index) {
                        dot.classList.toggle('is-active', index === current);
                    });
                }
            }

            function go(index) {
                current = (index + slides.length) % slides.length;
                render();
            }

            if (pagination) {
                slides.forEach(function (slide, index) {
                    var dot = document.createElement('button');
                    dot.type = 'button';
                    dot.className = 'dot' + (index === 0 ? ' is-active' : '');
                    dot.setAttribute('aria-label', String(index + 1));
                    dot.addEventListener('click', function () { go(index); });
                    pagination.appendChild(dot);
                });
            }

            var prev = slider.querySelector('[data-hero-prev]');
            var next = slider.querySelector('[data-hero-next]');

            if (prev) {
                prev.addEventListener('click', function () { go(current - 1); });
            }
            if (next) {
                next.addEventListener('click', function () { go(current + 1); });
            }

            function start() {
                timer = window.setInterval(function () { go(current + 1); }, 6000);
            }

            slider.addEventListener('mouseenter', function () { window.clearInterval(timer); });
            slider.addEventListener('mouseleave', start);

            start();
        });
    }

    /**
     * Güniçi grafik zaman aralığı sekmeleri (varsayılan "Tümü").
     */
    function initRangeTabs() {
        document.querySelectorAll('.intraday-ranges').forEach(function (group) {
            var buttons = group.querySelectorAll('.range-btn');

            buttons.forEach(function (button) {
                button.addEventListener('click', function () {
                    buttons.forEach(function (other) {
                        var active = other === button;
                        other.classList.toggle('is-active', active);
                        other.setAttribute('aria-selected', active ? 'true' : 'false');
                    });
                });
            });
        });
    }

    /**
     * KAP paylaş çubuğu "bağlantıyı kopyala".
     */
    function initShareCopy() {
        document.querySelectorAll('[data-copy-link]').forEach(function (button) {
            button.addEventListener('click', function () {
                var link = button.getAttribute('data-copy-link');

                if (!link || !navigator.clipboard) {
                    return;
                }

                navigator.clipboard.writeText(link).then(function () {
                    button.classList.add('is-copied');
                }).catch(function () { /* yoksay */ });
            });
        });
    }

    function init() {
        initCookie();
        initMenu();
        initSearch();
        initScrollTop();
        initTabs();
        initHeroSlider();
        initRangeTabs();
        initShareCopy();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();