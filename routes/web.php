<?php

use App\Http\Controllers\Admin\AuthorAdminController;
use App\Http\Controllers\Admin\ChartAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GalleryAdminController;
use App\Http\Controllers\Admin\KapAdminController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\NewsAdminController;
use App\Http\Controllers\Admin\ProgramAdminController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BreakingNewsController;
use App\Http\Controllers\ChartArchiveController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MarketDataController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\SearchController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/haberler', [NewsController::class, 'index'])->name('news.index');
Route::get('/sondakika', [BreakingNewsController::class, 'index'])->name('breaking.index');
Route::get('/grafik-arsivi', [ChartArchiveController::class, 'index'])->name('charts.index');
Route::get('/yazarlar', [AuthorController::class, 'index'])->name('authors.index');

Route::get('/arama', [SearchController::class, 'index'])->name('search.index');
Route::get('/arama/{q}', [SearchController::class, 'index'])
    ->where('q', '[^/]+')
    ->name('search.query');

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

Route::get('/{slug}-{newsId}', [NewsController::class, 'show'])
    ->where('slug', '[^/]+')
    ->where('newsId', '[0-9]+')
    ->name('news.show');