<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Support\Locale;
use Illuminate\Http\Response;

/**
 * Çok dilli sitemap.xml (master spec §18.6): tr + en eşleşmeleri, hreflang dahil.
 */
class SitemapController extends Controller
{
    public function index(): Response
    {
        $staticPaths = ['/', '/haberler', '/sondakika', '/grafik-arsivi', '/yazarlar', '/borsa', '/doviz'];

        $urls = [];

        foreach ($staticPaths as $path) {
            $urls[] = [
                'loc' => url(Locale::url($path)),
                'tr' => url($path),
                'en' => url('/en'.($path === '/' ? '' : $path)),
            ];
        }

        News::where('status', 'published')
            ->orderByDesc('published_at')
            ->take(500)
            ->get()
            ->each(function (News $news) use (&$urls) {
                $tr = '/'.$news->getRawOriginal('slug').'-'.$news->news_id;

                $urls[] = [
                    'loc' => url(Locale::url($tr)),
                    'tr' => url($tr),
                    'en' => url($this->englishUrl($news) ?? $tr),
                ];
            });

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }

    private function englishUrl(News $news): ?string
    {
        $translation = $news->translation('en');

        if (! $translation || ! $translation->getAttribute('slug')) {
            return null;
        }

        return '/en/'.$translation->getAttribute('slug').'-'.$news->news_id;
    }
}
