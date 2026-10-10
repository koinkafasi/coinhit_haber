<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\News;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class I18nTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_renders_turkish_by_default(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get('/')->assertOk();

        $response->assertSee('lang="tr"', false);
        $response->assertSee('ÖNE ÇIKANLAR');
        $response->assertSee('EN ÇOK OKUNANLAR');
        $response->assertSee("Fed'in para politikası mesajlarını bekliyor");
    }

    public function test_english_home_renders_english(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->get('/en')->assertOk();

        $response->assertSee('lang="en"', false);
        $response->assertSee('FEATURED');
        $response->assertSee('MOST READ');
        $response->assertSee('MARKETS');
        $response->assertSee('Global markets await the Fed');
        $response->assertSee('og:locale" content="en_US"', false);
    }

    public function test_language_switcher_links_to_equivalent_page(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/haberler')->assertOk()->assertSee('href="/en/haberler"', false);
        $this->get('/en/haberler')->assertOk()->assertSee('href="/haberler"', false);
    }

    public function test_hreflang_and_og_locale_are_present(): void
    {
        $this->seed(DatabaseSeeder::class);

        $html = $this->get('/haberler')->assertOk()->getContent();

        $this->assertStringContainsString('hreflang="tr"', $html);
        $this->assertStringContainsString('hreflang="en"', $html);
        $this->assertStringContainsString('hreflang="x-default"', $html);
        $this->assertStringContainsString('og:locale" content="tr_TR"', $html);
        $this->assertStringContainsString('og:locale:alternate" content="en_US"', $html);
    }

    public function test_canonical_is_self_referential_per_locale(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/haberler')->assertOk()->assertSee('rel="canonical" href="'.url('/haberler').'"', false);
        $this->get('/en/haberler')->assertOk()->assertSee('rel="canonical" href="'.url('/en/haberler').'"', false);
    }

    public function test_news_detail_works_with_english_slug(): void
    {
        $this->seed(DatabaseSeeder::class);

        $news = News::where('news_id', 3791000)->firstOrFail();
        $english = $news->translation('en');
        $this->assertNotNull($english);

        $response = $this->get('/en/'.$english->slug.'-'.$news->news_id)->assertOk();

        $response->assertSee($english->title);
        $response->assertSee('Global markets await the Fed');
        $response->assertDontSee("Fed'in para politikası mesajlarını bekliyor");
    }

    public function test_switcher_on_news_detail_uses_locale_slug(): void
    {
        $this->seed(DatabaseSeeder::class);

        $news = News::where('news_id', 3791000)->firstOrFail();
        $english = $news->translation('en');
        $trSlug = $news->getRawOriginal('slug');

        $this->get('/en/'.$english->slug.'-'.$news->news_id)
            ->assertOk()
            ->assertSee('href="/'.$trSlug.'-'.$news->news_id.'"', false);

        $this->get('/'.$trSlug.'-'.$news->news_id)
            ->assertOk()
            ->assertSee('href="/en/'.$english->slug.'-'.$news->news_id.'"', false);
    }

    public function test_unknown_locale_returns_404(): void
    {
        $this->get('/de/haberler')->assertNotFound();
        $this->get('/fr')->assertNotFound();
        $this->get('/tr/haberler')->assertNotFound();
    }

    public function test_missing_translation_falls_back_to_turkish(): void
    {
        $category = Category::factory()->create(['name' => 'HABERLER']);
        $news = News::factory()->create([
            'category_id' => $category->id,
            'news_id' => 3810000,
            'slug' => 'ceviri-yok-haberi',
            'title' => 'Çevirisi olmayan haber başlığı',
            'status' => 'published',
        ]);

        $this->assertNull($news->translation('en'));

        $response = $this->get('/en/ceviri-yok-haberi-3810000')->assertOk();

        $response->assertSee('Çevirisi olmayan haber başlığı');
    }

    public function test_category_name_changes_with_locale(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/')->assertOk()->assertSee('PİYASALAR');
        $this->get('/en')->assertOk()->assertSee('MARKETS');
    }

    public function test_sitemap_includes_both_locales_with_hreflang(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/sitemap.xml')->assertOk()->assertSee('<sitemapindex', false);

        $response = $this->get('/sitemap_news_0.xml')->assertOk();

        $response->assertSee('hreflang="tr"', false);
        $response->assertSee('hreflang="en"', false);
        $response->assertSee('x-default', false);
        $response->assertSee(url('/en'), false);
    }

    public function test_legal_pages_render_localized_content(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/kunye')->assertOk()->assertSee('Künye')->assertSee('Yayın Kuruluşu');

        $this->get('/en/kunye')->assertOk()
            ->assertSee('Imprint')
            ->assertSee('Broadcaster')
            ->assertDontSee('Yayın Kuruluşu');

        $this->get('/en/kullanimkosullari')->assertOk()
            ->assertSee('Terms of Use')
            ->assertSee('Content and Copyright')
            ->assertDontSee('Kullanım Koşulları');
    }

    public function test_fund_labels_are_localized(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/yatirim-fonlari')->assertOk()
            ->assertSee('Karma ve Değişken')
            ->assertSee('Günlük(%)');

        $this->get('/en/yatirim-fonlari')->assertOk()
            ->assertSee('Mixed and Variable')
            ->assertSee('Daily(%)')
            ->assertDontSee('Karma ve Değişken');
    }

    public function test_tv_schedule_day_names_are_localized(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get('/tv')->assertOk()->assertSee('Çarşamba');
        $this->get('/en/tv')->assertOk()->assertSee('Wednesday')->assertDontSee('Çarşamba');
    }

    public function test_chart_category_label_is_localized(): void
    {
        $chart = new \App\Models\ChartArchive(['category' => 'Aktüel']);

        app()->setLocale('en');
        $this->assertSame('Current', $chart->category_label);

        app()->setLocale('tr');
        $this->assertSame('Aktüel', $chart->category_label);
    }

    public function test_new_module_pages_render_in_english(): void
    {
        $this->seed(DatabaseSeeder::class);

        $paths = [
            '/en/piyasalar', '/en/yuzde-hesaplama', '/en/haberler', '/en/tumhaberler',
            '/en/sondakika', '/en/grafik-arsivi', '/en/trendler', '/en/gorus',
            '/en/fotohaber', '/en/halka-arz-takvimi', '/en/borsa',
            '/en/doviz', '/en/doviz-cevirici', '/en/faiz-bono', '/en/kripto',
            '/en/altin', '/en/altin-cevirici', '/en/emtia',
            '/en/yatirim-fonlari', '/en/yatirim-fonlari/fon-karsilastirma',
            '/en/yatirim-fonlari/fon-portfoy-dagilim',
            '/en/tv', '/en/tv/programlar', '/en/yayinakisi',
            '/en/podcastler', '/en/video', '/en/kunye', '/en/bizeulasin',
        ];

        foreach ($paths as $path) {
            $this->get($path)->assertOk()->assertSee('lang="en"', false);
        }
    }
}
