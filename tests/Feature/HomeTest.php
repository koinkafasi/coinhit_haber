<?php

namespace Tests\Feature;

use App\Models\Advertisement;
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
            'Öne Çıkanlar', 'Piyasalar', 'Programlar', 'Tüm Haberler', 'Yayın Akışı',
            'Son Sayılar', 'Foto Haber', 'Videolar', 'Podcast Serileri', 'Kap Haberleri',
            'Sektör Haberleri', 'Siyaset Haberleri', 'Kripto Para', 'En Çok Okunanlar',
            'Piyasa Haberleri', 'Hızlı Linkler', 'Grafik',
        ] as $title) {
            $this->assertStringContainsString($title, $html, "Eksik modül başlığı: {$title}");
        }
    }

    public function test_home_page_modules_keep_spec_order(): void
    {
        $this->seed(DatabaseSeeder::class);
        // Masthead çapası için yönetilebilir bir reklam tanımla.
        Advertisement::factory()->create(['position' => '1264']);

        $html = $this->get('/')->assertOk()->getContent();

        $anchors = [
            'zone_1264',                 // 1 masthead (header üstü)
            'data-widget-type="headline"', // 2 hero
            'finance-strip-mobile',      // 3 mobil finance şerit
            'piyasalar-widget',          // PİYASALAR
            'home-programs',             // PROGRAMLAR
            'home-featured-office',      // ÖNE ÇIKANLAR (#2) ajandası
            'home-all-news',             // TÜM HABERLER
            'home-broadcast',            // YAYIN AKIŞI
            'home-businessweek',         // Businessweek + SON SAYILAR
            'home-gallery',              // Foto Haber
            'home-videos',               // VİDEOLAR
            'home-podcasts',             // PODCAST SERİLERİ
            'home-kap',                  // KAP HABERLERİ
            'market-data-widget',        // Piyasa verileri
            'home-sector',               // SEKTÖR HABERLERİ (+Emtia, Siyaset)
            'data-section="crypto"',     // KRİPTO PARA (referans birebir; ek sınıf yok)
            'data-section="mostRead"',   // EN ÇOK OKUNANLAR (referans birebir; ek sınıf yok)
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

        // Yönetilebilir reklam sistemi: tanımlanan zone'lara aktif reklam
        // verilince alanlar görünür.
        $zones = ['1264', '1257', '2167', '1265', '1395', '1400',
            '2015', '2026', '2029', '2129', '22801', '22802', '2280'];
        foreach ($zones as $zone) {
            Advertisement::factory()->create(['position' => $zone]);
        }

        $html = $this->get('/')->assertOk()->getContent();

        foreach ($zones as $zone) {
            $this->assertStringContainsString('data-zone-id="zone_'.$zone.'"', $html, "Eksik ad zone: {$zone}");
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

        $this->assertStringContainsString('data-widget-type="marketGraphData"', $html);
        foreach (['Borsa', 'Döviz', 'Emtia', 'Faiz'] as $label) {
            $this->assertStringContainsString('>'.$label.'</button>', $html, "Eksik piyasa sekmesi: {$label}");
        }

        foreach ([
            'Döviz', 'Pariteler', 'Gelişen Piyasa Kurları', 'BIST', 'En çok Artanlar',
            'En Çok Azalanlar', 'Tahvil', 'Repo', 'Eurobondlar', 'Altın', 'Kıymetli Metaller',
            'Tarım Emtia', 'Alternatif Piyasalar', 'Alternatif',
        ] as $table) {
            $this->assertStringContainsString($table, $html, "Eksik piyasa tablosu: {$table}");
        }

        $this->assertStringContainsString('class="graphChart-wrapper', $html);
        $this->assertStringContainsString('data-type="foreignCurrency"', $html);
        $this->assertStringContainsString('data-market-code="XU100"', $html);
    }

    public function test_home_page_most_read_tabs_and_crypto(): void
    {
        $this->seed(DatabaseSeeder::class);

        $html = $this->get('/')->assertOk()->getContent();

        foreach (['Bugün', 'Bu Hafta', 'Bu Ay'] as $tab) {
            $this->assertStringContainsString('>'.$tab.'</button>', $html, "Eksik en çok okunanlar sekmesi: {$tab}");
        }

        $this->assertStringContainsString('data-widget-type="mostRead"', $html);
        $this->assertStringContainsString('data-widget-type="finance-slider"', $html);
        $this->assertStringContainsString('data-section="crypto"', $html);
        $this->assertStringContainsString('data-widget-type="crypto"', $html);
    }
}
