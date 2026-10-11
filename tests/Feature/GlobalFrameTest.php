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

    public function test_menu_widget_endpoint_returns_locale_aware_json(): void
    {
        $response = $this->getJson('/menu-widget');

        $response->assertOk();
        $response->assertJsonPath('locale', 'tr');
        $response->assertJsonPath('data.0.label', 'PİYASALAR');
        $response->assertJsonPath('data.1.label', 'BORSA');
        $response->assertJsonPath('data.1.children.0.label', 'Hisse Senetleri');
        $response->assertJsonPath('data.1.children.0.url', '/borsa/hisseler');
        $response->assertJsonPath('data.10.label', 'BusinessWeek');
        $response->assertJsonPath('data.10.external', true);

        $this->getJson('/en/menu-widget')
            ->assertOk()
            ->assertJsonPath('locale', 'en')
            ->assertJsonPath('data.0.label', 'MARKETS')
            ->assertJsonPath('data.1.label', 'EQUITIES')
            ->assertJsonPath('data.1.children.0.url', '/en/borsa/hisseler');
    }

    public function test_header_has_action_buttons_navbar_and_dropdowns(): void
    {
        $this->boot();

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('class="bht-navbar"', $html, 'Mega nav ikinci barı eksik.');
        $this->assertStringContainsString('bht-top-actions', $html);
        $this->assertStringContainsString('bht-action-live', $html);
        $this->assertStringContainsString('bht-action-radio', $html);
        $this->assertStringContainsString('search-toggle', $html);
        $this->assertStringContainsString('data-menu-toggle', $html);
        $this->assertSame(4, substr_count($html, 'class="drop-down bht-dropdown"'), 'Mega nav dropdown sayısı 4 olmalı.');
        $this->assertStringContainsString('bloomberght-icon-font-icon-radio', $html);
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