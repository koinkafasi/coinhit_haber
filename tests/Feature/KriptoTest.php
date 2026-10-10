<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KriptoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_kripto_page_renders_table_columns_and_quick_widget(): void
    {
        $response = $this->get('/kripto');

        $response->assertOk();
        $html = $response->getContent();

        foreach ([
            'Kripto', 'KRİPTO PARA', 'FİYAT($)', '%(24s)', '%(7g)',
            '24s Hacim($)', 'Piyasa Değ($)', 'İlgili Haberler',
        ] as $label) {
            $this->assertStringContainsString($label, $html, "Eksik /kripto modülü: {$label}");
        }

        foreach (['Bitcoin', 'Ethereum', 'Tether', 'BNB', 'Solana', 'Zcash', 'Hyperliquid', 'USDS'] as $coin) {
            $this->assertStringContainsString($coin, $html, "Eksik kripto para: {$coin}");
        }

        $this->assertStringContainsString('data-market-code="BTC"', $html);
        $this->assertStringContainsString('data-market-code="ETH"', $html);
        $this->assertStringContainsString('data-market-code="XRP"', $html);

        // BTC/ETH/XRP hızlı widget paneli
        $this->assertStringContainsString('crypto-quick-cards', $html);
    }

    public function test_crypto_table_has_20_rows_and_only_three_detail_links(): void
    {
        $html = $this->get('/kripto')->assertOk()->getContent();

        // 20 kripto satırı (tablo gövdesi `security-<slug>` sınıfıyla işaretli)
        $this->assertSame(20, substr_count($html, 'class="border-b text-sm security-'));

        // Yalnızca BTC/ETH/XRP detay linki alır (spec §3)
        $this->assertStringContainsString('href="/doviz/bitcoin"', $html);
        $this->assertStringContainsString('href="/doviz/ethereum"', $html);
        $this->assertStringContainsString('href="/doviz/ripple"', $html);

        // Diğerleri metin satırıdır
        $this->assertStringNotContainsString('href="/doviz/solana"', $html);
        $this->assertStringNotContainsString('href="/doviz/tether"', $html);
        $this->assertStringNotContainsString('href="/doviz/zcash"', $html);
    }

    public function test_az_filter_limits_table_rows(): void
    {
        $html = $this->get('/kripto/liste/B')->assertOk()->getContent();

        $this->assertStringContainsString('security-bitcoin', $html);
        $this->assertStringContainsString('security-bnb', $html);
        $this->assertStringNotContainsString('security-solana', $html);
        $this->assertStringNotContainsString('security-zcash', $html);
    }

    public function test_crypto_detail_pages_render(): void
    {
        $this->get('/doviz/bitcoin')->assertOk()->assertSee('Bitcoin');
        $this->get('/doviz/ethereum')->assertOk()->assertSee('Ethereum');
        $this->get('/doviz/ripple')->assertOk()->assertSee('XRP');
    }
}
