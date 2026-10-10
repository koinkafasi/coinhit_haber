{{-- Analitik & reklam ölçüm yığını (Bölüm 2.5, §11.4, §14.6, §16) --}}

{{-- Google Tag Manager (GTM-5XBP88) --}}
<script>
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});
var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';
j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-5XBP88');
</script>

{{-- Ogezio ölçüm seçenekleri --}}
<script>
window.__OGEZIO_ASYNC_OPTIONS = window.__OGEZIO_ASYNC_OPTIONS || {
    siteId: 'bloomberght',
    pageType: @json($pageType ?? 'home'),
    customDimension: window.customdimension || null
};
</script>

{{-- Gemius (tr.hit.gemius.pl) --}}
<script>
(function(w,d){
    var g=d.createElement('script');g.async=true;g.type='text/javascript';
    g.src=('https:'===d.location.protocol?'https://':'http://')+'tr.hit.gemius.pl/gemius.js';
    var s=d.getElementsByTagName('script')[0];s.parentNode.insertBefore(g,s);
})(window,document);
</script>

{{-- cbgAds motoru + Prebid/DFP köprüsü (Bölüm 2.5 / §17.2) --}}
<script src="{{ asset('adsbht/cbglout.js') }}" defer></script>