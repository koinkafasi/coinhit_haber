<?php

namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\ProgramEpisode;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Video serileri ve bölümleri (spec §3, §8, §14.3, §15.17 / /video).
 *
 * `/video` seri grid + son videolar + "Listele" lazy-load, `/video/{seri}`
 * seri sayfası, `/{seri}/video/{slug}/{id}` bölüm detayı ve `/video/embed/{id}`
 * oynatıcı embed'ini üretir.
 */
class VideoController extends Controller
{
    /**
     * Referans `/video` seri sırası (bloomberght.com/video). Klon verisinde
     * bulunmayan seriler atlanır; listede olmayanlar (ekstralar) sona eklenir.
     *
     * @var array<int, string>
     */
    private const REFERENCE_ORDER = [
        'asli-safakla-isin-asli',
        'acik-hava-sinemasi',
        'dort-kadin-bir-dunya',
        'tarih-keyfi',
        'pandoranin-meraki',
        'oyun-disi',
        'spor-kulubu',
        'iste-zirve',
        'yarinin-zekasi',
        'akustikhane',
        'haber',
        'finansal-teknoloji',
        'fon-saati',
    ];

    /**
     * Referans filtre hap kümesi — `/video` başlığı altındaki filtre swiper'ı
     * yalnız bu 8 seriyi gösterir (14 bölüm sayfada, 8 hap üstte).
     *
     * @var array<int, string>
     */
    private const FILTER_SLUGS = [
        'asli-safakla-isin-asli',
        'acik-hava-sinemasi',
        'tarih-keyfi',
        'dort-kadin-bir-dunya',
        'pandoranin-meraki',
        'spor-kulubu',
        'iste-zirve',
        'yarinin-zekasi',
    ];

    /**
     * /video — referans birebir: arka planlı "Video" başlığı + seri filtre
     * hapları + her seri için [kapsamlı video (type1) + 3 kenar video (type2)
     * + TÜMÜ linki].
     */
    public function index(Request $request): View
    {
        $order = array_flip(self::REFERENCE_ORDER);

        /** @var Collection<int, array{name: string, slug: string, url: string, episodes: Collection}> $series */
        $series = Program::series()
            ->where('is_active', true)
            ->get()
            ->sortBy(fn (Program $program): int => $order[$program->slug] ?? PHP_INT_MAX)
            ->values()
            ->map(fn (Program $program): array => [
                'name' => $program->name,
                'slug' => $program->slug,
                'url' => \App\Support\Locale::url('/video/'.$program->slug),
                'episodes' => $program->episodes()->orderByDesc('air_at')->take(4)->get(),
            ])
            ->reject(fn (array $s): bool => $s['episodes']->isEmpty())
            ->values();

        $filter = collect(self::FILTER_SLUGS)
            ->map(fn (string $slug): ?array => $series->firstWhere('slug', $slug))
            ->filter()
            ->values();

        return view('video.index', array_merge([
            'pageTitle' => __('media.video'),
            'series' => $series,
            'filter' => $filter,
        ], $this->layoutData()));
    }

    /**
     * /video/{seri} — seri sayfası (bölüm listesi + Listele).
     */
    public function series(Request $request, string $series): View
    {
        $program = Program::series()
            ->where('slug', $series)
            ->where('is_active', true)
            ->first();

        abort_unless($program, 404);

        $episodes = $program->episodes()
            ->orderByDesc('air_at')
            ->paginate(12)
            ->withQueryString();

        return view('video.series', array_merge([
            'pageTitle' => $program->name,
            'series' => $program,
            'episodes' => $episodes,
            'otherSeries' => Program::series()
                ->where('is_active', true)
                ->whereKeyNot($program->id)
                ->orderBy('sort_order')
                ->take(8)
                ->get(),
        ], $this->layoutData()));
    }

    /**
     * /{seri}/video/{slug}/{id} — bölüm detayı (video oynatıcı + ilgili videolar).
     */
    public function show(string $series, string $slug, string $id): View
    {
        $episode = ProgramEpisode::with('program')->find((int) $id);

        abort_unless($episode, 404);

        $related = ProgramEpisode::with('program')
            ->when($episode->program_id, fn ($query) => $query->where('program_id', $episode->program_id))
            ->whereKeyNot($episode->id)
            ->orderByDesc('air_at')
            ->take(6)
            ->get();

        if ($related->isEmpty()) {
            $related = ProgramEpisode::with('program')
                ->whereKeyNot($episode->id)
                ->orderByDesc('air_at')
                ->take(6)
                ->get();
        }

        return view('video.show', array_merge([
            'pageTitle' => $episode->display_title,
            'episode' => $episode,
            'series' => $episode->program,
            'seriesSlug' => $series,
            'slug' => $slug,
            'related' => $related,
        ], $this->layoutData()));
    }

    /**
     * /video/embed/{id} — sade oynatıcı (iframe/embed).
     */
    public function embed(string $id): View
    {
        $episode = ProgramEpisode::with('program')->find((int) $id);

        abort_unless($episode, 404);

        return view('video.embed', [
            'episode' => $episode,
        ]);
    }

    /**
     * @param  Collection<int, Program>  $series
     * @return array<int, array{label: string, url: string}>
     */
    private function categoryLinks(Collection $series): array
    {
        $links = [['label' => __('ui.trend_all'), 'url' => \App\Support\Locale::url('/video')]];

        foreach ($series as $item) {
            $links[] = ['label' => $item->name, 'url' => \App\Support\Locale::url('/video/'.$item->slug)];
        }

        return $links;
    }
}