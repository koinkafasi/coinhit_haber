<?php

namespace Tests\Feature;

use App\Models\Fund;
use App\Models\Program;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FundTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_fund_page_renders_all_modules(): void
    {
        $response = $this->get('/yatirim-fonlari');

        $response->assertOk();
        $html = $response->getContent();

        foreach ([
            'Yatırım Fonları', 'Fon Karşılaştır',
            'Fon Sınıflarında Bugün', 'Fon Portföy Dağılımı', 'Fon Saati',
            'Günün Özeti', 'Portföy Değeri', 'Portföy %', 'Fon Sayısı',
            'Yatırımcı Sayısı', 'Karşılaştırmalı Fon Getirileri',
            'Yükselen Fonlar', 'Düşen Fonlar', 'Tümünü Görüntüle',
        ] as $label) {
            $this->assertStringContainsString($label, $html, "Eksik /yatirim-fonlari modülü: {$label}");
        }

        // 7 fon sınıfı sekmesi (spec §8 sırası)
        foreach ([
            'Karma ve Değişken', 'Borçlanma Araçları', 'Fon Sepeti', 'Hisse Senedi',
            'Katılım', 'Kıymetli Madenler', 'Para Piyasası Fonları',
        ] as $class) {
            $this->assertStringContainsString($class, $html, "Eksik fon sınıfı: {$class}");
        }
    }

    public function test_fund_page_leaderboard_and_returns_tables_have_columns(): void
    {
        $html = $this->get('/yatirim-fonlari')->assertOk()->getContent();

        // Lider tablo kolonları (spec §8)
        foreach (['Kod', 'Ad', 'Fon Fiyatı (TL)', 'Değişim (TL) %', 'Değişim (USD) %'] as $column) {
            $this->assertStringContainsString($column, $html, "Eksik lider tablo kolonu: {$column}");
        }

        // Getiri matrisi kolonları
        foreach ([
            'Günlük(%)', 'Haftalık(%)', 'Aylık(%)', '3 Aylık(%)',
            '6 Aylık(%)', '1 Yıllık(%)', '3 Yıllık(%)', '5 Yıllık(%)', 'Risk Değeri',
        ] as $column) {
            $this->assertStringContainsString($column, $html, "Eksik getiri kolonu: {$column}");
        }

        // 7 sınıf dropdown seçeneği + 7 lider tablo paneli (referans: select-tab-menu)
        $this->assertStringContainsString('class="select-tab-options', $html);
        foreach (array_keys(\App\Models\Fund::CLASSES) as $classKey) {
            $this->assertStringContainsString('value="'.$classKey.'"', $html, "Eksik fon sınıfı seçeneği: {$classKey}");
        }
        $this->assertSame(7, substr_count($html, 'class="select-tab-content fund-class-panel'));

        // Fon kodları ve detay bağlantısı (Kod → fon detay)
        $this->assertStringContainsString('TCD', $html);
        $this->assertStringContainsString('AFA', $html);
        $this->assertStringContainsString('href="/yatirim-fonlari/tacirler-portfoy-degisken-fon"', $html);

        // Yükselen ve Düşen iki tablo da DOM'da (referans: toggle istemci tarafı)
        $this->assertStringContainsString('En Çok Yükselen Fonlar / Yatırım Fonları', $html);
        $this->assertStringContainsString('En Çok Düşen Fonlar / Yatırım Fonları', $html);
    }

    public function test_fund_classes_toggle_shows_rising_and_falling_tables(): void
    {
        $html = $this->get('/yatirim-fonlari')->assertOk()->getContent();

        // Her fon sınıfı panelinde Yükselen/Düşen tab-menu butonları (7 panel × 2) bulunur
        $this->assertSame(14, substr_count($html, 'class="tab-button w-1/2 border py-2'));

        // Yön sorguları artık sunucu çıktısını filtrelemez; iki tablo da SSR'dır ve
        // her yönde her iki tablo (Yükselen+Düşen) sayfada bulunur.
        $rising = $this->get('/yatirim-fonlari?yon=yukselen')->assertOk()->getContent();
        $falling = $this->get('/yatirim-fonlari?yon=dusen')->assertOk()->getContent();

        $this->assertStringContainsString('En Çok Yükselen Fonlar / Yatırım Fonları', $rising);
        $this->assertStringContainsString('En Çok Düşen Fonlar / Yatırım Fonları', $rising);
        $this->assertStringContainsString('En Çok Yükselen Fonlar / Yatırım Fonları', $falling);
        $this->assertStringContainsString('En Çok Düşen Fonlar / Yatırım Fonları', $falling);
    }

    public function test_fund_classes_page_is_standalone(): void
    {
        $html = $this->get('/yatirim-fonlari/fon-siniflarinda-bugun')->assertOk()->getContent();

        $this->assertStringContainsString('Fon Sınıflarında Bugün', $html);
        $this->assertStringContainsString('Yükselen Fonlar', $html);
        $this->assertStringContainsString('Düşen Fonlar', $html);
        $this->assertStringContainsString('class="select-tab-options', $html);
        $this->assertSame(7, substr_count($html, 'class="select-tab-content fund-class-panel'));

        // Alt-tool nav "Fon Sınıflarında Bugün" bağımsız sayfaya gider (referans birebir)
        $index = $this->get('/yatirim-fonlari')->assertOk()->getContent();
        $this->assertStringContainsString('href="/yatirim-fonlari/fon-siniflarinda-bugun"', $index);
    }

    public function test_returns_table_lists_all_funds_and_links_to_compare(): void
    {
        $html = $this->get('/yatirim-fonlari')->assertOk()->getContent();

        // Karşılaştırmalı tablo tüm fonları içerir (limit/toggle yok — referans birebir)
        $funds = Fund::where('is_active', true)->get();
        $this->assertTrue($funds->isNotEmpty());
        foreach ($funds as $fund) {
            $this->assertStringContainsString($fund->name, $html, "Tablo eksik: {$fund->name}");
        }

        // "Tümünü Görüntüle" → /yatirim-fonlari/fon-karsilastirma (referans birebir)
        $this->assertStringContainsString('href="/yatirim-fonlari/fon-karsilastirma"', $html);
        $this->assertStringContainsString('Tümünü Görüntüle', $html);
    }

    public function test_fund_detail_page_renders_matrix_and_portfolio_link(): void
    {
        $fund = Fund::where('code', 'TCD')->firstOrFail();

        $response = $this->get('/yatirim-fonlari/'.$fund->slug);

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString($fund->name, $html);
        $this->assertStringContainsString('TCD', $html);
        $this->assertStringContainsString('Getiri Matrisi', $html);
        $this->assertStringContainsString('Portföy Bilgisi', $html);
        $this->assertStringContainsString('Risk Değeri', $html);
        $this->assertStringContainsString('/yatirim-fonlari/fon-portfoy-dagilim?fon=TCD', $html);
    }

    public function test_compare_tool_renders_and_compares_two_funds(): void
    {
        $response = $this->get('/yatirim-fonlari/fon-karsilastirma');

        $response->assertOk();
        $response->assertSee('Fon Karşılaştır');

        $html = $this->get('/yatirim-fonlari/fon-karsilastirma?a=TCD&b=AFA')->assertOk()->getContent();

        $this->assertStringContainsString('Karşılaştırma Sonucu', $html);
        $this->assertStringContainsString('Tacirler Portföy Değişken Fon', $html);
        $this->assertStringContainsString('Ak Portföy Hisse Senedi Fonu', $html);
        $this->assertStringContainsString('Risk Değeri', $html);
    }

    public function test_portfolio_tool_renders_distribution(): void
    {
        $response = $this->get('/yatirim-fonlari/fon-portfoy-dagilim?fon=AFA');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('Portföy Dağılımı', $html);
        $this->assertStringContainsString('Hisse Senedi', $html);
        $this->assertStringContainsString('fund-distribution-bar', $html);
    }

    public function test_fon_saati_program_lists_six_episodes(): void
    {
        $program = Program::where('slug', 'fon-saati')->firstOrFail();

        $this->assertSame(6, $program->episodes()->count());

        $html = $this->get('/yatirim-fonlari')->assertOk()->getContent();
        $this->assertStringContainsString('Fon Saati', $html);
        $this->assertStringContainsString('video', $html);
    }

    public function test_unknown_fund_slug_returns_404(): void
    {
        $this->get('/yatirim-fonlari/zzz-yok-boyle-fon')->assertNotFound();
    }
}