/**
 * Bloomberg HT — lazy görsel yükleyici (blazy deseni).
 *
 * Görseller `transparent.gif` ile başlar; gerçek URL `data-src` (ve isteğe
 * bağlı `data-srcset` / `data-sizes`) içinde taşınır. Görünür alana girince
 * `data-src` → `src` geçişi yapılır (Bölüm 1, 16).
 */
(function () {
    'use strict';

    function reveal(img) {
        if (img.dataset.src && img.getAttribute('src') !== img.dataset.src) {
            img.setAttribute('src', img.dataset.src);
        }

        if (img.dataset.srcset) {
            img.setAttribute('srcset', img.dataset.srcset);
        }

        if (img.dataset.sizes) {
            img.setAttribute('sizes', img.dataset.sizes);
        }

        img.classList.add('b-lazy-loaded');

        img.addEventListener('load', function () {
            img.classList.add('b-lazy-loaded');
        }, { once: true });
    }

    function init() {
        var images = Array.prototype.slice.call(
            document.querySelectorAll('img[data-src], img.b-lazy')
        );

        if (!images.length) {
            return;
        }

        if (!('IntersectionObserver' in window)) {
            images.forEach(reveal);
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    reveal(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { rootMargin: '200px 0px' });

        images.forEach(function (img) {
            observer.observe(img);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();