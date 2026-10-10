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
}
