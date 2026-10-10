<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Category;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JsonLdTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_declares_organization_website_and_searchaction(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('"@type":"Organization"', $html);
        $this->assertStringContainsString('"@type":"WebSite"', $html);
        $this->assertStringContainsString('"@type":"SearchAction"', $html);
        $this->assertStringContainsString('queryKeyword', $html);
    }

    public function test_article_declares_newsarticle_person_imageobject_webpage_and_breadcrumb(): void
    {
        $category = Category::factory()->create(['name' => 'ENERJİ', 'slug' => 'enerji']);
        $author = Author::factory()->create(['name' => 'İrfan Donat']);

        $news = News::factory()->create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'slug' => 'motorinde-zam',
            'news_id' => 3791025,
            'title' => 'Motorinde zam benzinde indirim',
            'image_og' => '/img/og-default.png',
            'status' => 'published',
        ]);

        $html = $this->get($news->url)->assertOk()->getContent();

        foreach (['NewsArticle', 'Person', 'ImageObject', 'WebPage', 'BreadcrumbList'] as $type) {
            $this->assertStringContainsString('"@type":"'.$type.'"', $html, "Eksik JSON-LD türü: {$type}");
        }

        $this->assertStringContainsString('"@type":"ImageObject"', $html);
        $this->assertStringContainsString('"width":960', $html);
        $this->assertStringContainsString('"height":540', $html);
    }

    public function test_article_meta_pattern_and_canonical(): void
    {
        $category = Category::factory()->create(['name' => 'ENERJİ', 'slug' => 'enerji']);
        $news = News::factory()->create([
            'category_id' => $category->id,
            'slug' => 'motorinde-zam',
            'news_id' => 3791025,
            'title' => 'Motorinde zam',
        ]);

        $response = $this->get($news->url)->assertOk();

        $response->assertSee('Motorinde zam | ENERJİ haberleri', false);
        $response->assertSee('<link rel="canonical" href="'.url($news->url).'">', false);
        $response->assertSee('og:locale" content="tr_TR"', false);
    }
}
