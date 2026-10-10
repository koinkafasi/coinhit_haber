<?php

namespace Tests\Feature;

use App\Models\BreakingNews;
use App\Models\Category;
use App\Models\ChartArchive;
use App\Models\Author;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListingPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_breaking_news_page_renders(): void
    {
        Category::factory()->create(['name' => 'HABERLER']);
        BreakingNews::factory()->create(['title' => 'Demo son dakika başlığı']);

        $response = $this->get('/sondakika');

        $response->assertOk();
        $response->assertSee('Demo son dakika başlığı');
    }

    public function test_chart_archive_page_renders(): void
    {
        ChartArchive::factory()->create([
            'category' => 'Piyasa',
            'title' => 'Demo grafik başlığı',
        ]);

        $response = $this->get('/grafik-arsivi');

        $response->assertOk();
        $response->assertSee('Demo grafik başlığı');
        $response->assertSee('Piyasa');
    }

    public function test_authors_page_renders(): void
    {
        Author::factory()->create(['name' => 'Demo Yazar']);

        $response = $this->get('/yazarlar');

        $response->assertOk();
        $response->assertSee('Demo Yazar');
    }
}
