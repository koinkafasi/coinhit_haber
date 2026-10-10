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
     * /video — hero + seri grid + filtre + son videolar (Listele lazy-load).
     */
    public function index(Request $request): View
    {
        $series = Program::series()
            ->where('is_active', true)
            ->withCount('episodes')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $videos = ProgramEpisode::with('program')
            ->orderByDesc('air_at')
            ->paginate(12)
            ->withQueryString();

        return view('video.index', array_merge([
            'pageTitle' => __('media.video'),
            'series' => $series,
            'videos' => $videos,
            'categories' => $this->categoryLinks($series),
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