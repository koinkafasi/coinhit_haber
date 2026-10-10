<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BreakingNewsController;
use App\Http\Controllers\ChartArchiveController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\SearchController;
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

Route::get('/{slug}-{newsId}', [NewsController::class, 'show'])
    ->where('slug', '[^/]+')
    ->where('newsId', '[0-9]+')
    ->name('news.show');
