<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaizBonoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_faiz_bono_page_renders_all_tables_and_links(): void
    {
        $response = $this->get('/faiz-bono');

        $response->assertOk();
        $html = $response->getContent();

        foreach ([
            'Faiz Bono', 'Tahvil', 'Repo', 'Eurobondlar', 'Uluslararası Tahviller',
            'İlgili Haberler',
        ] as $label) {
            $this->assertStringContainsString($label, $html, "Eksik /faiz-bono modülü: {$label}");
        }

        foreach ([
            'TR 2 Yıllık Tahvil', 'TR 5 Yıllık Tahvil', 'TR 10 Yıllık Tahvil',
            'Gecelik Repo', 'Haftalık Repo', 'Aylık Repo',
            'TR Eurobond 1 Yıl', 'TR Eurobond 30 Yıl',
            'ABD 10 Yıllık', 'ALMANYA 10 Yıllık', 'İNGİLTERE 10 Yıllık', 'JAPONYA 10 Yıllık',
        ] as $instrument) {
            $this->assertStringContainsString($instrument, $html, "Eksik faiz-bono enstrümanı: {$instrument}");
        }

        foreach ([
            '/tahvil/tr-2-yillik-tahvil', '/tahvil/tr-5-yillik-tahvil', '/tahvil/tr-10-yillik-tahvil',
            '/repo/repo-gecelik', '/repo/repo-haftalik', '/repo/repo-aylik',
            '/eurobond/tr-eurobond-1-yil', '/eurobond/tr-eurobond-30-yil',
            '/tahvil/abd-10-yillik-tahvil',
        ] as $url) {
            $this->assertStringContainsString('href="'.$url.'"', $html, "Eksik faiz-bono detay bağlantısı: {$url}");
        }

        $this->assertStringContainsString('data-market-code="TR2Y"', $html);
        $this->assertStringContainsString('data-market-code="REPO_GECELIK"', $html);
        $this->assertStringContainsString('data-market-code="EUROBOND_TR_1Y"', $html);

        // Foreks gecikme notu (tahvil-bono 15 dk)
        $this->assertStringContainsString('Foreks', $html);
    }

    public function test_bond_tr_detail_renders_stats_and_intraday(): void
    {
        $response = $this->get('/tahvil/tr-10-yillik-tahvil');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('TR 10 Yıllık Tahvil', $html);
        $this->assertStringContainsString('Güniçi Verileri', $html);
        $this->assertStringContainsString('Tümü', $html);
    }

    public function test_intl_bond_detail_and_almayna_typo_alias(): void
    {
        $this->get('/tahvil/abd-10-yillik-tahvil')->assertOk()->assertSee('ABD 10 Yıllık');
        $this->get('/tahvil/ingiltere-5-yillik-tahvil')->assertOk()->assertSee('İNGİLTERE 5 Yıllık');
        // sitemap `almayna` TYPO'su birebir korunur (spec §5)
        $this->get('/tahvil/almayna-10-yillik-tahvil')->assertOk()->assertSee('ALMANYA 10 Yıllık');
    }

    public function test_repo_and_eurobond_detail_pages_render(): void
    {
        $this->get('/repo/repo-gecelik')->assertOk()->assertSee('Gecelik Repo');
        $this->get('/eurobond/tr-eurobond-30-yil')->assertOk()->assertSee('TR Eurobond 30 Yıl');
    }

    public function test_tahvil_faiz_general_page_renders(): void
    {
        $response = $this->get('/tahvil/faiz');

        $response->assertOk();
        $response->assertSee('Tahvil');
    }

    public function test_unknown_bond_repo_eurobond_slugs_return_404(): void
    {
        $this->get('/tahvil/zzz-yok')->assertNotFound();
        $this->get('/repo/zzz-yok')->assertNotFound();
        $this->get('/eurobond/zzz-yok')->assertNotFound();
    }
}
