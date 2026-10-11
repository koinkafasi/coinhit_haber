<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ChartArchive;
use App\Models\News;
use App\Models\Trend;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WidgetsTest extends TestCase
{
    use RefreshDatabase;

    private function chart(string $title, string $category, int $chartId, string $publishedAt): ChartArchive
    {
        return ChartArchive::factory()->create([
            'title' => $title,
            'slug' => str($title)->slug(),
            'category' => $category,
            'chart_id' => $chartId,
            'published_at' => $publishedAt,
        ]);
    }

    public function test_chart_archive_shows_latest_categories_pagination_and_businessweek(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            $this->chart('Grafik örneği '.$i, 'Grafik', 3791000 + $i, now()->subDays($i)->toDateTimeString());
        }

        $response = $this->get('/grafik-arsivi');

        $response->assertOk();
        $response->assertSee('Grafik Arşivi');
        $response->assertSee('Son Eklenen Grafikler');
        $response->assertSee('Kategoriler');
        foreach (['Grafik', 'Piyasa', 'Makro Ekonomi', 'Aktüel'] as $category) {
            $response->assertSee($category);
        }

        $response->assertSee('grafik-arsivi/2');           // sayfalama bağlantısı
        $response->assertSee('Sonraki');
        $response->assertSee('businessweek.com.tr/grafik-arsivi');
        $response->assertSee('3791001');                    // kart → gerçek detay rotası
    }

    public function test_chart_archive_second_page_renders(): void
    {
        for ($i = 1; $i <= 12; $i++) {
            $this->chart('Sayfa grafiği '.$i, 'Grafik', 3792000 + $i, now()->subDays($i)->toDateTimeString());
        }

        $this->get('/grafik-arsivi/2')->assertOk()->assertSee('Sayfa grafiği 12');
    }

    public function test_chart_archive_category_filter_applies(): void
    {
        // En yeni 3 grafik latest bölümünde; filtreli grid'i ayrı doğrula.
        $this->chart('EN YENİ BİR', 'Grafik', 3793001, now()->subDay()->toDateTimeString());
        $this->chart('EN YENİ İKİ', 'Grafik', 3793002, now()->subDays(2)->toDateTimeString());
        $this->chart('EN YENİ ÜÇ', 'Grafik', 3793003, now()->subDays(3)->toDateTimeString());
        $this->chart('SADECE PİYASA KAYDI', 'Piyasa', 3793004, now()->subDays(10)->toDateTimeString());

        $this->get('/grafik-arsivi?kategori=piyasa')
            ->assertOk()
            ->assertSee('SADECE PİYASA KAYDI');

        $this->get('/grafik-arsivi?kategori=grafik')
            ->assertOk()
            ->assertDontSee('SADECE PİYASA KAYDI');
    }

    public function test_chart_detail_renders_with_related(): void
    {
        $chart = $this->chart('Detay grafiği', 'Makro Ekonomi', 3794001, now()->toDateTimeString());
        $this->chart('İlgili grafik bir', 'Makro Ekonomi', 3794002, now()->subDay()->toDateTimeString());

        $this->get($chart->url)
            ->assertOk()
            ->assertSee('Detay grafiği')
            ->assertSee('Makro Ekonomi')
            ->assertSee('İlgili Grafikler')
            ->assertSee('İlgili grafik bir');
    }

    public function test_trend_page_renders_chips_and_related_news(): void
    {
        $category = Category::factory()->create(['name' => 'PİYASALAR']);
        $news = News::factory()->create([
            'category_id' => $category->id,
            'title' => 'Rezerv haber başlığı',
            'status' => 'published',
        ]);

        $trend = Trend::factory()->create(['label' => 'Rezerv', 'slug' => 'rezerv', 'sort_order' => 1]);
        $trend->news()->attach($news->id);

        $response = $this->get('/trendler');

        $response->assertOk();
        $response->assertSee('Hızlı Linkler');
        $response->assertSee('>Tümü</button>', false);
        $response->assertSee('Rezerv');
        $response->assertSee('Rezerv haber başlığı');
        $response->assertSee('data-widget-type="trend-tabs"', false);
        $response->assertSee('data-widget-type="market-graphic-tab"', false);
    }

    public function test_trend_page_by_slug_is_accepted(): void
    {
        Trend::factory()->create(['label' => 'Enflasyon', 'slug' => 'enflasyon']);

        $this->get('/trendler/enflasyon')->assertOk()->assertSee('Enflasyon');
    }

    public function test_unknown_trend_slug_returns_404(): void
    {
        $this->get('/trendler/yok-boyle-bir-sey')->assertNotFound();
    }

    public function test_most_read_widget_renders_on_secondary_page(): void
    {
        $category = Category::factory()->create(['name' => 'EKONOMİ']);
        News::factory()->create([
            'category_id' => $category->id,
            'title' => 'Çok okunan haber',
            'views' => 999999,
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $html = $this->get('/tumhaberler')->assertOk()->getContent();

        $this->assertStringContainsString('data-widget-type="most-read"', $html);
        $this->assertStringContainsString('most-read-figure', $html);
        foreach (['Bugün', 'Bu Hafta', 'Bu Ay'] as $tab) {
            $this->assertStringContainsString('>'.$tab.'</button>', $html);
        }
        $this->assertStringContainsString('Çok okunan haber', $html);
    }

    public function test_home_renders_featured_widget_and_news_module(): void
    {
        $this->seed(DatabaseSeeder::class);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('id="home-featured-office"', $html);
        $this->assertStringContainsString('Emtia Haberleri', $html);
        $this->assertStringContainsString('category-news-module', $html);
        $this->assertStringContainsString('news-card-grid', $html);
    }

    public function test_article_sidebar_featured_widget_variants(): void
    {
        $category = Category::factory()->create(['name' => 'HABERLER']);
        $news = News::factory()->create([
            'category_id' => $category->id,
            'title' => 'Makale başlığı',
            'status' => 'published',
        ]);
        News::factory()->count(4)->create([
            'category_id' => $category->id,
            'is_featured' => true,
            'status' => 'published',
        ]);

        $html = $this->get($news->url)->assertOk()->getContent();

        $this->assertStringContainsString('Öne Çıkanlar', $html);
        $this->assertStringContainsString('headlinesOfTheDay', $html);
        $this->assertStringContainsString('news-card-type1', $html);
        $this->assertStringContainsString('news-card-type2', $html);
    }
}