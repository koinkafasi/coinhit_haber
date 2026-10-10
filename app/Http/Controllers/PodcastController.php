<?php

namespace App\Http\Controllers;

use App\Models\Podcast;
use Illuminate\Contracts\View\View;

/**
 * Podcast bölümü (spec §4, §8).
 *
 * `/podcastler/` 12 seri kartı, `/podcastler/{slug}` epizot listesi (oynatılabilir).
 */
class PodcastController extends Controller
{
    /**
     * /podcastler/ — podcast serileri.
     */
    public function index(): View
    {
        $podcasts = Podcast::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('podcast.index', array_merge([
            'pageTitle' => 'Podcast',
            'podcasts' => $podcasts,
        ], $this->layoutData()));
    }

    /**
     * /podcastler/{slug} — seri epizot listesi.
     */
    public function show(string $slug): View
    {
        $podcast = Podcast::where('slug', $slug)->where('is_active', true)->first();

        abort_unless($podcast, 404);

        $episodes = $podcast->episodes()
            ->orderByDesc('published_at')
            ->get();

        $others = Podcast::where('is_active', true)
            ->whereKeyNot($podcast->id)
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        return view('podcast.show', array_merge([
            'pageTitle' => $podcast->title,
            'podcast' => $podcast,
            'episodes' => $episodes,
            'others' => $others,
        ], $this->layoutData()));
    }
}