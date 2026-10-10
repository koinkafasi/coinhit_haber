<?php

namespace Tests\Feature;

use App\Services\Market\GoldCalculator;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AltinTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_altin_page_renders_all_modules(): void
    {
        $response = $this->get('/altin');

        $response->assertOk();
        $html = $response->getContent();

        foreach ([
            'Altın', 'Altın Fiyatları', 'Serbest Piyasada Altın', 'Diğer Emtia',
            'KIYMETLİ METAL', 'Altın Hesaplama', 'HESAPLA', 'SAAT', 'İlgili Haberler', 'PİYASALAR',
        ] as $label) {
            $this->assertStringContainsString($label, $html, "Eksik /altin modülü: {$label}");
        }

        // Serbest Piyasada Altın = 17 satır (spec §5)
        $this->assertSame(17, substr_count($html, 'gold-row'), 'Serbest piyasa altın satırı 17 olmalı.');
    }

    public function test_altin_quick_links_and_detail_routes(): void
    {
        $html = $this->get('/altin')->assertOk()->getContent();

        foreach ([
            '/altin/altin-ons', '/altin/gram-altin', '/altin/ceyrek-altin',
            '/altin/cumhuriyet-altini', '/altin/ziynet-altini',
            '/altin/14-ay-bilezik-gr', '/altin/22-ay-bilezik-gr', '/emtia',
        ] as $url) {
            $this->assertStringContainsString('href="'.$url.'"', $html, "Eksik altın bağlantısı: {$url}");
        }
    }

    public function test_gold_detail_page_renders_stats_and_intraday(): void
    {
        $response = $this->get('/altin/gram-altin');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('GRAM ALTIN', $html);
        $this->assertStringContainsString('Güniçi Verileri', $html);
        $this->assertStringContainsString('Tümü', $html);
        $this->assertStringContainsString('Serbest Piyasada Altın', $html);
        $this->assertStringContainsString('Altın Hesaplama', $html);

        foreach (['ALIŞ', 'SATIŞ', 'FARK', 'ÖNCEKİ KAPIŞ', 'EN YÜKSEK', 'EN DÜŞÜK', 'Güncelleme'] as $label) {
            $this->assertStringContainsString($label, $html, "Eksik altın detay alanı: {$label}");
        }
    }

    public function test_gold_detail_resolves_readable_slugs_and_404s(): void
    {
        $this->get('/altin/22-ay-bilezik-gr')->assertOk()->assertSee('22 AYAR BİLEZİK GRAMI');
        $this->get('/altin/altin-ons')->assertOk()->assertSee('ALTIN/ONS');
        $this->get('/altin/cumhuriyet-altini')->assertOk()->assertSee('CUMHURİYET ALTINI');

        $this->get('/altin/zzzz-yok')->assertNotFound();
    }

    public function test_altin_calculator_page_lists_18_gold_options(): void
    {
        $response = $this->get('/altin-cevirici');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('Altın Hesaplama', $html);
        $this->assertStringContainsString('HESAPLA', $html);
        $this->assertStringContainsString('Mevcut Altın', $html);
        $this->assertStringContainsString('Çevrilecek Altın', $html);
        $this->assertStringContainsString('Alış', $html);
        $this->assertStringContainsString('Satış', $html);

        // 2 select × 18 enstrüman = 36 option
        $this->assertSame(36, substr_count($html, '<option value='));
    }

    public function test_gold_calculator_has_18_options_and_correct_conversion(): void
    {
        $this->assertCount(18, GoldCalculator::OPTIONS);

        $calculator = app(GoldCalculator::class);

        // Aynı birim → aynı tutar
        $this->assertEqualsWithDelta(250.0, $calculator->convert(250, 'GRAM', 'GRAM'), 0.0001);

        // 1 ons ≈ 31,1 gram (gram → ons dönüşümü doğru)
        $gramPerOns = $calculator->unitValue('GAU') / $calculator->unitValue('GRAM');
        $this->assertEqualsWithDelta(31.1, $gramPerOns, 1.5);

        // Pozitif değerler ve TL → gram
        $this->assertGreaterThan(0, $calculator->unitValue('CEYREK'));
        $this->assertGreaterThan(0, $calculator->convert(1000, 'TRY', 'GRAM'));
        $this->assertGreaterThan(0, $calculator->unitValue('KULCE_USD'));
    }
}
