<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DovizTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_sepet_kur_page_renders(): void
    {
        $response = $this->get('/doviz/sepet-kur');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('Sepet Kur', $html);
        $this->assertStringContainsString('Döviz Kurları', $html);
        $this->assertStringContainsString('data-market-code="USDTRY"', $html);
    }

    public function test_tl_euro_page_renders(): void
    {
        $response = $this->get('/doviz/tl-euro');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString('TL/Euro', $html);
        $this->assertStringContainsString('EURO', $html);
        $this->assertStringContainsString('data-market-code="EURTRY"', $html);
    }
}
