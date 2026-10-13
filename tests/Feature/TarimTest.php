<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TarimTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_tarim_page_renders_reference_modules(): void
    {
        $response = $this->get('/tarim');

        $response->assertOk();
        $html = $response->getContent();

        foreach ([
            'Tarım', 'Tarım Analiz', 'Tarım Haberleri',
        ] as $label) {
            $this->assertStringContainsString($label, $html, "Eksik /tarim modülü: {$label}");
        }

        // Hero haber main-slider (referans: 12 slide)
        $this->assertStringContainsString('data-widget-type="main-slider"', $html);
        $this->assertGreaterThanOrEqual(8, substr_count($html, 'data-hash="'), 'Hero slider az slide içeriyor.');

        // Tarım Analiz köşesi: sarı zemin + 3 person-card (İrfan Donat köşeleri)
        $this->assertStringContainsString('person-card-type2', $html);
        $this->assertSame(3, substr_count($html, 'data-widget-type="person-card-type2"'), 'Tarım Analiz 3 köşe yazısı içermeli.');

        // Tarım Haberleri: 3 kolonlu grid
        $this->assertStringContainsString('grid grid-cols-1 md:grid-cols-3 md:gap-4 mb-4', $html);
    }

    public function test_tarim_analiz_shows_author_name_and_linked_writeups(): void
    {
        $html = $this->get('/tarim')->assertOk()->getContent();

        // Yazar köşesi adı (İrfan Donat) ve köşe yazıları linkli
        $this->assertStringContainsString('İrfan Donat', $html);
        $this->assertStringContainsString('Tarımın geleceği teknolojide', $html);
        $this->assertStringContainsString('Tarımda fiyat istikrarı için politikalar gerekli', $html);

        $author = \App\Models\Author::where('slug', 'irfan-donat')->first();
        if ($author) {
            $opinions = \App\Models\News::where('author_id', $author->id)->where('status', 'published')
                ->orderByDesc('published_at')->take(3)->get();
            $this->assertCount(3, $opinions);

            foreach ($opinions as $opinion) {
                $this->assertStringContainsString('href="'.$opinion->url.'"', $html, "Eksik köşe yazısı bağlantısı: {$opinion->title}");
            }
        }
    }
}
