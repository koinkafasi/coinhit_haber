<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders(): void
    {
        $category = Category::factory()->create(['name' => 'HABERLER']);
        News::factory()->create([
            'category_id' => $category->id,
            'title' => 'Demo haber başlığı',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('HABERLER');
        $response->assertSee('Demo haber başlığı');
    }

    public function test_home_page_shows_brand(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Bloomberg HT');
    }
}
