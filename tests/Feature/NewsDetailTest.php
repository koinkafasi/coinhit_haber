<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsDetailTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::factory()->create(['name' => 'ENERJİ', 'slug' => 'enerji']);
    }

    private function makeArticle(array $attributes = []): News
    {
        return News::factory()->create(array_merge([
            'category_id' => $this->category->id,
            'news_id' => 3799002,
            'slug' => 'motorinde-zam',
            'title' => 'Motorinde zam benzinde indirim',
            'spot' => "- Piyasa katılımcıları gelişmeyi takip ediyor.\n- Uzmanlar değerlendirme yaptı.",
            'body' => "Enflasyon verileri açıklandı.\n\nPiyasa beklentileri şekillendi.",
            'keywords' => ['enflasyon'],
            'tags' => ['enerji'],
            'status' => 'published',
        ], $attributes));
    }

    public function test_article_detail_renders_seo_and_layout(): void
    {
        $this->makeArticle();
        $this->makeArticle(['news_id' => 3799001, 'slug' => 'ilgili-haber', 'title' => 'İlgili enerji haberi']);

        $response = $this->get('/motorinde-zam-3799002');

        $response->assertOk();
        $response->assertSee('Motorinde zam benzinde indirim');
        $response->assertSee('| ENERJİ haberleri', false);

        // NewsArticle + ek JSON-LD tipleri
        $response->assertSee('NewsArticle', false);
        $response->assertSee('articleSection', false);
        $response->assertSee('ImageObject', false);
        $response->assertSee('"@type":"Person"', false);
        $response->assertSee('/kunye', false);
        $response->assertSee('BreadcrumbList', false);
        $response->assertSee('/enerji', false);

        // Görünüm: spot (ul > li > h2.description), Google CTA, paylaş
        $response->assertSee('<h2 class="description text-base md:text-lg">', false);
        $response->assertSee('google.com/preferences/source?q=bloomberght.com', false);
        $response->assertSee('facebook.com/sharer', false);
        $response->assertSee('twitter.com/intent/tweet', false);
        $response->assertSee('nsosyal.com', false);
        $response->assertSee('linkedin.com', false);
        $response->assertSee('wa.me', false);
        $response->assertSee('mailto:', false);
        $response->assertSee('bb.ht/', false);
        $response->assertSee('text-increase', false);

        // Inline keyword bağlantısı
        $response->assertSee('/haberleri/enflasyon', false);

        // İlgili Haberler + sonsuz kaydırma verisi
        $response->assertSee('İlgili Haberler', false);
        $response->assertSee('data-infinite-article', false);
        $response->assertSee('data-infinite-sentinel', false);
        $response->assertSee('/infinite/news/3799001', false);

        // Analitik
        $response->assertSee('window.customdimension', false);
        $response->assertSee('"newsID":"3799002"', false);
    }

    public function test_infinite_endpoint_returns_next_article_fragment(): void
    {
        $this->makeArticle();
        $this->makeArticle([
            'news_id' => 3799001,
            'slug' => 'ikinci-haber',
            'title' => 'Ikinci haber basligi',
        ]);

        $response = $this->get('/infinite/news/3799001');

        $response->assertOk();
        $response->assertSee('Ikinci haber basligi');
        $response->assertSee('data-infinite-article', false);
        $response->assertSee('/infinite/news/3799000', false);
    }

    public function test_infinite_endpoint_returns_end_marker_when_missing(): void
    {
        $this->makeArticle();

        $response = $this->get('/infinite/news/3799999');

        $response->assertOk();
        $response->assertSee('data-infinite-end', false);
    }

    public function test_robots_disallows_infinite_and_sitemap_present(): void
    {
        $robots = (string) file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Disallow: /infinite/', $robots);
        $this->assertStringContainsString('Sitemap: https://www.bloomberght.com/sitemap.xml', $robots);
        $this->assertStringContainsString('sitemap_google_news.xml', $robots);
    }
}