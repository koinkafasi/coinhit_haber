<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmtiaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_emtia_page_renders_all_groups(): void
    {
        $response = $this->get('/emtia');

        $response->assertOk();
        $html = $response->getContent();

        foreach ([
            'Emtia', 'Kıymetli Metaller', 'Tarım Emtia', 'Sanayi Emtia',
            'Sanayi Emtia 3 Aylık', 'Enerji Emtia', 'Serbest Piyasada Altın',
            'İlgili Haberler', 'Altın Listesi',
        ] as $label) {
            $this->assertStringContainsString($label, $html, "Eksik /emtia modülü: {$label}");
        }

        foreach ([
            'GÜMÜŞ GRAM', 'GÜMÜŞ ONS', 'PLATİN', 'PALADYUM',
            'PAMUK', 'KAKAO', 'KAHVE',
            'BAKIR', 'ALÜMİNYUM', 'ÇİNKO', 'NİKEL', 'KALAY', 'KURŞUN',
            'BAKIR 3 AYLIK', 'WTI PETROL', 'BRENT',
        ] as $instrument) {
            $this->assertStringContainsString($instrument, $html, "Eksik emtia enstrümanı: {$instrument}");
        }

        // CME veri lisansı notu
        $this->assertStringContainsString('Chicago Mercantile Exchange', $html);
    }

    public function test_emtia_rows_link_to_their_detail_pages(): void
    {
        $html = $this->get('/emtia')->assertOk()->getContent();

        foreach ([
            '/emtia/gumus-gram', '/emtia/gumus-ons', '/emtia/platin', '/emtia/paladyum',
            '/emtia/pamuk', '/emtia/kakao', '/emtia/kahve', '/emtia/bakir', '/emtia/nikel',
            '/emtia/bakir3m', '/emtia/nikel3m', '/emtia/wti-petrol', '/emtia/brent',
        ] as $url) {
            $this->assertStringContainsString('href="'.$url.'"', $html, "Eksik emtia detay bağlantısı: {$url}");
        }
    }

    public function test_emtia_detail_pages_render_and_aliases_resolve(): void
    {
        $this->get('/emtia/bakir3m')->assertOk()->assertSee('BAKIR 3 AYLIK');
        $this->get('/emtia/nikel3m')->assertOk()->assertSee('NİKEL 3 AYLIK');
        $this->get('/emtia/petrol')->assertOk()->assertSee('BRENT');
        $this->get('/emtia/bidy')->assertOk();

        $this->get('/emtia/zzzz-yok')->assertNotFound();
    }
}
