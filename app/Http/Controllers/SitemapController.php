<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;
use App\Models\News;
use App\Models\ProgramEpisode;
use App\Support\Locale;
use Carbon\CarbonImmutable;
use Illuminate\Http\Response;

/**
 * Birebir sitemap altyapısı (master spec §11.2, §14.2).
 *
 * `sitemap.xml` bir `<sitemapindex>`'tir; haber parçaları, Google News,
 * piyasa, yazarlar, kategoriler ve 18 parçalı video sitemap'lerine işaret eder.
 */
class SitemapController extends Controller
{
    private const NEWS_PER_PART = 200;

    private const VIDEO_PARTS = 18;

    private const VIDEO_PER_PART = 200;

    public function index(): Response
    {
        $lastmod = $this->lastmod();

        $sitemaps = [
            $this->entry('/sitemap_google_news.xml', $lastmod),
            $this->entry('/sitemap_piyasa.xml', $lastmod),
            $this->entry('/sitemap_authors.xml', $lastmod),
            $this->entry('/sitemap_categories.xml', $lastmod),
            $this->entry('/sitemap_video.xml', $lastmod),
        ];

        // sitemap_news_{n}.xml — en yüksek parça önce (spec §14.2 sıralaması).
        for ($part = $this->newsPartCount() - 1; $part >= 0; $part--) {
            $sitemaps[] = $this->entry('/sitemap_news_'.$part.'.xml', $lastmod);
        }

        return $this->xml('seo.sitemap-index', ['sitemaps' => $sitemaps]);
    }

    /**
     * sitemap_news_{part}.xml — haber URL'leri, tr/en hreflang alternatifleriyle.
     */
    public function newsPart(int $part): Response
    {
        if ($part < 0 || $part >= $this->newsPartCount()) {
            abort(404);
        }

        $news = News::where('status', 'published')
            ->with('translations')
            ->orderByDesc('published_at')
            ->skip($part * self::NEWS_PER_PART)
            ->take(self::NEWS_PER_PART)
            ->get();

        $urls = $news->map(fn (News $item) => [
            'loc' => url(Locale::url($this->newsPath($item))),
            'lastmod' => $this->iso($item->modified_at ?? $item->published_at),
            'alternates' => $this->newsAlternates($item),
        ])->all();

        return $this->xml('seo.sitemap-news', ['urls' => $urls]);
    }

    /**
     * sitemap_google_news.xml — yalnızca son 48 saat, news + image namespace.
     */
    public function googleNews(): Response
    {
        $news = News::where('status', 'published')
            ->where('published_at', '>=', CarbonImmutable::now()->subHours(48))
            ->orderByDesc('published_at')
            ->take(1000)
            ->get();

        $urls = $news->map(fn (News $item) => [
            'loc' => url(Locale::url($this->newsPath($item))),
            'lastmod' => $this->iso($item->modified_at ?? $item->published_at),
            'publication_date' => $this->iso($item->published_at),
            'title' => (string) $item->title,
            'image' => url($item->image_og ?: ($item->image ?: '/img/og-default.png')),
            'image_title' => (string) $item->title,
            'image_caption' => (string) $item->spot,
        ])->all();

        return $this->xml('seo.sitemap-google-news', ['urls' => $urls]);
    }

    /**
     * sitemap_piyasa.xml — enstrüman/liste sayfaları (spec §11.1 instrument liste).
     */
    public function piyasa(): Response
    {
        $paths = [
            '/doviz', '/doviz/pariteler', '/doviz/sepet-kur', '/doviz/tl-euro',
            '/altin', '/altin-cevirici', '/emtia', '/emtia/petrol', '/emtia/bity', '/emtia/bidy',
            '/borsa', '/borsa/hisseler', '/borsa/en-cok', '/borsa/endeksler',
            '/borsa/dunya-borsalari', '/borsa/borsa-kapanis', '/alternatif',
            '/faiz-bono', '/tahvil/faiz',
            '/kripto', '/yatirim-fonlari', '/yatirim-fonlari/fon-karsilastirma',
            '/yatirim-fonlari/fon-portfoy-dagilim',
        ];

        return $this->xml('seo.sitemap-urlset', ['urls' => $this->fromPaths($paths)]);
    }

    /**
     * sitemap_authors.xml — yazar listesi + yorum arşivleri (spec §14.2).
     */
    public function authors(): Response
    {
        $urls = $this->fromPaths(['/yazarlar', '/gorus']);

        Author::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->each(function (Author $author) use (&$urls) {
                $urls[] = $this->url(Locale::url('/yorum/'.$author->slug.'/1'), null);
            });

        return $this->xml('seo.sitemap-urlset', ['urls' => $urls]);
    }

