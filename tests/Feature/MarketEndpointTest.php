<?php

namespace Tests\Feature;

use Database\Seeders\MarketInstrumentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketEndpointTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(MarketInstrumentSeeder::class);
    }

    public function test_snapshot_endpoint_returns_groups(): void
    {
        $response = $this->getJson('/piyasa/veri?groups=bist_index,crypto');

        $response->assertOk()
            ->assertJsonStructure([
                'updated_at',
                'poll_seconds',
                'groups' => ['bist_index', 'crypto'],
            ]);
    }

    public function test_snapshot_endpoint_returns_single_quote_by_code(): void
    {
        $response = $this->getJson('/piyasa/veri?code=XU100');

        $response->assertOk()
            ->assertJsonPath('quote.code', 'XU100')
            ->assertJsonStructure(['quote' => ['last', 'change_percent', 'formatted']]);
    }

    public function test_slider_endpoint_returns_curated_quotes(): void
    {
        $response = $this->getJson('/finance-slider-data');

        $response->assertOk()
            ->assertJsonStructure(['updated_at', 'poll_seconds', 'quotes']);

        $this->assertNotEmpty($response->json('quotes'));
    }
}