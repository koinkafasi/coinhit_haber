<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Category;
use App\Models\News;
use App\Services\Analytics\CustomDimensionBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomDimensionTest extends TestCase
{
    use RefreshDatabase;

    public function test_builds_article_dimension_from_news(): void
    {
        $root = Category::create(['name' => 'HABERLER', 'slug' => 'haberler']);
        $child = Category::create(['name' => 'ENERJİ', 'slug' => 'enerji', 'parent_id' => $root->id]);
        $author = Author::factory()->create(['name' => 'İrfan Donat']);

        $news = News::factory()->create([
            'category_id' => $child->id,
            'author_id' => $author->id,
            'title' => 'Motorinde zam',
            'body' => 'Bir iki üç dört beş altı yedi.',
            'comment_count' => 3,
            'has_video' => true,
            'views' => 1200,
        ]);

        $dimension = app(CustomDimensionBuilder::class)->build($news, 'article');

        $this->assertSame((string) $news->news_id, $dimension['newsID']);
        $this->assertSame('Motorinde zam', $dimension['newsTitle']);
        $this->assertSame('news_detail', $dimension['detailType']);
        $this->assertSame('article', $dimension['pageType']);
        $this->assertSame('ENERJİ', $dimension['mainCategory']);
        $this->assertSame('HABERLER', $dimension['mainCategory2']);
        $this->assertSame('HABERLER > ENERJİ', $dimension['hierarchy']);
        $this->assertSame('İrfan Donat', $dimension['columnistName']);
        $this->assertSame(3, $dimension['commentCount']);
        $this->assertTrue($dimension['videoIncluded']);
        $this->assertGreaterThan(0, $dimension['wordCount']);
    }

    public function test_defaults_render_for_non_article_pages(): void
    {
        $dimension = app(CustomDimensionBuilder::class)->build(null, 'home');

        $this->assertSame('home', $dimension['pageType']);
        $this->assertSame('', $dimension['newsID']);
        $this->assertSame('guest', $dimension['loginStatus']);
        $this->assertArrayHasKey('adBlockUser', $dimension);
    }

    public function test_layout_exposes_customdimension_script(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('window.customdimension', false);
    }
}