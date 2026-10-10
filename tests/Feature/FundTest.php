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
            'Yatırım Fonları', 'Fon Haberleri', 'Fon Karşılaştır',
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

        // 7 sınıf sekmesi + 7 lider tablo paneli
        $this->assertSame(7, substr_count($html, 'class="tab-button fund-class-tab'));
        $this->assertSame(7, substr_count($html, 'class="tab-content fund-class-panel'));

        // Fon kodları ve detay bağlantısı (Kod → fon detay)
        $this->assertStringContainsString('TCD', $html);
        $this->assertStringContainsString('AFA', $html);
        $this->assertStringContainsString('href="/yatirim-fonlari/tacirler-portfoy-degisken-fon"', $html);
    }

    public function test_direction_toggle_filters_leaderboard(): void
    {
        $rising = $this->get('/yatirim-fonlari?yon=yukselen')->assertOk()->getContent();
        $falling = $this->get('/yatirim-fonlari?yon=dusen')->assertOk()->getContent();

        $this->assertStringContainsString('Yükselen Fonlar', $rising);
        $this->assertStringContainsString('Yükselen Fonlar', $falling);
        $this->assertStringContainsString('Düşen Fonlar', $falling);

        // Yükselen ve düşen listeler farklı sıralama üretmeli
        $this->assertNotSame($rising, $falling);
    }

    public function test_show_all_query_expands_returns_table(): void
    {
        $limited = $this->get('/yatirim-fonlari')->assertOk()->getContent();
        $full = $this->get('/yatirim-fonlari?tumu=1')->assertOk()->getContent();

        $this->assertStringContainsString('Tümünü Görüntüle', $limited);
        $this->assertStringContainsString('Daha Az Görüntüle', $full);
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