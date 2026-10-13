<?php

namespace Tests\Feature;

use App\Models\Advertisement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvertisementTest extends TestCase
{
    use RefreshDatabase;

    public function test_ad_areas_hidden_when_no_active_advertisement(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('data-zone-id="zone_1257"', $html);
        $this->assertStringNotContainsString('class="ad-label', $html);
        $this->assertStringNotContainsString('zone_2280', $html);
        $this->assertStringNotContainsString('masthead-zone', $html);
    }

    public function test_active_advertisement_renders_at_its_zone(): void
    {
        $ad = Advertisement::factory()->create([
            'position' => '1257',
            'link_url' => 'https://ornek.com/',
        ]);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('data-zone-id="zone_1257"', $html);
        $this->assertStringContainsString('https://ornek.com/', $html);
        $this->assertStringContainsString('alt="'.$ad->title.'"', $html);

        // Tanımlanmayan zonlar gizli kalır.
        $this->assertStringNotContainsString('data-zone-id="zone_2167"', $html);
    }

    public function test_inactive_advertisement_hidden(): void
    {
        Advertisement::factory()->create(['position' => '1257', 'is_active' => false]);

        $this->assertStringNotContainsString(
            'data-zone-id="zone_1257"',
            $this->get('/')->getContent()
        );
    }

    public function test_expired_advertisement_hidden(): void
    {
        Advertisement::factory()->create([
            'position' => '1257',
            'ends_at' => now()->subDay()->toDateString(),
        ]);

        $this->assertStringNotContainsString(
            'data-zone-id="zone_1257"',
            $this->get('/')->getContent()
        );
    }

    public function test_code_based_advertisement_renders_raw_html(): void
    {
        Advertisement::factory()->create([
            'position' => '1257',
            'image_path' => null,
            'code' => '<div id="dfp-test">AD</div>',
        ]);

        $this->assertStringContainsString(
            'id="dfp-test"',
            $this->get('/')->getContent()
        );
    }

    public function test_admin_can_access_advertisement_pages(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'is_active' => true]);
        Advertisement::factory()->create(['title' => 'Vitrin Reklamı']);

        $this->actingAs($admin)
            ->get('/admin/advertisements')
            ->assertOk()
            ->assertSee('Vitrin Reklamı');

        $this->actingAs($admin)
            ->get('/admin/advertisements/create')
            ->assertOk()
            ->assertSee('Konum (zone)');
    }
}
