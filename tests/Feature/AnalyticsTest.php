<?php

namespace Tests\Feature;

use App\Models\Advertisement;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array<int, string>
     */
    private const DIMENSION_KEYS = [
        'newsID', 'newsTitle', 'editorID', 'detailType', 'pageType', 'textLength',
        'wordCount', 'commentCount', 'videoIncluded', 'datePublished', 'dateModified',
        'columnistName', 'hierarchy', 'mainCategory', 'mainCategory2', 'category1',
        'category2', 'category3', 'category4', 'layoutType', 'photoTotalCount',
        'adBlockUser', 'loginStatus', 'memberId', 'gender', 'platform', 'cinerSource',
        'nativeAd', 'webVersion', 'ageGroup', 'disYazarlar', 'isSpecialNews',
    ];

    public function test_layout_exposes_full_custom_dimension_script(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('window.customdimension', $html);

        foreach (self::DIMENSION_KEYS as $key) {
            $this->assertStringContainsString('"'.$key.'"', $html, "customdimension eksik alan: {$key}");
        }
    }

    public function test_measurement_stack_is_present(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        foreach ([
            'GTM-5XBP88',
            'googletagmanager.com/gtm.js',
            '__OGEZIO_ASYNC_OPTIONS',
            'tr.hit.gemius.pl',
            'adsbht/cbglout.js',
            'window.dataLayer',
            'pageview',
        ] as $needle) {
            $this->assertStringContainsString($needle, $html, "Ölçüm yığınında eksik: {$needle}");
        }
    }

    public function test_sticky_mobile_zone_2280_present_on_all_pages(): void
    {
        // Mobil sticky bant yalnızca yönetilebilir reklam tanımlandığında görünür.
        Advertisement::factory()->create(['position' => '2280']);

        foreach (['/', '/piyasalar', '/kunye'] as $path) {
            $html = $this->get($path)->assertOk()->getContent();
            $this->assertStringContainsString('zone_2280', $html, "zone 2280 eksik: {$path}");
        }
    }

    public function test_ad_zone_component_emits_desktop_and_mobile_variants(): void
    {
        $news = News::factory()->create();
        Advertisement::factory()->create(['position' => '1258']);

        $html = $this->get($news->url)->assertOk()->getContent();

        $this->assertStringContainsString('data-device="DESKTOP"', $html);
        $this->assertStringContainsString('data-device="MOBILE"', $html);
        $this->assertStringContainsString('data-adv="bht"', $html);
        $this->assertStringContainsString('REKLAM', $html);
    }
}
