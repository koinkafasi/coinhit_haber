<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorsaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_borsa_page_renders_all_modules(): void
    {
        $response = $this->get('/borsa');

        $response->assertOk();
        $html = $response->getContent();

        foreach ([
            'Borsa', 'En Çok Artanlar', 'En Çok Azalanlar', 'En Çok İşlem Görenler',
            'İlgili Haberler', 'Öne Çıkan Haberler', 'KAP Haberleri',
        ] as $label) {
            $this->assertStringContainsString($label, $html, "Eksik /borsa modülü: {$label}");
        }

        foreach (['/borsa/hisseler', '/borsa/en-cok', '/borsa/hisseler/kap-haberleri', '/borsa/dunya-borsalari', '/borsa/endeksler', '/borsa/borsa-kapanis', '/alternatif'] as $link) {
            $this->assertStringContainsString('href="'.$link.'"', $html, "Eksik hızlı bağlantı: {$link}");
        }

        $this->assertStringContainsString('data-market-code="XU100"', $html);
    }

    public function test_stocks_page_has_dropdown_az_tabs_columns_and_pagination(): void
    {
        $response = $this->get('/borsa/hisseler');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('Endeks Seçiniz', $html);
        $this->assertStringContainsString('class="az-tabs"', $html);

        foreach (['SON', 'DÜN', '%', 'YÜKSEK', 'DÜŞÜK', 'HACİM LOT', 'HACİM TL'] as $column) {
            $this->assertStringContainsString($column, $html, "Eksik hisse sütunu: {$column}");
        }

        // 40 hisse / 8 = 5 sayfa
        $this->assertStringContainsString('page-btn is-active', $html);
        $this->assertStringContainsString('/borsa/hisseler?sayfa=2', $html);
    }

    public function test_stocks_letter_filter_limits_rows(): void
    {
        $response = $this->get('/borsa/hisseler/G');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('>GARAN<', $html);
        $this->assertStringContainsString('>GUBRF<', $html);
        $this->assertStringNotContainsString('>THYAO<', $html);
    }

    public function test_most_traded_page_renders_rankings(): void
    {
        $response = $this->get('/borsa/en-cok');

        $response->assertOk();
        $response->assertSee('Yükselenler');
        $response->assertSee('Düşenler');
        $response->assertSee('En Çok İşlem Görenler');
    }

    public function test_kap_news_page_has_feed_videos_and_parities(): void
    {
        $response = $this->get('/borsa/hisseler/kap-haberleri');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('KAP Haberleri', $html);
        $this->assertStringContainsString('Öne Çıkan Videolar', $html);
        $this->assertStringContainsString('Pariteler', $html);
        $this->assertStringContainsString('data-market-code="USDTRY"', $html);
        $this->assertStringContainsString('/borsa/hisse/garanti-bbva/kap-haberi/', $html);
    }

    public function test_world_markets_page_and_cme_note(): void
    {
        $response = $this->get('/borsa/dunya-borsalari');

        $response->assertOk();
        $html = $response->getContent();

        foreach (['Amerika', 'Avrupa', 'Asya Pasifik', 'Vadeliler'] as $group) {
            $this->assertStringContainsString($group, $html, "Eksik dünya borsası grubu: {$group}");
        }

        $this->assertStringContainsString('CME', $html);
        $this->assertStringContainsString('data-market-code="DOW"', $html);
        $this->assertStringContainsString('data-market-code="SPFUT"', $html);
    }

    public function test_indices_page_lists_48_indices_with_detail_links(): void
    {
        $response = $this->get('/borsa/endeksler');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('ENDEKSLER', $html);
        $this->assertStringContainsString('AYLIK%', $html);
        $this->assertStringContainsString('YILLIK%', $html);
        $this->assertStringContainsString('/borsa/endeks/bist-100', $html);

        // 48 endeks satırı (data-market-code ile)
        $this->assertSame(48, substr_count($html, 'class="border-b text-sm security-'));
    }

    public function test_index_detail_page_renders(): void
    {
        $response = $this->get('/borsa/endeks/bist-100');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('BIST 100', $html);
        $this->assertStringContainsString('Güniçi Verileri', $html);
        $this->assertStringContainsString('Tümü', $html);
    }

    public function test_closing_page_has_date_select_and_columns(): void
    {
        $response = $this->get('/borsa/borsa-kapanis');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('Tarih Seçiniz', $html);
        foreach (['DÜN', 'KAPIŞ', 'YÜKSEK', 'DÜŞÜK', 'DEĞİŞİM%', 'HACİM LOT', 'HACİM TL'] as $column) {
            $this->assertStringContainsString($column, $html, "Eksik kapanış sütunu: {$column}");
        }
        $this->assertStringContainsString('>2019<', $html);
        $this->assertStringContainsString('>2026<', $html);
    }

    public function test_alternative_page_renders_baltic_and_alternatives(): void
    {
        $response = $this->get('/alternatif');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('Baltık', $html);
        $this->assertStringContainsString('Alternatif', $html);
        $this->assertStringContainsString('/emtia/', $html);
        $this->assertStringContainsString('data-market-code="VIX"', $html);
        $this->assertStringContainsString('data-market-code="BDIY"', $html);
    }

    public function test_stock_detail_page_renders_stats_and_intraday(): void
    {
        $response = $this->get('/borsa/hisse/garan-garanti-bbva-detay');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('Garanti BBVA', $html);
        $this->assertStringContainsString('(GARAN)', $html);
        $this->assertStringContainsString('Güniçi Verileri', $html);
        $this->assertStringContainsString('Güncelleme:', $html);

        foreach (['ALIŞ', 'SATIŞ', 'FARK', 'ÖNCEKİ KAPIŞ', 'EN YÜKSEK', 'EN DÜŞÜK', 'HACİM', 'HACİM(LOT)'] as $label) {
            $this->assertStringContainsString($label, $html, "Eksik istatistik: {$label}");
        }
    }

    public function test_stock_detail_returns_404_for_unknown_code(): void
    {
        $this->get('/borsa/hisse/zzzz-yok-detay')->assertNotFound();
    }

    public function test_kap_detail_page_has_share_bar_and_tr_en_table(): void
    {
        $response = $this->get('/borsa/hisse/garanti-bbva/kap-haberi/349000');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('KAP HABERLERİ', $html);
        $this->assertStringContainsString('share-bar', $html);
        $this->assertStringContainsString('facebook.com/sharer', $html);
        $this->assertStringContainsString('wa.me', $html);
        $this->assertStringContainsString('widget-table-kapnews', $html);
        $this->assertStringContainsString('CONSOLIDATION_METHOD', $html);
        $this->assertStringContainsString('ExplanationTextBlock', $html);
    }

    public function test_kap_detail_returns_404_for_unknown_id(): void
    {
        $this->get('/borsa/hisse/garanti-bbva/kap-haberi/1')->assertNotFound();
    }
}