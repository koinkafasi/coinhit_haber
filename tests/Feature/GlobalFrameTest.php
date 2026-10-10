<?php

namespace Tests\Feature;

use App\Models\BreakingNews;
use App\Models\Category;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalFrameTest extends TestCase
{
    use RefreshDatabase;

    private function boot(): void
    {
        $category = Category::factory()->create(['name' => 'HABERLER']);
        News::factory()->create([
            'category_id' => $category->id,
            'title' => 'Global çerçeve demo haberi',
        ]);
        BreakingNews::factory()->create(['title' => 'Global çerçeve son dakika', 'is_active' => true]);
    }

    public function test_mega_navigation_matches_spec_tree(): void
    {
        $this->boot();

        $html = $this->get('/')->assertOk()->getContent();

        foreach ([
            'PİYASALAR', 'BORSA', 'DÖVİZ', 'ALTIN', 'YATIRIM FONLARI',
            'HABERLER', 'TARIM', 'ZİRVE', 'VİDEO', 'YAYIN AKIŞI', 'BusinessWeek',
        ] as $label) {
            $this->assertStringContainsString($label, $html, "Eksik mega nav: {$label}");
        }

        foreach ([
            '/borsa/hisseler/kap-haberleri', '/borsa/dunya-borsalari', '/borsa/borsa-kapanis', '/alternatif',
            '/doviz/ingiliz-sterlini', '/doviz/pariteler',
            '/altin/22-ay-bilezik-gr', '/altin/cumhuriyet-altini', '/emtia',
            '/finansal-teknoloji', '/ekonomik-veriler-ve-gundem', '/halka-arz-takvimi', '/gorus',
        ] as $url) {
            $this->assertStringContainsString($url, $html, "Eksik mega nav bağlantısı: {$url}");
        }
    }

    public function test_header_links_and_ticker_target_sondakika(): void
    {
        $this->boot();

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('href="/tv"', $html);
        $this->assertStringContainsString('https://www.bloomberghtradyo.com', $html);
        $this->assertStringContainsString(url('/sondakika'), $html);
        $this->assertStringContainsString('SON DAKİKA', $html);
        $this->assertStringContainsString('breaking-track', $html);
    }

    public function test_footer_has_five_exact_columns_and_extras(): void
    {
        $this->boot();

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(5, substr_count($html, 'class="footer-col"'), 'Footer 5 sütun olmalı.');

        foreach ([
            'Kripto Fiyatları', 'Künye', 'Ata Altın Fiyatı', 'Altın ONS Fiyatı',
            'Sterin Kaç TL', 'Riyal Kaç TL', 'Kanada Doları', 'Bilezik Fiyatları',
            'Yüzde Hesaplama', 'Kişisel Verilerin Korunması Politikası', 'İzleyici Temsilcisi', 'Çerez Politikası',
        ] as $label) {
            $this->assertStringContainsString($label, $html, "Eksik footer bağlantısı: {$label}");
        }

        $this->assertStringContainsString('AppStore', $html);
        $this->assertStringContainsString('GooglePlay', $html);
        $this->assertStringContainsString('AppGallery', $html);
        $this->assertStringContainsString('Can Medya Grubu', $html);
        $this->assertStringContainsString('YASAL UYARI © 2026', $html);
        $this->assertStringContainsString('cgorsel@hs03.kep.tr', $html);
        $this->assertStringContainsString('15 dk', $html);
        $this->assertStringContainsString('10 dk', $html);
        $this->assertStringContainsString('BİRKAÇ KELİME YAZARAK SİZE YARDIMCI OLABİLİRİZ!', $html);
        $this->assertStringContainsString('Listele', $html);
    }

    public function test_cookie_banner_present(): void
    {
        $this->boot();

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('data-type="cookiePolicy"', $html);
        $this->assertStringContainsString('data-cookie-bar', $html);
        $this->assertStringContainsString('href="/cerez-politikasi"', $html);
        $this->assertStringContainsString('href="/aydinlatma-metni"', $html);
        $this->assertStringContainsString('Kabul Et', $html);
        $this->assertStringContainsString('data-cookie-close', $html);
    }

    public function test_head_meta_and_jsonld_global(): void
    {
        $this->boot();

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('rel="canonical"', $html);
        $this->assertStringContainsString('og:image:width" content="960"', $html);
        $this->assertStringContainsString('og:image:height" content="540"', $html);
        $this->assertStringContainsString('summary_large_image', $html);
        $this->assertStringContainsString('@bloomberght', $html);

        $this->assertStringContainsString('"@type":"Organization"', $html);
        $this->assertStringContainsString('"@type":"WebSite"', $html);
        $this->assertStringContainsString('/arama/{queryKeyword}', $html);
    }

    public function test_article_breadcrumb_jsonld(): void
    {
        $category = Category::factory()->create(['name' => 'ENERJİ']);
        $news = News::factory()->create([
            'category_id' => $category->id,
            'news_id' => 3799001,
            'slug' => 'global-cerceve-makale',
            'title' => 'Global çerçeve makale başlığı',
            'status' => 'published',
        ]);

        $html = $this->get($news->url)->assertOk()->getContent();

        $this->assertStringContainsString('"@type":"BreadcrumbList"', $html);
        $this->assertStringContainsString('breadcrumb', $html);
        $this->assertStringContainsString('og:type" content="article"', $html);
    }

    public function test_search_route_works(): void
    {
        $category = Category::factory()->create(['name' => 'PİYASALAR']);
        News::factory()->create([
            'category_id' => $category->id,
            'title' => 'Dolar kuru yükseldi',
            'status' => 'published',
        ]);

        $response = $this->get('/arama/dolar');

        $response->assertOk();
        $response->assertSee('noindex,follow', false);
        $response->assertSee('Dolar kuru yükseldi', false);

        $this->get('/arama')->assertOk();
    }
}