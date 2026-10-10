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
     * @var array<string, array{name: string, category: string, author?: string, market?: bool, description?: string}>
     */
    public const CHANNELS = [
        'finansal-teknoloji' => [
            'name' => 'Finansal Teknoloji',
            'category' => 'finansal-teknoloji',
            'author' => 'sefer-yuksel',
            'description' => 'Dijital bankacılık, ödeme teknolojileri, fintech yatırımları ve merkez bankası dijital para çalışmalarına dair son gelişmeler.',
        ],
        'enerji' => [
            'name' => 'Enerji',
            'category' => 'enerji',
            'description' => 'Petrol, doğal gaz, elektrik ve yenilenebilir enerji piyasalarına ilişkin haberler ve analizler.',
        ],
        'ekonomik-veriler-ve-gundem' => [
            'name' => 'Ekonomik Veriler ve Gündem',
            'category' => 'ekonomik-veriler-ve-gundem',
            'author' => 'ayse-karagoz',
            'description' => 'Enflasyon, büyüme, istihdam ve dış ticaret verileri; ekonomi gündemine dair son gelişmeler.',
        ],
        'tarim' => [
            'name' => 'Tarım',
            'category' => 'tarim',
            'author' => 'irfan-donat',
            'description' => 'Tarım ürünleri, gıda fiyatları ve tarımsal desteklere ilişkin haberler ve Tarım Analiz köşesi.',
        ],
        'zirve' => [
            'name' => 'Zirve',
            'category' => 'zirve',
            'market' => true,
            'description' => 'Ekonomi ve finans dünyasının zirve etkinlikleri, paneller ve konuşmacı haberleri.',
        ],
        'siyaset' => [
            'name' => 'Siyaset',
            'category' => 'siyaset',
            'description' => 'Ekonomi politikalarını etkileyen siyasi gelişmeler ve gündem haberleri.',
        ],
        'sektor-haberleri' => [
            'name' => 'Sektör Haberleri',
            'category' => 'sektor-haberleri',
            'description' => 'Otomotiv, enerji, perakende, teknoloji ve sanayi sektörlerinden haberler.',
        ],
        'turkiye-ekonomisi' => [
            'name' => 'Türkiye Ekonomisi',
            'category' => 'turkiye-ekonomisi',
            'description' => 'Türkiye ekonomisine dair büyüme, enflasyon, istihdam ve dış denge haberleri.',
        ],
        'kuresel-piyasalar' => [
            'name' => 'Küresel Piyasalar',
            'category' => 'kuresel-piyasalar',
            'description' => 'Küresel borsalar, döviz, emtia ve tahvil piyasalarına ilişkin gelişmeler.',
        ],
        'emtia-enerji' => [
            'name' => 'Emtia & Enerji',
            'category' => 'emtia-enerji',
            'description' => 'Emtia ve enerji piyasalarını birlikte izleyen haber akışı.',
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

        $data = [
            'channel' => $channel,
            'title' => $config['name'],
            'description' => $config['description'] ?? null,
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
