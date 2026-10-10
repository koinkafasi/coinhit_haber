/*
 * cbgAds — reklam motoru iskeleti (Bölüm 2.5 / §14.6 / §17.2 birebir).
 *
 * Canlı sitedeki `cbgAds` motorunun klon karşılığıdır: zone keşfi,
 * AdsHeader/AdsInline/AdsBody yerleşimleri, pageskin sol/sağ, mobil sticky
 * (zone 2280, 30 sn refresh) ve Prebid/DFP köprüsü.
 */
(function (window, document) {
    'use strict';

    var REFRESH_MS = 30000;

    var cbgAds = {
        VERSION: '127',
        zones: {},
        prebid: null,
        googletag: null,

        /* Yerleşime göre zone haritası (spec §14.6). */
        zoneMap: {
            home: { pageskin: ['1251', '1252'], masthead: null },
            article: { pageskin: ['2126', '2127'], masthead: ['1393', '1394'] },
            static: { pageskin: ['1253', '1254'], masthead: ['1264', '1394'] },
            markets: { pageskin: ['2228', '2229', '2230', '2267'], masthead: null },
            subcategory: { pageskin: ['23041', '23031'], masthead: ['23022', '2199'] }
        },

        init: function () {
            this.collect();
            this.header();
            this.inline();
            this.body();
            this.pageskin();
            this.sticky();
            this.refresh();
        },

        /* DOM'daki tüm `[data-zone-id]` yuvalarını topla. */
        collect: function () {
            var nodes = document.querySelectorAll('[data-zone-id]');
            for (var i = 0; i < nodes.length; i++) {
                var el = nodes[i];
                var id = el.getAttribute('data-zone-id');
                if (!id) { continue; }
                (this.zones[id] = this.zones[id] || []).push(el);
            }
        },

        AdsHeader: function () { this.header(); },
        AdsInline: function () { this.inline(); },
        AdsBody: function () { this.body(); },

        header: function () {
            this.render('masthead');
        },

        inline: function () {
            this.render('ad-body');
        },

        body: function () {
            this.render('billboard');
        },

        /* Pageskin sol/sağ sticky ad (Bölüm 17.2). */
        pageskin: function () {
            this.attach('ciner-pageskin-left', 'pageskin-left');
            this.attach('ciner-pageskin-right', 'pageskin-right');
        },

        /* Mobil sticky alt bant — zone 2280, 30 sn refresh. */
        sticky: function () {
            var sticky = document.getElementById('zone_auto_refresh_sticky');
            if (!sticky) { return; }
            this.renderZone(sticky);
        },

        attach: function (id, cls) {
            var el = document.getElementById(id);
            if (el) { el.classList.add(cls, 'cbg-ads-attached'); }
        },

        render: function (variant) {
            var nodes = document.querySelectorAll('.ads-zone[data-device][data-zone-id]');
            for (var i = 0; i < nodes.length; i++) {
                var node = nodes[i];
                var block = node.closest ? node.closest('.ad-zone-' + variant) : null;
                if (block || variant === 'masthead') {
                    this.renderZone(node);
                }
            }
        },

        /* Tek bir zone yuvasını işaretle (DFP/Prebid yüklenince burası dolar). */
        renderZone: function (node) {
            if (!node || node.dataset.cbgRendered === '1') { return; }
            node.dataset.cbgRendered = '1';
            node.classList.remove('lazy-init');
            node.classList.add('cbg-ads-zone');

            if (window.googletag && window.googletag.cmd) {
                window.googletag.cmd.push(function () {
                    window.googletag.display(node.id || node.getAttribute('data-zone-id'));
                });
            }
        },

        RefreshZone: function (selector) {
            var node = typeof selector === 'string' ? document.querySelector(selector) : selector;
            if (node) { delete node.dataset.cbgRendered; this.renderZone(node); }
        },

        RefreshZoneEx: function (node) { this.RefreshZone(node); },

        /* `.ads-zone:not(.not-refreshed)` yuvalarını periyodik yenile. */
        refresh: function () {
            var self = this;
            window.setInterval(function () {
                var nodes = document.querySelectorAll('.ads-zone:not(.not-refreshed)');
                for (var i = 0; i < nodes.length; i++) { self.RefreshZone(nodes[i]); }
            }, REFRESH_MS);
        }
    };

    window.cbgAds = cbgAds;
})(window, document);