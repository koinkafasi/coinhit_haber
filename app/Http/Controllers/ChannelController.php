<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsMarketWidget;
use App\Models\Author;
use App\Models\Category;
use App\Models\News;
use App\Models\ProgramEpisode;
use Illuminate\Contracts\View\View;

/**
 * Kanal/kategori sayfaları (spec §8 / §15.16) — ortak şablon:
 * haber grid → yazar köşesi (varsa) → TV videoları → uzun haber listesi.
 */
class ChannelController extends Controller
{
    use BuildsMarketWidget;

    /**
     * @var array<string, array{category: string, author?: string, market?: bool}>
     */
    public const CHANNELS = [
        'finansal-teknoloji' => [
            'category' => 'finansal-teknoloji',
            'author' => 'sefer-yuksel',
        ],
        'enerji' => [
            'category' => 'enerji',
        ],
        'ekonomik-veriler-ve-gundem' => [
            'category' => 'ekonomik-veriler-ve-gundem',
            'author' => 'ayse-karagoz',
        ],
        'tarim' => [
            'category' => 'tarim',
            'author' => 'irfan-donat',
        ],
        'zirve' => [
            'category' => 'zirve',
            'market' => true,
        ],
        'siyaset' => [
            'category' => 'siyaset',
        ],
        'sektor-haberleri' => [
            'category' => 'sektor-haberleri',
        ],
        'turkiye-ekonomisi' => [
            'category' => 'turkiye-ekonomisi',
        ],
        'kuresel-piyasalar' => [
            'category' => 'kuresel-piyasalar',
        ],
        'emtia-enerji' => [
            'category' => 'emtia-enerji',
        ],
    ];

    public function show(string $channel): View
    {
        $config = self::CHANNELS[$channel] ?? null;

        abort_unless($config, 404);

        $category = Category::where('slug', $config['category'])->first();

        $query = News::where('status', 'published')
            ->when($category !== null, fn ($builder) => $builder->where('category_id', $category->id))
            ->with(['category', 'author'])
            ->orderByDesc('published_at');

        $grid = (clone $query)->take(4)->get();
        $list = $query->paginate(12)->withQueryString();

        $author = null;
        $authorNews = collect();

        if (! empty($config['author'])) {
            $author = Author::where('slug', $config['author'])->first();

            if ($author !== null) {
                $authorNews = News::where('author_id', $author->id)
                    ->where('status', 'published')
                    ->orderByDesc('published_at')
                    ->take(4)
                    ->get();
            }
        }

        $videos = ProgramEpisode::with('program')
            ->orderByDesc('air_at')
            ->take(3)
            ->get();

        $keyBase = 'content.channel_'.str_replace('-', '_', $channel);

        $data = [
            'channel' => $channel,
            'title' => __($keyBase),
            'description' => __($keyBase.'_description'),
            'category' => $category,
            'grid' => $grid,
            'news' => $list,
            'author' => $author,
            'authorNews' => $authorNews,
            'videos' => $videos,
        ];

        if (! empty($config['market'])) {
            $data = array_merge($data, $this->marketWidgetData());
        }

        return view('channels.show', array_merge($data, $this->layoutData()));
    }
}
