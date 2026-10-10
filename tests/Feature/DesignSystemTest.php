<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\News;
use App\Support\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class DesignSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_image_size_contracts_are_registered(): void
    {
        $this->assertSame(['width' => 640, 'height' => 360, 'srcset' => [320, 400, 640, 1280]], Media::contract('news_card'));
        $this->assertSame(640, Media::contract('news_card_square')['width']);
        $this->assertSame(360, Media::contract('video_thumb')['width']);
        $this->assertSame(260, Media::contract('magazine')['width']);
        $this->assertSame(750, Media::contract('businessweek_lead')['width']);
        $this->assertSame(960, Media::contract('og')['width']);
        $this->assertSame('/img/transparent.gif', Media::transparentGif());
    }

    public function test_srcset_helper_builds_width_variants(): void
    {
        $srcset = Media::srcset('/img/demo.jpg', [320, 400, 640, 1280]);

        $this->assertSame(
            '/img/demo.jpg?w=320 320w, /img/demo.jpg?w=400 400w, /img/demo.jpg?w=640 640w, /img/demo.jpg?w=1280 1280w',
            $srcset
        );
    }

    public function test_lazy_image_component_uses_transparent_gif_and_data_src(): void
    {
        $html = Blade::render('<x-lazy-image src="/img/haber.jpg" alt="Demo" contract="news_card" />');

        $this->assertStringContainsString('src="/img/transparent.gif"', $html);
        $this->assertStringContainsString('data-src="/img/haber.jpg"', $html);
        $this->assertStringContainsString('data-srcset="/img/haber.jpg?w=320 320w', $html);
        $this->assertStringContainsString('loading="lazy"', $html);
        $this->assertStringContainsString('b-lazy', $html);
        $this->assertStringContainsString('<noscript>', $html);
    }

    public function test_lazy_image_component_eager_loads_without_placeholder(): void
    {
        $html = Blade::render('<x-lazy-image src="/img/one.jpg" alt="One" eager />');

        $this->assertStringContainsString('src="/img/one.jpg"', $html);
        $this->assertStringNotContainsString('data-src=', $html);
    }

    public function test_icon_component_exposes_bloomberght_icon_font_classes(): void
    {
        foreach (['facebook', 'twitter-x', 'nsosyal', 'linkedin', 'whatsapp', 'mail', 'copy', 'chevron-right', 'search', 'live', 'radio'] as $name) {
            $html = Blade::render('<x-icon name="'.$name.'" />');
            $this->assertStringContainsString('bloomberght-icon-font-icon-'.$name, $html, "Eksik ikon: {$name}");
            $this->assertStringContainsString('bloomberght-icon-font', $html);
        }
    }

    public function test_css_contains_design_tokens(): void
    {
        $css = file_get_contents(public_path('css/app.css'));

        foreach (['--bht-yellow', '--bht-black', '--breaking-red', '--live-yellow', '--up', '--down', '--flat', '.font-unna', 'bloomberght-icon-font', 'img-640x360'] as $needle) {
            $this->assertStringContainsString($needle, $css, "CSS token eksik: {$needle}");
        }
    }

    public function test_front_layout_renders_with_header_footer_and_icons(): void
    {
        $category = Category::factory()->create(['name' => 'HABERLER']);
        News::factory()->create([
            'category_id' => $category->id,
            'title' => 'Demo haber başlığı',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('bloomberght-icon-font-icon-search', false);
        $response->assertSee('bloomberght-icon-font-icon-live', false);
        $response->assertSee('bloomberght-icon-font-icon-radio', false);
        $response->assertSee('bht-footer-grid', false);
        $response->assertSee('footer-menu', false);
        $response->assertSee('js/lazy.js', false);
        $response->assertSee('SON DAKİKA', false);
        $response->assertSee('data-src=', false);
        $response->assertSee('/img/transparent.gif', false);
    }
}
