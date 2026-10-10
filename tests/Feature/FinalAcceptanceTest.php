<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinalAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    /**
     * @return array<int, string>
     */
    public static function frontUrls(): array
    {
        return [
            '/', '/piyasalar', '/yuzde-hesaplama',
            '/haberler', '/tumhaberler', '/sondakika', '/trendler', '/grafik-arsivi',
            '/yazarlar', '/gorus', '/fotohaber', '/halka-arz-takvimi',
            '/borsa', '/borsa/hisseler', '/borsa/en-cok', '/borsa/endeksler',
            '/borsa/dunya-borsalari', '/borsa/borsa-kapanis', '/alternatif',
            '/doviz', '/doviz/pariteler', '/doviz/sepet-kur', '/doviz/tl-euro',
            '/altin', '/altin-cevirici', '/emtia',
            '/faiz-bono', '/tahvil/faiz',
            '/kripto', '/yatirim-fonlari', '/yatirim-fonlari/fon-karsilastirma',
            '/tv', '/tv/programlar', '/tv/frekans-listesi', '/yayinakisi',
            '/podcastler', '/video',
            '/kunye', '/bizeulasin', '/bizeulasin/izleyicitemsilcisi',
            '/kullanimkosullari', '/aydinlatma-metni', '/cerez-politikasi',
            '/privacy', '/biyografi',
        ];
    }

    public function test_front_urls_return_successful_or_redirect(): void
    {
        foreach (self::frontUrls() as $url) {
            $response = $this->get($url);

            $this->assertContains($response->getStatusCode(), [200, 301, 302], "Beklenmeyen durum kodu ({$url}): ".$response->getStatusCode());
        }
    }

    /**
     * @return array<int, string>
     */
    public static function seoUrls(): array
    {
        return [
            '/sitemap.xml', '/sitemap_google_news.xml', '/sitemap_piyasa.xml',
            '/sitemap_authors.xml', '/sitemap_categories.xml', '/sitemap_video.xml',
            '/sitemap_video_0.xml', '/sitemap_news_0.xml',
            '/rss', '/rss/tum-haberler.xml', '/manifest.webmanifest',
        ];
    }

    public function test_seo_urls_return_ok(): void
    {
        foreach (self::seoUrls() as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_footer_contains_foreks_and_cme_data_notes(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('Foreks', $html);
        $this->assertStringContainsString('15 dk', $html);
        $this->assertStringContainsString('10 dk', $html);
        $this->assertStringContainsString('no AI/ML training use', $html);
        $this->assertStringContainsString('YASAL UYARI © 2026', $html);
        $this->assertStringContainsString('cgorsel@hs03.kep.tr', $html);
    }

    public function test_borsa_pages_show_cme_license_note(): void
    {
        foreach (['/borsa', '/borsa/dunya-borsalari', '/borsa/hisseler'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertStringContainsString('Chicago Mercantile Exchange', $html, "CME lisans notu eksik: {$url}");
        }
    }
}
