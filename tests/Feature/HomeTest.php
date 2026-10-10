<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\News;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders(): void
    {
        $category = Category::factory()->create(['name' => 'HABERLER']);
        News::factory()->create([
            'category_id' => $category->id,
            'title' => 'Demo haber başlığı',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('HABERLER');
        $response->assertSee('Demo haber başlığı');
    }

    public function test_home_page_shows_brand(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Bloomberg HT');
    }

    public function test_home_page_renders_all_module_titles(): void
    {
        $this->seed(DatabaseSeeder::class);

        $html = $this->get('/')->assertOk()->getContent();

        foreach ([
            'ÖNE ÇIKANLAR', 'PİYASALAR', 'PROGRAMLAR', 'TÜM HABERLER', 'YAYIN AKIŞI',
            'SON SAYILAR', 'Foto Haber', 'VİDEOLAR', 'PODCAST SERİLERİ', 'KAP HABERLERİ',
            'SEKTÖR HABERLERİ', 'Siyaset Haberleri', 'KRİPTO PARA', 'EN ÇOK OKUNANLAR',
            'Piyasa Haberleri', 'Hızlı Linkler', 'Grafik', 'REKLAM',
        ] as $title) {
            $this->assertStringContainsString($title, $html, "Eksik modül başlığı: {$title}");
        }
    }

    public function test_home_page_modules_keep_spec_order(): void
    {
        $this->seed(DatabaseSeeder::class);

        $html = $this->get('/')->assertOk()->getContent();

        $anchors = [
            'zone_1264',              // 1 masthead (header üstü)
            'data-widget-type="headline"', // 2 hero
            'finance-strip-mobile',   // 3 mobil finance şerit
            'home-featured-strip',    // 5 ÖNE ÇIKANLAR şerit
            'piyasalar-widget',       // 7 PİYASALAR
            'home-programs',          // 8 PROGRAMLAR
            'home-featured-cards',    // 10 ÖNE ÇIKANLAR kartlar
            'home-all-news',          // 11 TÜM HABERLER
            'home-broadcast',         // 12 YAYIN AKIŞI
            'home-businessweek',      // 14 Businessweek + SON SAYILAR
            'home-gallery',           // 15 Foto Haber
            'home-videos',            // 16 VİDEOLAR
            'home-podcasts',          // 18 PODCAST SERİLERİ
            'home-kap',               // 19 KAP HABERLERİ
            'market-data-widget',     // 20 Piyasa verileri
            'home-sector',            // 22 SEKTÖR HABERLERİ
            'home-crypto',            // 23 KRİPTO PARA
            'home-most-read',         // 25 EN ÇOK OKUNANLAR
        ];

        $previous = -1;

        foreach ($anchors as $anchor) {
            $position = strpos($html, $anchor);
            $this->assertNotFalse($position, "Modül çapası bulunamadı: {$anchor}");
            $this->assertGreaterThan($previous, $position, "Modül sırası bozuk: {$anchor}");
            $previous = $position;
        }
    }

    public function test_home_page_renders_ad_zones(): void
    {
        $this->seed(DatabaseSeeder::class);

        $html = $this->get('/')->assertOk()->getContent();

        foreach ([
            'zone_1264', 'zone_1394',          // masthead
            'zone_1257',                        // tek REKLAM
            'zone_2167', 'zone_1265',           // REKLAM x2
            'zone_1395', 'zone_1400',           // REKLAM x2
            'zone_2015', 'zone_2026',           // REKLAM x2
            'zone_2029', 'zone_2129',           // REKLAM x2
            'zone_22801', 'zone_22802',         // REKLAM x2
            'zone_1260',                        // footer billboard
            'zone_2280',                        // mobil sticky
        ] as $zone) {
            $this->assertStringContainsString('data-zone-id="'.$zone.'"', $html, "Eksik ad zone: {$zone}");
        }

        $this->assertStringContainsString('data-adv="bht"', $html);
        $this->assertStringContainsString('data-device="DESKTOP"', $html);
        $this->assertStringContainsString('data-device="MOBILE"', $html);
        $this->assertGreaterThanOrEqual(11, substr_count($html, 'class="ad-label'), 'En az 11 REKLAM etiketi bekleniyor.');
    }

    public function test_home_page_all_links_go_to_real_routes(): void
    {
        $this->seed(DatabaseSeeder::class);

        $html = $this->get('/')->assertOk()->getContent();

        foreach ([
            'href="/piyasalar"',
            'href="/video"',
            'href="/tumhaberler"',
            'href="/yayinakisi"',
            'href="/fotohaber"',
            'href="/podcastler/"',
            'href="/borsa/hisseler/kap-haberleri"',
            'href="/grafik-arsivi"',
            'href="/trendler"',
            'href="/kripto"',
            'href="/haberler/sektor-haberleri"',
        ] as $link) {
            $this->assertStringContainsString($link, $html, "Eksik bağlantı: {$link}");
        }
    }

    public function test_home_page_has_market_widget_tabs_and_tables(): void
    {
        $this->seed(DatabaseSeeder::class);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('data-widget-type="market-graphic-tab"', $html);
        foreach (['Borsa', 'Döviz', 'Emtia', 'Faiz'] as $label) {
            $this->assertStringContainsString('>'.$label.'</button>', $html, "Eksik piyasa sekmesi: {$label}");
        }

        foreach ([
            'Döviz', 'Pariteler', 'Gelişen Piyasa Kurları', 'BIST', 'En çok Artanlar',
            'En çok Azalanlar', 'Tahvil', 'Repo', 'Eurobondlar', 'Altın', 'Kıymetli Metaller',
            'Tarım Emtia', 'Alternatif Piyasalar', 'Alternatif',
        ] as $table) {
            $this->assertStringContainsString($table, $html, "Eksik piyasa tablosu: {$table}");
        }

        $this->assertStringContainsString('class="sparkline', $html);
        $this->assertStringContainsString('data-market-code="XU100"', $html);
    }

    public function test_home_page_most_read_tabs_and_crypto(): void
    {
        $this->seed(DatabaseSeeder::class);

        $html = $this->get('/')->assertOk()->getContent();

        foreach (['Bugün', 'Bu Hafta', 'Bu Ay'] as $tab) {
            $this->assertStringContainsString('>'.$tab.'</button>', $html, "Eksik en çok okunanlar sekmesi: {$tab}");
        }

        $this->assertStringContainsString('data-widget-type="most-read"', $html);
        $this->assertStringContainsString('data-widget-type="finance-slider"', $html);
        $this->assertStringContainsString('crypto-swiper', $html);
    }
}
