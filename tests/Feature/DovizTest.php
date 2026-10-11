<?php

namespace Tests\Feature;

use App\Services\Market\CurrencyConverter;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DovizTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_doviz_page_renders_all_modules(): void
    {
        $response = $this->get('/doviz');

        $response->assertOk();
        $html = $response->getContent();

        foreach ([
            'Döviz', 'Döviz Kurları', 'Pariteler', 'Gelişen Piyasa Kurları',
            'İlgili Haberler', 'KAP Haberleri', 'Döviz Çevirici', 'HESAPLA',
        ] as $label) {
            $this->assertStringContainsString($label, $html, "Eksik /doviz modülü: {$label}");
        }

        foreach (['/doviz/dolar', '/doviz/euro', '/doviz/ingiliz-sterlini', '/doviz/isvicre-frangi', '/doviz/pariteler'] as $link) {
            $this->assertStringContainsString('href="'.$link.'"', $html, "Eksik döviz hızlı bağlantısı: {$link}");
        }

        // Döviz Kurları (14) + mini kartlar + parite
        $this->assertStringContainsString('data-market-code="USDTRY"', $html);
        $this->assertStringContainsString('data-market-code="EURUSD"', $html);
        $this->assertStringContainsString('data-market-code="USDMXN"', $html);
    }

    public function test_doviz_tables_have_expected_row_counts_and_links(): void
    {
        $response = $this->get('/doviz');
        $html = $response->getContent();

        // Döviz (14) + Pariteler (7) + Gelişen Piyasa Kurları (6) = 27 satır
        // + header finance-slider (referans security-* sınıfı, 7 enstrüman) = 34
        $this->assertSame(34, substr_count($html, 'security-'));
        // Detay linkleri kısaltılmış slug'lara gider
        $this->assertStringContainsString('href="/doviz/dolar"', $html);
        $this->assertStringContainsString('href="/doviz/japon-yeni"', $html);
        $this->assertStringContainsString('href="/doviz/parite/eur-usd"', $html);
    }

    public function test_currency_detail_page_renders_stats_and_intraday(): void
    {
        $response = $this->get('/doviz/dolar');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('ABD DOLARI', $html);
        $this->assertStringContainsString('Güniçi Verileri', $html);
        $this->assertStringContainsString('Tümü', $html);
        $this->assertStringContainsString('Döviz Çevirici', $html);

        foreach (['ALIŞ', 'SATIŞ', 'FARK', 'ÖNCEKİ KAPIŞ', 'EN YÜKSEK', 'EN DÜŞÜK', 'Güncelleme'] as $label) {
            $this->assertStringContainsString($label, $html, "Eksik döviz detay alanı: {$label}");
        }

        foreach (['DÖVİZ', 'SON', '%', 'YÜKSEK', 'DÜŞÜK'] as $column) {
            $this->assertStringContainsString($column, $html, "Eksik detay sütunu: {$column}");
        }
    }

    public function test_currency_detail_alias_resolves_and_unknown_404s(): void
    {
        // Nav /doviz/isvicre-frangi, /doviz/japon-yeni gibi takma adları da çözer
        $this->get('/doviz/isvicre-frangi')->assertOk()->assertSee('İSVİÇRE FRANGI');
        $this->get('/doviz/japon-yeni')->assertOk()->assertSee('JAPON YENİ');

        $this->get('/doviz/zzzz-yok')->assertNotFound();
    }

    public function test_parity_detail_page_renders(): void
    {
        $response = $this->get('/doviz/parite/eur-usd');

        $response->assertOk();
        $response->assertSee('EUR/USD');
        $response->assertSee('Güniçi Verileri');
        $response->assertSee('Döviz Çevirici');
    }

    public function test_parities_list_page_renders(): void
    {
        $response = $this->get('/doviz/pariteler');

        $response->assertOk();
        $response->assertSee('Pariteler');
        $response->assertSee('Gelişen Piyasa Kurları');
        $response->assertSee('/doviz/parite/eur-usd');
    }

    public function test_converter_page_lists_22_currencies(): void
    {
        $response = $this->get('/doviz-cevirici');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('Döviz Çevirici', $html);
        $this->assertStringContainsString('HESAPLA', $html);
        $this->assertStringContainsString('Mevcut Döviz', $html);
        $this->assertStringContainsString('Çevrilecek Döviz', $html);

        // 2 select × 22 birim = 44 option
        $this->assertSame(44, substr_count($html, '<option value='));
    }

    public function test_converter_calculates_correct_cross_rates(): void
    {
        $converter = app(CurrencyConverter::class);

        $usd = $converter->rateToTry('USD');
        $eur = $converter->rateToTry('EUR');

        $this->assertGreaterThan(0, $usd);
        $this->assertGreaterThan(0, $eur);

        // 1 USD = usd TRY
        $this->assertEqualsWithDelta($usd, $converter->convert(1, 'USD', 'TRY'), 0.0001);
        // USD → EUR çapraz kur
        $this->assertEqualsWithDelta($usd / $eur, $converter->convert(1, 'USD', 'EUR'), 0.0001);
        // Aynı birim = aynı tutar
        $this->assertEqualsWithDelta(250.0, $converter->convert(250, 'GBP', 'GBP'), 0.0001);
        // Kataloğta olmayan birimler de çalışır (BRL, CNH)
        $this->assertGreaterThan(0, $converter->rateToTry('BRL'));
        $this->assertGreaterThan(0, $converter->rateToTry('CNH'));
    }
}