    /**
     * sitemap_categories.xml — kategori/kanal + statik/legal sayfalar.
     */
    public function categories(): Response
    {
        $paths = [
            '/', '/haberler', '/tumhaberler', '/sondakika', '/trendler', '/grafik-arsivi',
            '/fotohaber', '/halka-arz-takvimi', '/haberler/sektor-haberleri',
            '/finansal-teknoloji', '/enerji', '/ekonomik-veriler-ve-gundem', '/tarim',
            '/zirve', '/siyaset', '/haberler/turkiye-ekonomisi', '/haberler/kuresel-piyasalar',
            '/haberler/emtia-enerji',
            '/tv', '/tv/programlar', '/tv/frekans-listesi', '/yayinakisi', '/video', '/podcastler',
            '/kunye', '/bizeulasin', '/bizeulasin/izleyicitemsilcisi', '/kullanimkosullari',
            '/aydinlatma-metni', '/cerez-politikasi', '/privacy', '/biyografi',
        ];

        $urls = $this->fromPaths($paths);

        Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->each(function (Category $category) use (&$urls) {
                $urls[] = $this->url(Locale::url('/haberleri/'.$category->slug), null);
            });

        return $this->xml('seo.sitemap-urlset', ['urls' => $urls]);
    }

    /**
     * sitemap_video.xml — 18 parçalı `<sitemapindex>` (spec §14.2).
     */
    public function videoIndex(): Response
    {
        $lastmod = $this->iso(
            ProgramEpisode::orderByDesc('air_at')->value('air_at')
        );

        $sitemaps = [];

        for ($part = 0; $part < self::VIDEO_PARTS; $part++) {
            $sitemaps[] = $this->entry('/sitemap_video_'.$part.'.xml', $lastmod);
        }

        return $this->xml('seo.sitemap-index', ['sitemaps' => $sitemaps]);
    }

    /**
     * sitemap_video_{part}.xml — video:video namespace'li bölümler (vmcdn mp4).
     */
    public function videoPart(int $part): Response
    {
        if ($part < 0 || $part >= self::VIDEO_PARTS) {
            abort(404);
        }

        $episodes = ProgramEpisode::with('program')
            ->orderByDesc('air_at')
            ->skip($part * self::VIDEO_PER_PART)
            ->take(self::VIDEO_PER_PART)
            ->get();

        $urls = $episodes->map(function (ProgramEpisode $episode) {
            $series = $episode->program?->slug ?? 'haber';
            $hostSeries = $series === 'pandoranin-meraki' ? 'pandoranin-meraki' : $series;

            return [
                'loc' => url(Locale::url('/'.$hostSeries.'/video/'.$episode->slug.'/'.$episode->id)),
                'lastmod' => $this->iso($episode->air_at),
                'title' => (string) ($episode->display_title),
                'description' => (string) ($episode->program?->description ?: $episode->display_title),
                'thumbnail' => url($episode->thumbnail ?: '/img/og-default.png'),
                'content_loc' => (string) ($episode->video_url ?: ''),
                'player_loc' => url('/video/embed/'.$episode->id),
                'duration' => max(1, (int) $episode->duration_seconds),
                'publication_date' => $this->iso($episode->air_at),
            ];
        })->all();

        return $this->xml('seo.sitemap-video-part', ['urls' => $urls]);
    }

    /**
     * Haber parça sayısı (min 1). Spec'teki 261 parça yapısı veri hacmiyle ölçeklenir.
     */
    private function newsPartCount(): int
    {
        $total = News::where('status', 'published')->count();

        return max(1, (int) ceil($total / self::NEWS_PER_PART));
    }

    private function newsPath(News $news): string
    {
        return '/'.$news->getRawOriginal('slug').'-'.$news->news_id;
    }

    /**
     * @return array<int, array{hreflang: string, href: string}>
     */
    private function newsAlternates(News $news): array
    {
        $tr = $this->newsPath($news);

        $alternates = [
            ['hreflang' => 'tr', 'href' => url($tr)],
        ];

        $translation = $news->translation('en');

        if ($translation && $translation->getAttribute('slug')) {
            $alternates[] = [
                'hreflang' => 'en',
                'href' => url('/en/'.$translation->getAttribute('slug').'-'.$news->news_id),
            ];
        }

        $alternates[] = ['hreflang' => 'x-default', 'href' => url($tr)];

        return $alternates;
    }

    /**
     * @param  array<int, string>  $paths
     * @return array<int, array{loc: string, lastmod: string|null}>
     */
    private function fromPaths(array $paths): array
    {
        return array_map(fn (string $path) => $this->url(Locale::url($path), null), $paths);
    }

    /**
     * @return array{loc: string, lastmod: string|null}
     */
    private function url(string $loc, ?string $lastmod): array
    {
        return ['loc' => url($loc), 'lastmod' => $lastmod];
    }

    /**
     * @return array{loc: string, lastmod: string|null}
     */
    private function entry(string $path, ?string $lastmod): array
    {
        return ['loc' => url($path), 'lastmod' => $lastmod];
    }

    private function lastmod(): ?string
    {
        return $this->iso(
            News::where('status', 'published')->max('published_at')
        );
    }

    private function iso(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return CarbonImmutable::parse($value)->toIso8601String();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function xml(string $view, array $data): Response
    {
        return response()
            ->view($view, $data)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=1800, stale-while-revalidate=600');
    }
}
