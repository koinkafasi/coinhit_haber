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
        var button = document.querySelector('.menu-button');
        var panel = document.querySelector('[data-mega-menu-panel]');

        if (!button || !panel) {
            return;
        }

        function close() {
            panel.classList.add('hidden');
            panel.setAttribute('hidden', '');
            document.body.classList.remove('bht-nav-open');
            document.body.classList.remove('overflow-hidden');
        }

        button.addEventListener('click', function () {
            panel.classList.remove('hidden');
            panel.removeAttribute('hidden');
            document.body.classList.add('bht-nav-open');
            document.body.classList.add('overflow-hidden');
        });

        var closeBtn = panel.querySelector('[data-menu-close]');
        if (closeBtn) {
            closeBtn.addEventListener('click', close);
        }

        panel.addEventListener('click', function (event) {
            if (event.target === panel) {
                close();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                close();
            }
        });

        panel.querySelectorAll('[data-menu-caret]').forEach(function (caret) {
            caret.addEventListener('click', function () {
                var item = caret.closest('.mega-menu-item');
                var children = item ? item.querySelector('.mega-menu-children') : null;

                if (!children) {
                    return;
                }

                var closed = children.classList.toggle('hidden');
                caret.setAttribute('aria-expanded', closed ? 'false' : 'true');
                caret.classList.toggle('is-open', !closed);
            });
        });
    }

    function initSearchWidget() {
        var trigger = document.querySelector('.search-widget-trigger');
        var overlay = document.querySelector('[data-search-overlay]');

        if (!trigger || !overlay) {
            return;
        }

        function close() {
            overlay.setAttribute('hidden', '');
        }

        trigger.addEventListener('click', function () {
            overlay.removeAttribute('hidden');
            var input = overlay.querySelector('input[name="q"]');
            if (input) {
                window.setTimeout(function () { input.focus(); }, 50);
            }
        });

        var closeBtn = overlay.querySelector('[data-search-close]');
        if (closeBtn) {
            closeBtn.addEventListener('click', close);
        }

        overlay.addEventListener('click', function (event) {
            if (event.target === overlay) {
                close();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                close();
            }
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

        document.querySelectorAll('[data-widget-type="marketGraphData"], [data-widget-type="market-graphic-tab"]').forEach(function (container) {
            bindTabs(container, '.market-widget-tabs > .tab-head > [data-tab]', '.market-widget-panels > [data-id]');

            container.querySelectorAll('.market-widget-panel').forEach(function (panel) {
                bindTabs(panel, '.market-subtab', '.market-widget-chart');
            });
        });

        document.querySelectorAll('[data-widget-type="mostRead"]').forEach(function (container) {
            bindTabs(container, '.most-read-tabhead > [data-tab]', '.most-read-panels > [data-id]');

            // CH-37: ana sayfa En Çok Okunanlar — referans `.tab-menu` yapısı
            // (tab-head pill'leri + tab-body panelleri) birebir bağlanır.
            bindTabs(container, '.tab-menu > .tab-head > [data-tab]', '.tab-menu > .tab-body > .tab-content[data-id]');
        });

        document.querySelectorAll('[data-widget-type="fastLinks"]').forEach(function (container) {
            bindTabs(container, '.tab-button[data-tab]', '.tab-content[data-id]');
        });

        document.querySelectorAll('[data-widget-type="trend-tabs"]').forEach(function (container) {
            bindTabs(container, '.trend-tabs > [data-tab]', '.trend-panels > [data-id]');
        });

        document.querySelectorAll('[data-widget-type="fund-classes"]').forEach(function (container) {
            // Referans birebir: 7 fon sınıfı dropdown (select-tab-options change → panel)
            var select = container.querySelector('.select-tab-options');
            if (select) {
                select.addEventListener('change', function () {
                    var key = this.value;
                    container.querySelectorAll('.select-tab-content').forEach(function (panel) {
                        panel.classList.toggle('hidden', panel.getAttribute('data-id') !== key);
                    });
                });
            }

            // Referans: her sınıf panelinde Yükselen/Düşen iki tablo, toggle istemci tarafı
            container.querySelectorAll('.fund-class-panel').forEach(function (panel) {
                bindTabs(panel, '.tab-menu > .tab-head > [data-tab]', '.tab-menu > .tab-body > .tab-content[data-id]');
            });
        });

        document.querySelectorAll('[data-widget-type="broadcast-days"]').forEach(function (container) {
            bindTabs(container, '.broadcast-day-tabs > [data-tab]', '.broadcast-day-panels > [data-id]');
        });

        document.querySelectorAll('[data-widget-type="marketDataLists"]').forEach(function (container) {
            bindTabs(container, '.home-piyasa-verileri .tab-head > [data-tab]', '.tab-body > [data-id]');
        });
    }

    /**
     * Hero main-slider — CH-24: referans Swiper (`[data-type="swiper"]`,
     * opsiyonlar data-swiper JSON).
     */
    function initSwiper() {
        if (!window.Swiper) {
            return;
        }

        document.querySelectorAll('[data-type="swiper"]').forEach(function (el) {
            var options = null;

            try {
                options = JSON.parse(el.getAttribute('data-swiper') || 'null');
            } catch (e) {
                options = null;
            }

            if (!options || typeof options !== 'object') {
                options = {};
            }

            new window.Swiper(el, options);
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
        document.addEventListener('click', function (event) {
            var button = event.target.closest ? event.target.closest('[data-copy-link]') : null;

            if (!button) {
                return;
            }

            var link = button.getAttribute('data-copy-link');

            if (!link || !navigator.clipboard) {
                return;
            }

            navigator.clipboard.writeText(link).then(function () {
                button.classList.add('is-copied');
            }).catch(function () { /* yoksay */ });
        });
    }

    /**
     * Makale "yazı boyutunu artır" (text-increase, spec §9, §10).
     */
    function initTextIncrease() {
        document.addEventListener('click', function (event) {
            var button = event.target.closest ? event.target.closest('[data-text-increase]') : null;

            if (!button) {
                return;
            }

            var article = button.closest('.article-detail');
            var wrapper = article ? article.querySelector('.article-wrapper') : null;

            if (!wrapper) {
                return;
            }

            var level = wrapper.classList.contains('text-size-xlarge')
                ? 2
                : (wrapper.classList.contains('text-size-large') ? 1 : 0);

            level = (level + 1) % 3;

            wrapper.classList.remove('text-size-large', 'text-size-xlarge');

            if (level === 1) {
                wrapper.classList.add('text-size-large');
            } else if (level === 2) {
                wrapper.classList.add('text-size-xlarge');
            }
        });
    }

    /**
     * Paylaş dropdown (hover CSS + dokunmatik için tıklama, spec §9).
     */
    function initShareDropdown() {
        document.addEventListener('click', function (event) {
            var target = event.target;
            var trigger = target.closest ? target.closest('.share-trigger') : null;

            if (trigger) {
                var dropdown = trigger.closest('.share-dropdown');
                var open = dropdown.classList.toggle('is-open');
                trigger.setAttribute('aria-expanded', open ? 'true' : 'false');

                document.querySelectorAll('.share-dropdown.is-open').forEach(function (other) {
                    if (other !== dropdown) {
                        other.classList.remove('is-open');
                        var otherTrigger = other.querySelector('.share-trigger');
                        if (otherTrigger) {
                            otherTrigger.setAttribute('aria-expanded', 'false');
                        }
                    }
                });

                return;
            }

            if (!target.closest || !target.closest('.share-menu')) {
                document.querySelectorAll('.share-dropdown.is-open').forEach(function (dropdown) {
                    dropdown.classList.remove('is-open');
                });
            }
        });
    }

    /**
     * İlgili Haberler karuseli (masaüstü yatay, 4sn otomatik; spec §9).
     */
    function initRelatedSlider() {
        document.querySelectorAll('[data-related-slider]').forEach(function (slider) {
            var track = slider.querySelector('[data-related-track]');
            if (!track) {
                return;
            }

            var section = slider.closest('.article-related') || slider.parentElement;
            var prevBtn = section ? section.querySelector('[data-related-prev]') : null;
            var nextBtn = section ? section.querySelector('[data-related-next]') : null;

            var slides = Array.prototype.slice.call(track.children);
            if (slides.length < 2) {
                return;
            }

            var index = 0;

            function step() {
                return (slides[0].getBoundingClientRect().width || 288) + 16;
            }

            function perView() {
                var width = slider.clientWidth || 0;
                return Math.max(1, Math.floor(width / step()));
            }

            function maxIndex() {
                return Math.max(0, slides.length - perView());
            }

            function updateButtons() {
                if (prevBtn) {
                    prevBtn.style.opacity = index <= 0 ? '0.4' : '1';
                    prevBtn.style.pointerEvents = index <= 0 ? 'none' : 'auto';
                }
                if (nextBtn) {
                    nextBtn.style.opacity = index >= maxIndex() ? '0.4' : '1';
                    nextBtn.style.pointerEvents = index >= maxIndex() ? 'none' : 'auto';
                }
            }

            function render() {
                if (window.innerWidth <= 767) {
                    track.style.transform = '';
                    return;
                }
                index = Math.max(0, Math.min(index, maxIndex()));
                track.style.transform = 'translateX(-' + (index * step()) + 'px)';
                updateButtons();
            }

            function next() {
                if (index < maxIndex()) {
                    index++;
                    render();
                }
            }

            function prev() {
                if (index > 0) {
                    index--;
                    render();
                }
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    next();
                });
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    prev();
                });
            }

            window.addEventListener('resize', function () {
                render();
            });

            render();
        });
    }

    /**
     * Sonsuz kaydırma — `/infinite/news/{id}` parçalarını IntersectionObserver
     * ile ekler; önceki/sonraki butonu YOK (spec §3, §9, §15.15).
     */
    function initInfiniteArticle() {
        var sentinel = document.querySelector('[data-infinite-sentinel]');
        var article = document.querySelector('[data-infinite-article]');

        if (!sentinel || !article) {
            return;
        }

        function readState(element) {
            try {
                return JSON.parse(element.getAttribute('data-it') || '{}');
            } catch (e) {
                return {};
            }
        }

        var state = readState(article);
        var loading = false;

        function load() {
            if (loading || !state || !state.next) {
                return;
            }

            loading = true;

            fetch(state.next, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (response) { return response.ok ? response.text() : Promise.reject(); })
                .then(function (html) {
                    var doc = new DOMParser().parseFromString(html, 'text/html');
                    var fresh = doc.querySelector('[data-infinite-article]');

                    if (!fresh) {
                        state.next = '';
                        return;
                    }

                    var node = document.importNode(fresh, true);
                    sentinel.parentNode.insertBefore(node, sentinel);
                    state = readState(node);

                    if (window.history && state.url) {
                        window.history.replaceState({}, '', state.url);
                    }
                    if (state.title) {
                        document.title = state.title;
                    }

                    if (typeof window.bhtLazyScan === 'function') {
                        window.bhtLazyScan();
                    }
                })
                .catch(function () { /* sonraki denemede tekrar */ })
                .then(function () { loading = false; });
        }

        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        load();
                    }
                });
            }, { rootMargin: '800px 0px' });

            observer.observe(sentinel);
        } else {
            window.addEventListener('scroll', function () {
                if (sentinel.getBoundingClientRect().top < window.innerHeight + 800) {
                    load();
                }
            }, { passive: true });
        }
    }

    /**
     * Döviz Çevirici (Bölüm 6): A↔B kur seçimi + Tutar + HESAPLA.
     */
    function initCurrencyConverter() {
        document.querySelectorAll('[data-currency-converter]').forEach(function (root) {
            var form = root.querySelector('[data-converter-form]');
            var from = root.querySelector('[data-converter-from]');
            var to = root.querySelector('[data-converter-to]');
            var amount = root.querySelector('[data-converter-amount]');
            var resultValue = root.querySelector('[data-converter-value]');
            var resultCurrency = root.querySelector('[data-converter-currency]');

            if (!form || !from || !to || !amount) {
                return;
            }

            var rates = {};
            var decimals = {};

            try { rates = JSON.parse(root.getAttribute('data-rates') || '{}'); } catch (e) { rates = {}; }
            try { decimals = JSON.parse(root.getAttribute('data-decimals') || '{}'); } catch (e) { decimals = {}; }

            function format(value, code) {
                var d = (typeof decimals[code] === 'number') ? decimals[code] : 4;

                try {
                    return value.toLocaleString('tr-TR', { minimumFractionDigits: d, maximumFractionDigits: d });
                } catch (e) {
                    return value.toFixed(d).replace('.', ',');
                }
            }

            function calculate() {
                var value = parseFloat(String(amount.value).replace(',', '.')) || 0;
                var fromRate = (typeof rates[from.value] === 'number') ? rates[from.value] : 1;
                var toRate = (typeof rates[to.value] === 'number') ? rates[to.value] : 1;
                var result = toRate > 0 ? (value * fromRate) / toRate : 0;

                if (resultValue) {
                    resultValue.textContent = format(result, to.value);
                }
                if (resultCurrency) {
                    resultCurrency.textContent = to.value;
                }
            }

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                calculate();
            });

            from.addEventListener('change', calculate);
            to.addEventListener('change', calculate);
            amount.addEventListener('input', calculate);
        });
    }

    /**
     * Altın Hesaplama (Bölüm 6, 8): Alış/Satış modu + A↔B enstrüman + Tutar.
     */
    function initGoldCalculator() {
        document.querySelectorAll('[data-gold-calculator]').forEach(function (root) {
            var form = root.querySelector('[data-gold-form]');
            var from = root.querySelector('[data-gold-from]');
            var to = root.querySelector('[data-gold-to]');
            var amount = root.querySelector('[data-gold-amount]');
            var valueEl = root.querySelector('[data-gold-value]');
            var currencyEl = root.querySelector('[data-gold-currency]');
            var modes = root.querySelectorAll('[data-gold-mode]');

            if (!form || !from || !to || !amount) {
                return;
            }

            var values = {};

            try { values = JSON.parse(root.getAttribute('data-values') || '{}'); } catch (e) { values = {}; }

            var activeMode = root.querySelector('[data-gold-mode].is-active');
            var mode = activeMode ? activeMode.getAttribute('data-gold-mode') : 'alis';

            function format(value) {
                try {
                    return value.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 4 });
                } catch (e) {
                    return value.toFixed(4).replace('.', ',');
                }
            }

            function calculate() {
                var table = values[mode] || {};
                var fromValue = (typeof table[from.value] === 'number') ? table[from.value] : 0;
                var toValue = (typeof table[to.value] === 'number') ? table[to.value] : 0;
                var value = parseFloat(String(amount.value).replace(',', '.')) || 0;
                var result = toValue > 0 ? (value * fromValue) / toValue : 0;

                if (valueEl) {
                    valueEl.textContent = format(result);
                }
                if (currencyEl && to.selectedIndex >= 0) {
                    currencyEl.textContent = to.options[to.selectedIndex].textContent;
                }
            }

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                calculate();
            });

            from.addEventListener('change', calculate);
            to.addEventListener('change', calculate);
            amount.addEventListener('input', calculate);

            modes.forEach(function (button) {
                button.addEventListener('click', function () {
                    modes.forEach(function (other) {
                        var active = other === button;
                        other.classList.toggle('is-active', active);
                        if (other.hasAttribute('aria-selected')) {
                            other.setAttribute('aria-selected', active ? 'true' : 'false');
                        }
                    });

                    mode = button.getAttribute('data-gold-mode');
                    calculate();
                });
            });
        });
    }

    /**
     * "Daha fazla yükle / Listele" — bir sonraki sayfayı çekip listeye ekler,
     * buton yoksa gizler (spec §10, §15.10).
     */
    function initLoadMore() {
        document.querySelectorAll('[data-load-more]').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();

                var url = button.getAttribute('data-next-url');
                var selector = button.getAttribute('data-container');
                var container = selector ? document.querySelector(selector) : null;

                if (!url || !container) {
                    return;
                }

                button.classList.add('is-loading');

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (response) { return response.ok ? response.text() : Promise.reject(); })
                    .then(function (html) {
                        var doc = new DOMParser().parseFromString(html, 'text/html');
                        var fresh = doc.querySelector(selector);

                        if (fresh) {
                            Array.prototype.slice.call(fresh.children).forEach(function (child) {
                                container.appendChild(document.importNode(child, true));
                            });
                        }

                        var freshButton = doc.querySelector('[data-load-more]');

                        if (freshButton && freshButton.getAttribute('data-next-url')) {
                            button.setAttribute('data-next-url', freshButton.getAttribute('data-next-url'));
                            button.setAttribute('href', freshButton.getAttribute('href') || freshButton.getAttribute('data-next-url'));
                            button.classList.remove('is-loading');
                        } else {
                            button.classList.remove('is-loading');
                            button.classList.add('is-done');
                        }

                        if (typeof window.bhtLazyScan === 'function') {
                            window.bhtLazyScan();
                        }
                    })
                    .catch(function () {
                        button.classList.remove('is-loading');
                    });
            });
        });
    }

    /**
     * Foto galeri slideshow (spec §4, §8 /fotohaber).
     */
    function initGallerySlideshow() {
        document.querySelectorAll('[data-gallery-slideshow]').forEach(function (root) {
            var slides = root.querySelectorAll('[data-slide]');
            var scope = root.closest('.gallery-detail') || document;
            var thumbs = scope.querySelectorAll('[data-gallery-thumb]');
            var counter = root.querySelector('[data-gallery-counter]');
            var current = 0;

            if (!slides.length) {
                return;
            }

            function render() {
                slides.forEach(function (slide, index) {
                    slide.classList.toggle('is-active', index === current);
                });

                thumbs.forEach(function (thumb, index) {
                    thumb.classList.toggle('is-active', index === current);
                });

                if (counter) {
                    counter.textContent = (current + 1) + ' / ' + slides.length;
                }
            }

            function go(index) {
                current = (index + slides.length) % slides.length;
                render();
            }

            var prev = root.querySelector('[data-gallery-prev]');
            var next = root.querySelector('[data-gallery-next]');

            if (prev) {
                prev.addEventListener('click', function () { go(current - 1); });
            }
            if (next) {
                next.addEventListener('click', function () { go(current + 1); });
            }

            thumbs.forEach(function (thumb) {
                thumb.addEventListener('click', function () {
                    go(parseInt(thumb.getAttribute('data-gallery-thumb'), 10) || 0);
                });
            });
        });
    }

    /**
     * Finance-slider mini grafikleri — referans gibi <canvas> cizimi.
     * Noktalar sunucu tarafinda data-spark JSON olarak gomulur; `down`/`up`
     * yonune gore renklendirilir ve yari saydam alan dolgusu eklenir.
     */
    function initFinanceSparklines() {
        document.querySelectorAll('[data-widget-type="finance-slider"] canvas.graphChart-wrapper[data-spark]').forEach(function (canvas) {
            var pts;
            try {
                pts = JSON.parse(canvas.getAttribute('data-spark') || 'null');
            } catch (e) {
                return;
            }
            if (!pts || !pts.length) {
                return;
            }
            var ctx = canvas.getContext && canvas.getContext('2d');
            if (!ctx) {
                return;
            }

            var w = canvas.width || 96;
            var h = canvas.height || 48;
            var pad = 2;
            var min = Math.min.apply(null, pts);
            var max = Math.max.apply(null, pts);
            var range = (max - min) || 1;
            var step = (w - pad * 2) / (pts.length - 1 || 1);
            var up = canvas.getAttribute('data-colordirection') !== '-1';
            // Referanstan olculen renkler (piksel analizi):
            //   yukari -> cizgi #3EC65F, asagi -> cizgi #BE2E29
            var color = up ? '#3EC65F' : '#BE2E29';

            ctx.clearRect(0, 0, w, h);

            // cizgi noktalari
            var xs = [], ys = [];
            pts.forEach(function (v, i) {
                var x = pad + i * step;
                var y = pad + (1 - (v - min) / range) * (h - pad * 2);
                xs.push(x);
                ys.push(y);
            });

            // alan dolgusu: cizgiden alta dogru dikey degrade (referans gibi)
            var grad = ctx.createLinearGradient(0, Math.min.apply(null, ys) - 4, 0, h);
            grad.addColorStop(0, up ? 'rgba(61,198,96,0.42)' : 'rgba(190,46,41,0.42)');
            grad.addColorStop(1, up ? 'rgba(213,241,213,0.05)' : 'rgba(232,170,170,0.05)');
            ctx.beginPath();
            ctx.moveTo(xs[0], ys[0]);
            xs.forEach(function (_x, i) {
                if (i > 0) { ctx.lineTo(xs[i], ys[i]); }
            });
            ctx.lineTo(w - pad, h);
            ctx.lineTo(pad, h);
            ctx.closePath();
            ctx.fillStyle = grad;
            ctx.fill();

            // ustteki cizgi
            ctx.beginPath();
            xs.forEach(function (_x, i) {
                if (i === 0) { ctx.moveTo(xs[i], ys[i]); }
                else { ctx.lineTo(xs[i], ys[i]); }
            });
            ctx.strokeStyle = color;
            ctx.lineWidth = 1.5;
            ctx.lineJoin = 'round';
            ctx.lineCap = 'round';
            ctx.stroke();

            bindFinanceTooltip(canvas, pts);
        });
    }

    /**
     * Finance mini grafik hover tooltip — mevcut canvas degismez, uzerine
     * bindirilen sip tarafi. Fare grafik uzerinde gezdirildiginde en yakin
     * noktanin zamanini ve degerini gosterir.
     */
    function bindFinanceTooltip(canvas, pts) {
        var times = null;
        try {
            times = JSON.parse(canvas.getAttribute('data-spark-times') || 'null');
        } catch (e) { /* yoksa indis kullanilir */ }

        var tt = document.createElement('div');
        tt.className = 'finance-chart-tooltip';
        tt.setAttribute('role', 'tooltip');
        document.body.appendChild(tt);

        var w = canvas.width || 96;
        var h = canvas.height || 48;
        var pad = 2;
        var step = (w - pad * 2) / (pts.length - 1 || 1);
        var up = canvas.getAttribute('data-colordirection') !== '-1';
        var color = up ? '#24A278' : '#BD1B2E';

        function labelOf(i) {
            if (times && times[i]) {
                return times[i];
            }
            return String(i + 1);
        }

        function fmt(v) {
            var num = typeof v === 'number' ? v : parseFloat(v);
            if (isNaN(num)) { return String(v); }
            return num.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 4 });
        }

        canvas.addEventListener('mousemove', function (e) {
            var r = canvas.getBoundingClientRect();
            var x = e.clientX - r.left;
            var idx = Math.round((x - pad) / step);
            idx = Math.max(0, Math.min(pts.length - 1, idx));
            tt.innerHTML =
                '<span class="chart-tip-time" style="color:' + color + '">' + (times && times[idx] ? times[idx] : '') + '</span>' +
                '<span class="chart-tip-value">' + fmt(pts[idx]) + '</span>';
            tt.style.display = 'block';
            tt.style.left = Math.round(e.clientX + 12) + 'px';
            tt.style.top = Math.round(e.clientY - 26) + 'px';
        });

        canvas.addEventListener('mouseleave', function () {
            tt.style.display = 'none';
        });
    }

    /**
     * Piyasalar widget Highcharts-görünümlü SSR grafiklerine hover tooltip.
     * Aksiyal SSR SVG statiktir (JS Highcharts yok); seri verisi gomulur ve
     * fare grafik üzerinde gezdirildiginde en yakin noktanin saati+degeri,
     * referans Highcharts tooltip'ine benzer sekilde gosterilir.
     */
    function initMarketWidgetTooltips() {
        document.querySelectorAll('.mw-chart-canvas[data-chart-values]').forEach(function (chartEl) {
            var values = null, times = null;
            try {
                values = JSON.parse(chartEl.getAttribute('data-chart-values') || '[]');
                times = JSON.parse(chartEl.getAttribute('data-chart-times') || '[]');
            } catch (e) {
                return;
            }
            if (!values || !values.length) {
                return;
            }
            var label = chartEl.getAttribute('data-chart-label') || '';

            var tt = document.createElement('div');
            tt.className = 'chart-tooltip';
            tt.setAttribute('role', 'tooltip');
            document.body.appendChild(tt);

            var svg = chartEl.querySelector('svg');

            function fmt(v) {
                var num = typeof v === 'number' ? v : parseFloat(v);
                if (isNaN(num)) { return String(v); }
                return num.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 4 });
            }

            function showTooltip(e) {
                var r = svg ? svg.getBoundingClientRect() : chartEl.getBoundingClientRect();
                if (!r.width) {
                    return;
                }
                var ratio = (e.clientX - r.left) / r.width;
                var idx = Math.round(ratio * (values.length - 1));
                idx = Math.max(0, Math.min(values.length - 1, idx));
                var parts = [];
                if (label) {
                    parts.push('<span class="chart-tip-label">' + label + '</span>');
                }
                if (times && times[idx]) {
                    parts.push('<span class="chart-tip-time">' + times[idx] + '</span>');
                }
                parts.push('<span class="chart-tip-value">' + fmt(values[idx]) + '</span>');
                tt.innerHTML = parts.join('');
                tt.style.display = 'block';
                tt.style.left = Math.round(e.clientX + 12) + 'px';
                tt.style.top = Math.round(e.clientY - 30) + 'px';
            }

            (svg || chartEl).addEventListener('mousemove', showTooltip);
            (svg || chartEl).addEventListener('mouseleave', function () {
                tt.style.display = 'none';
            });
        });
    }

    function init() {
        initCookie();
        initMenu();
        initSearchWidget();
        initSearch();
        initScrollTop();
        initTabs();
        initSwiper();
        initRangeTabs();
        initShareCopy();
        initTextIncrease();
        initShareDropdown();
        initCurrencyConverter();
        initGoldCalculator();
        initLoadMore();
        initGallerySlideshow();
        initRelatedSlider();
        initInfiniteArticle();
        initFinanceSparklines();
        initMarketWidgetTooltips();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();