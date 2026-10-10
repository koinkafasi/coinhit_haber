<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_news_article_detail_renders(): void
    {
        $category = Category::factory()->create(['name' => 'ENERJİ']);
        $news = News::factory()->create([
            'category_id' => $category->id,
            'news_id' => 3791234,
            'slug' => 'demo-enerji-haberi',
            'title' => 'Demo enerji haberi başlığı',
            'status' => 'published',
        ]);

        $response = $this->get('/demo-enerji-haberi-3791234');

        $response->assertOk();
        $response->assertSee('Demo enerji haberi başlığı');
        $response->assertSee('ENERJİ');
    }

    public function test_news_article_with_wrong_slug_returns_404(): void
    {
        Category::factory()->create(['name' => 'ENERJİ']);
        News::factory()->create([
            'news_id' => 3791235,
            'slug' => 'dogru-slug',
            'status' => 'published',
        ]);

        $this->get('/yanlis-slug-3791235')->assertNotFound();
    }

    public function test_news_article_with_unknown_id_returns_404(): void
    {
        Category::factory()->create(['name' => 'ENERJİ']);
        News::factory()->create([
            'news_id' => 3791236,
            'slug' => 'baslik',
            'status' => 'published',
        ]);

        $this->get('/baslik-999999')->assertNotFound();
    }

    public function test_news_list_page_renders(): void
    {
        $category = Category::factory()->create(['name' => 'PİYASALAR']);
        News::factory()->count(5)->create([
            'category_id' => $category->id,
            'status' => 'published',
        ]);

        $response = $this->get('/haberler');

        $response->assertOk();
        $response->assertSee('PİYASALAR');
    }
}
