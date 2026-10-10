<?php

use App\Http\Controllers\Admin\AuthorAdminController;
use App\Http\Controllers\Admin\ChartAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryAdminController;
use App\Http\Controllers\Admin\KapAdminController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\NewsAdminController;
use App\Http\Controllers\Admin\ProgramAdminController;
use App\Http\Controllers\AltinController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BorsaController;
use App\Http\Controllers\BreakingNewsController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\ChartArchiveController;
use App\Http\Controllers\DovizController;
use App\Http\Controllers\EmtiaController;
use App\Http\Controllers\FaizBonoController;
use App\Http\Controllers\FundController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\IpoCalendarController;
use App\Http\Controllers\KriptoController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\ManifestController;
use App\Http\Controllers\MarketDataController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PhotoGalleryController;
use App\Http\Controllers\PiyasalarController;
use App\Http\Controllers\PodcastController;
use App\Http\Controllers\RssController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\TrendController;
use App\Http\Controllers\TvController;
use App\Http\Controllers\VideoController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Ön yüz sayfa rotaları (locale'e duyarlı)
|--------------------------------------------------------------------------
|
| Aynı rota seti iki kez kaydedilir: tr kökte (prefixsiz, mevcut URL'ler
| korunur) ve en `/en` prefix'i ile. Master spec §18.2.
*/
$frontRoutes = function (): void {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/piyasalar', [PiyasalarController::class, 'index'])->name('markets.index');
    Route::get('/yuzde-hesaplama', [ToolController::class, 'percentage'])->name('tools.percentage');

    /*
    |----------------------------------------------------------------------
    | Haber listeleri & kategori/içerik sayfaları (Bölüm 8, 15.10-15.13)
    |----------------------------------------------------------------------
    */
    Route::get('/haberler', [NewsController::class, 'index'])->name('news.index');
    Route::get('/tumhaberler', [NewsController::class, 'all'])->name('news.all');
    Route::get('/infinite/news/{newsId}', [NewsController::class, 'infinite'])
        ->where('newsId', '[0-9]+')->name('news.infinite');
    Route::get('/sondakika', [BreakingNewsController::class, 'index'])->name('breaking.index');
    Route::get('/grafik-arsivi', [ChartArchiveController::class, 'index'])->name('charts.index');
    Route::get('/grafik-arsivi/{page}', [ChartArchiveController::class, 'index'])
        ->where('page', '[0-9]+')->name('charts.page');
    Route::get('/grafik-arsivi/{slug}-{id}', [ChartArchiveController::class, 'show'])
        ->where('slug', '[^/]+')
        ->where('id', '[0-9]+')->name('charts.show');
    Route::get('/trendler', [TrendController::class, 'index'])->name('trends.index');
    Route::get('/trendler/{slug}', [TrendController::class, 'show'])
        ->where('slug', '[A-Za-z0-9\-]+')->name('trends.show');
    Route::get('/yazarlar', [AuthorController::class, 'index'])->name('authors.index');
    Route::get('/gorus', [AuthorController::class, 'opinions'])->name('authors.opinions');
    Route::get('/yorum/{author}/{segment}', [AuthorController::class, 'archive'])
        ->where('author', '[A-Za-z0-9\-]+')
        ->where('segment', '[A-Za-z0-9\-]+')
        ->name('authors.archive');

    Route::get('/fotohaber', [PhotoGalleryController::class, 'index'])->name('photo.index');
    Route::get('/fotohaber/{slug}-{id}', [PhotoGalleryController::class, 'show'])
        ->where('slug', '[^/]+')
        ->where('id', '[0-9]+')
        ->name('photo.show');

    Route::get('/halka-arz-takvimi', [IpoCalendarController::class, 'index'])->name('ipo.index');

    /*
    |----------------------------------------------------------------------
    | Yasal statik sayfalar (Bölüm 3, 11.6, 14.4) — footer'dan erişilir
    |----------------------------------------------------------------------
    */
    Route::get('/kunye', [LegalController::class, 'show'])->defaults('slug', 'kunye')->name('legal.kunye');
    Route::get('/bizeulasin', [LegalController::class, 'show'])->defaults('slug', 'bizeulasin')->name('legal.contact');
    Route::post('/bizeulasin', [LegalController::class, 'contact'])->name('legal.contact.submit');
    Route::get('/bizeulasin/izleyicitemsilcisi', [LegalController::class, 'show'])
        ->defaults('slug', 'bizeulasin/izleyicitemsilcisi')->name('legal.izleyici');
    Route::get('/kullanimkosullari', [LegalController::class, 'show'])->defaults('slug', 'kullanimkosullari')->name('legal.kullanim');
    Route::get('/aydinlatma-metni', [LegalController::class, 'show'])->defaults('slug', 'aydinlatma-metni')->name('legal.aydinlatma');
    Route::get('/cerez-politikasi', [LegalController::class, 'show'])->defaults('slug', 'cerez-politikasi')->name('legal.cerez');
    Route::get('/privacy', [LegalController::class, 'show'])->defaults('slug', 'privacy')->name('legal.privacy');
    Route::get('/biyografi', [LegalController::class, 'show'])->defaults('slug', 'biyografi')->name('legal.biyografi');

    Route::get('/arama', [SearchController::class, 'index'])->name('search.index');
    Route::get('/arama/{q}', [SearchController::class, 'index'])
        ->where('q', '[^/]+')
        ->name('search.query');
    Route::get('/haberleri/{tag}', [SearchController::class, 'tag'])
        ->where('tag', '[A-Za-z0-9\-]+')
        ->name('search.tag');

    /*
    |----------------------------------------------------------------------
    | Kanal/kategori sayfaları (Bölüm 8 / 15.16 — ortak şablon)
    |----------------------------------------------------------------------
    */
    Route::get('/finansal-teknoloji', [ChannelController::class, 'show'])->defaults('channel', 'finansal-teknoloji')->name('channel.finansal-teknoloji');
    Route::get('/enerji', [ChannelController::class, 'show'])->defaults('channel', 'enerji')->name('channel.enerji');
    Route::get('/ekonomik-veriler-ve-gundem', [ChannelController::class, 'show'])->defaults('channel', 'ekonomik-veriler-ve-gundem')->name('channel.ekonomik-veriler-ve-gundem');
    Route::get('/tarim', [ChannelController::class, 'show'])->defaults('channel', 'tarim')->name('channel.tarim');
    Route::get('/zirve', [ChannelController::class, 'show'])->defaults('channel', 'zirve')->name('channel.zirve');
    Route::get('/siyaset', [ChannelController::class, 'show'])->defaults('channel', 'siyaset')->name('channel.siyaset');
    Route::get('/haberler/sektor-haberleri', [ChannelController::class, 'show'])->defaults('channel', 'sektor-haberleri')->name('channel.sektor-haberleri');
    Route::get('/haberler/turkiye-ekonomisi', [ChannelController::class, 'show'])->defaults('channel', 'turkiye-ekonomisi')->name('channel.turkiye-ekonomisi');
    Route::get('/haberler/kuresel-piyasalar', [ChannelController::class, 'show'])->defaults('channel', 'kuresel-piyasalar')->name('channel.kuresel-piyasalar');
    Route::get('/haberler/emtia-enerji', [ChannelController::class, 'show'])->defaults('channel', 'emtia-enerji')->name('channel.emtia-enerji');

    /*
    |----------------------------------------------------------------------
    | Borsa bölümü (Bölüm 8, 15.3 — hisse/endeks/KAP/dünya/alternatif)
    |----------------------------------------------------------------------
    */
    Route::get('/borsa', [BorsaController::class, 'index'])->name('borsa.index');
    Route::get('/borsa/hisseler/kap-haberleri', [BorsaController::class, 'kapNews'])->name('borsa.kap-news');
    Route::get('/borsa/hisseler/{letter}', [BorsaController::class, 'stocks'])
        ->where('letter', '[A-Za-z]')->name('borsa.stocks.letter');
    Route::get('/borsa/hisseler', [BorsaController::class, 'stocks'])->name('borsa.stocks');
    Route::get('/borsa/en-cok', [BorsaController::class, 'most'])->name('borsa.most');
    Route::get('/borsa/dunya-borsalari', [BorsaController::class, 'world'])->name('borsa.world');
    Route::get('/borsa/endeksler', [BorsaController::class, 'indices'])->name('borsa.indices');
    Route::get('/borsa/endeks/{slug}', [BorsaController::class, 'indexDetail'])
        ->where('slug', '[A-Za-z0-9\-]+')->name('borsa.index-detail');
    Route::get('/borsa/borsa-kapanis/{letter}', [BorsaController::class, 'closing'])
        ->where('letter', '[A-Za-z]')->name('borsa.closing.letter');
    Route::get('/borsa/borsa-kapanis', [BorsaController::class, 'closing'])->name('borsa.closing');
    Route::get('/alternatif', [BorsaController::class, 'alternative'])->name('borsa.alternative');
    Route::get('/borsa/hisse/{company}/kap-haberi/{kapId}', [BorsaController::class, 'kapDetail'])
        ->where('kapId', '[0-9]+')->name('borsa.kap-detail');
    Route::get('/borsa/hisse/{company}', [BorsaController::class, 'stock'])
        ->where('company', '[A-Za-z0-9\-]+')->name('borsa.stock');

    /*
    |----------------------------------------------------------------------
    | Döviz & Parite bölümü (Bölüm 5, 6, 8, 15.4 — döviz/parite + çevirici)
    |----------------------------------------------------------------------
    */
    Route::get('/doviz', [DovizController::class, 'index'])->name('doviz.index');
    Route::get('/doviz/pariteler', [DovizController::class, 'parities'])->name('doviz.parities');
    Route::get('/doviz/sepet-kur', [DovizController::class, 'sepetKur'])->name('doviz.sepet-kur');
    Route::get('/doviz/tl-euro', [DovizController::class, 'tlEuro'])->name('doviz.tl-euro');
    Route::get('/doviz/parite/{slug}', [DovizController::class, 'parityDetail'])
        ->where('slug', '[A-Za-z0-9\-]+')->name('doviz.parity-detail');
    Route::get('/doviz/{slug}', [DovizController::class, 'detail'])
        ->where('slug', '[A-Za-z0-9\-]+')->name('doviz.detail');
    Route::get('/doviz-cevirici', [DovizController::class, 'converter'])->name('doviz.converter');

    /*
    |----------------------------------------------------------------------
    | Faiz-Bono bölümü (Bölüm 5, 8, 15.7 — tahvil/repo/eurobond)
    |----------------------------------------------------------------------
    */
    Route::get('/faiz-bono', [FaizBonoController::class, 'index'])->name('faiz-bono.index');
    Route::get('/tahvil/faiz', [FaizBonoController::class, 'tahvilFaiz'])->name('faiz-bono.tahvil-faiz');
    Route::get('/tahvil/{slug}', [FaizBonoController::class, 'bondDetail'])
        ->where('slug', '[A-Za-z0-9\-]+')->name('faiz-bono.bond');
    Route::get('/repo/{slug}', [FaizBonoController::class, 'repoDetail'])
        ->where('slug', '[A-Za-z0-9\-]+')->name('faiz-bono.repo');
    Route::get('/eurobond/{slug}', [FaizBonoController::class, 'eurobondDetail'])
        ->where('slug', '[A-Za-z0-9\-]+')->name('faiz-bono.eurobond');

    /*
    |----------------------------------------------------------------------
    | Kripto bölümü (Bölüm 5, 8, 14.5, 15.6 — A-Z filtre + tablo + widget)
    |----------------------------------------------------------------------
    */
    Route::get('/kripto', [KriptoController::class, 'index'])->name('kripto.index');
    Route::get('/kripto/liste/{harf}', [KriptoController::class, 'index'])
        ->where('harf', '[A-Za-z]')->name('kripto.list');

    /*
    |----------------------------------------------------------------------
    | Altın & Emtia bölümü (Bölüm 5, 6, 8, 15.5 — altın/emtia + hesaplama)
    |----------------------------------------------------------------------
    */
    Route::get('/altin', [AltinController::class, 'index'])->name('altin.index');
    Route::get('/altin-cevirici', [AltinController::class, 'calculator'])->name('altin.calculator');
    Route::get('/altin/{slug}', [AltinController::class, 'detail'])
        ->where('slug', '[A-Za-z0-9\-]+')->name('altin.detail');
    Route::get('/emtia', [EmtiaController::class, 'index'])->name('emtia.index');
    Route::get('/emtia/{slug}', [EmtiaController::class, 'detail'])
        ->where('slug', '[A-Za-z0-9\-]+')->name('emtia.detail');

    /*
    |----------------------------------------------------------------------
    | Yatırım Fonları bölümü (Bölüm 6, 8, 15.8 — fonlar + araç sayfaları)
    |----------------------------------------------------------------------
    */
    Route::get('/yatirim-fonlari', [FundController::class, 'index'])->name('fund.index');
    Route::get('/yatirim-fonlari/fon-karsilastirma', [FundController::class, 'compare'])->name('fund.compare');
    Route::get('/yatirim-fonlari/fon-portfoy-dagilim', [FundController::class, 'portfolio'])->name('fund.portfolio');
    Route::get('/yatirim-fonlari/{slug}', [FundController::class, 'show'])
        ->where('slug', '[A-Za-z0-9\-]+')->name('fund.show');

    /*
    |----------------------------------------------------------------------
    | TV / Yayın Akışı / Video / Podcast bölümü (Bölüm 8, §14.3, §15.9)
    |----------------------------------------------------------------------
    */
    Route::get('/tv', [TvController::class, 'index'])->name('tv.index');
    Route::get('/tv/programlar', [TvController::class, 'programs'])->name('tv.programs');
    Route::get('/tv/frekans-listesi', [TvController::class, 'frequencies'])->name('tv.frequencies');
    Route::get('/tv/program/{slug}', [TvController::class, 'program'])
        ->where('slug', '[A-Za-z0-9\-]+')->name('tv.program');
    Route::get('/yayinakisi', [TvController::class, 'schedule'])->name('tv.schedule');

    Route::get('/podcastler', [PodcastController::class, 'index'])->name('podcast.index');
    Route::get('/podcastler/{slug}', [PodcastController::class, 'show'])
        ->where('slug', '[A-Za-z0-9\-]+')->name('podcast.show');

    Route::get('/video', [VideoController::class, 'index'])->name('video.index');
    Route::get('/video/embed/{id}', [VideoController::class, 'embed'])
        ->where('id', '[0-9]+')->name('video.embed');
    Route::get('/video/{series}', [VideoController::class, 'series'])
        ->where('series', '[A-Za-z0-9\-]+')->name('video.series');
    Route::get('/pandoranin-meraki', [VideoController::class, 'series'])
        ->defaults('series', 'pandoranin-meraki')->name('video.pandora');
    Route::get('/{series}/video/{slug}/{id}', [VideoController::class, 'show'])
        ->where('series', '[A-Za-z0-9\-]+')
        ->where('slug', '[A-Za-z0-9\-]+')
        ->where('id', '[0-9]+')->name('video.show');

    /*
    |----------------------------------------------------------------------
    | Legacy makale kalıbı → yeni `/{slug}-{id}` (301, Bölüm 11.2/14.1)
    |----------------------------------------------------------------------
    */
    Route::get('/haberler/haber/{newsId}-{slug}', [NewsController::class, 'legacy'])
        ->where('newsId', '[0-9]+')
        ->where('slug', '[^/]+')->name('news.legacy');

    /*
    |----------------------------------------------------------------------
    | Haber detay (catch-all) — grubun en sonunda kalmalı.
    |----------------------------------------------------------------------
    */
    Route::get('/{slug}-{newsId}', [NewsController::class, 'show'])
        ->where('slug', '[^/]+')
        ->where('newsId', '[0-9]+')
        ->name('news.show');
};

/*
|--------------------------------------------------------------------------
| SEO altyapısı (Bölüm 11.2, §14.2): sitemap, RSS, PWA manifest
|--------------------------------------------------------------------------
| Catch-all haber rotasından önce kaydedilir.
*/
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap_google_news.xml', [SitemapController::class, 'googleNews'])->name('sitemap.google-news');
Route::get('/sitemap_piyasa.xml', [SitemapController::class, 'piyasa'])->name('sitemap.piyasa');
Route::get('/sitemap_authors.xml', [SitemapController::class, 'authors'])->name('sitemap.authors');
Route::get('/sitemap_categories.xml', [SitemapController::class, 'categories'])->name('sitemap.categories');
Route::get('/sitemap_video.xml', [SitemapController::class, 'videoIndex'])->name('sitemap.video');
Route::get('/sitemap_video_{part}.xml', [SitemapController::class, 'videoPart'])
    ->where('part', '[0-9]+')->name('sitemap.video.part');
Route::get('/sitemap_news_{part}.xml', [SitemapController::class, 'newsPart'])
    ->where('part', '[0-9]+')->name('sitemap.news.part');

Route::get('/rss', [RssController::class, 'index'])->name('rss.index');
Route::get('/rss/tum-haberler.xml', [RssController::class, 'all'])->name('rss.all');
Route::get('/rss/kategori/{slug}.xml', [RssController::class, 'category'])
    ->where('slug', '[A-Za-z0-9\-]+')->name('rss.category');
Route::get('/rss/yazar/{slug}.xml', [RssController::class, 'author'])
    ->where('slug', '[A-Za-z0-9\-]+')->name('rss.author');

Route::get('/manifest.webmanifest', [ManifestController::class, 'show'])->name('manifest');

Route::middleware(SetLocale::class)->group($frontRoutes);
Route::prefix('en')->name('en.')->middleware(SetLocale::class)->group($frontRoutes);

/*
|--------------------------------------------------------------------------
| Piyasa veri katmanı (Bölüm 5, 10, 14.1)
|--------------------------------------------------------------------------
| Canlı pano polling uçları. robots.txt'te Disallow edilen iç veri
| servislerinin klon karşılıklarıdır.
*/
Route::get('/piyasa/veri', [MarketDataController::class, 'snapshot'])->name('market.snapshot');
Route::get('/finance-slider-data', [MarketDataController::class, 'slider'])->name('market.slider');

/*
|--------------------------------------------------------------------------
| Görsel ölçekleme (geoim deseninin lokal karşılığı — Bölüm 4, 12)
|--------------------------------------------------------------------------
*/
Route::get('/gorsel/{folder}/{file}', [ImageController::class, 'show'])
    ->where('folder', '[A-Za-z0-9_\-\.]+')
    ->where('file', '[A-Za-z0-9_\-\.]+')
    ->name('image.show');

/*
|--------------------------------------------------------------------------
| Yönetim paneli (Bölüm 4 — editör/içerik yönetimi)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [LoginController::class, 'show'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.attempt');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::middleware(EnsureAdmin::class)->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');

        Route::resource('news', NewsAdminController::class)->except('show');
        Route::resource('authors', AuthorAdminController::class)->except('show');
        Route::resource('programs', ProgramAdminController::class)->except('show');
        Route::resource('kap', KapAdminController::class)->except('show');
        Route::resource('galleries', GalleryAdminController::class)->except('show');
        Route::resource('charts', ChartAdminController::class)->except('show');

        Route::post('programs/{program}/episodes', [ProgramAdminController::class, 'storeEpisode'])->name('programs.episodes.store');
        Route::delete('programs/{program}/episodes/{episode}', [ProgramAdminController::class, 'destroyEpisode'])->name('programs.episodes.destroy');

        Route::post('galleries/{gallery}/items', [GalleryAdminController::class, 'storeItem'])->name('galleries.items.store');
        Route::delete('galleries/{gallery}/items/{item}', [GalleryAdminController::class, 'destroyItem'])->name('galleries.items.destroy');
    });
});
