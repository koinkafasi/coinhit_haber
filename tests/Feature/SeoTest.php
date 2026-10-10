<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\News;
use App\Models\Program;
use App\Models\ProgramEpisode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    private function article(array $attributes = []): News
    {
        return News::factory()->create($attributes);
    }

    public function test_sitemap_index_references_all_parts(): void
    {
        $this->article();

        $response = $this->get('/sitemap.xml')->assertOk();

        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<sitemapindex', false);

        foreach ([
            '/sitemap_google_news.xml',
            '/sitemap_piyasa.xml',
            '/sitemap_authors.xml',
            '/sitemap_categories.xml',
            '/sitemap_video.xml',
            '/sitemap_news_0.xml',
        ] as $part) {
            $response->assertSee(url($part), false);
        }
    }

    public function test_news_sitemap_part_contains_hreflang_alternates(): void
    {
        $news = $this->article(['slug' => 'motorinde-zam', 'news_id' => 3791025]);

        $response = $this->get('/sitemap_news_0.xml')->assertOk();

        $response->assertSee(url('/motorinde-zam-3791025'), false);
        $response->assertSee('hreflang="tr"', false);
        $response->assertSee('x-default', false);
    }

    public function test_google_news_sitemap_uses_news_and_image_namespaces(): void
    {
        $this->article(['title' => 'Google News haberi']);

        $response = $this->get('/sitemap_google_news.xml')->assertOk();

        $response->assertSee('xmlns:news=', false);
        $response->assertSee('xmlns:image=', false);
        $response->assertSee('<news:name>BloombergHT</news:name>', false);
        $response->assertSee('<news:title>Google News haberi</news:title>', false);
        $response->assertSee('<news:publication_date>', false);
        $response->assertSee('<image:loc>', false);
    }

    public function test_video_sitemap_index_has_eighteen_parts(): void
    {
        $response = $this->get('/sitemap_video.xml')->assertOk();
        $response->assertSee('<sitemapindex', false);

        foreach (range(0, 17) as $part) {
            $response->assertSee(url('/sitemap_video_'.$part.'.xml'), false);
        }
    }

    public function test_video_sitemap_part_exposes_video_namespace_and_vmcdn(): void
    {
        $program = Program::factory()->create(['is_series' => true, 'slug' => 'oyun-disi']);
        ProgramEpisode::factory()->create([
            'program_id' => $program->id,
            'slug' => 'bolum-1',
            'title' => 'Oyun Dışı Bölüm 1',
        ]);

        $response = $this->get('/sitemap_video_0.xml')->assertOk();

        $response->assertSee('xmlns:video=', false);
        $response->assertSee('<video:thumbnail_loc>', false);
        $response->assertSee('vmcdn.ciner.com.tr', false);
        $response->assertSee('/video/embed/', false);
    }

    public function test_piyasa_and_categories_sitemaps_list_expected_urls(): void
    {
        $this->article();

        $this->get('/sitemap_piyasa.xml')->assertOk()
            ->assertSee(url('/borsa'), false)
            ->assertSee(url('/kripto'), false);

        $this->get('/sitemap_categories.xml')->assertOk()
            ->assertSee(url('/haberler'), false)
            ->assertSee(url('/kunye'), false)
            ->assertSee(url('/cerez-politikasi'), false);
    }

    public function test_authors_sitemap_lists_active_authors(): void
    {
        $author = Author::factory()->create(['slug' => 'irfan-donat']);

        $this->get('/sitemap_authors.xml')->assertOk()
            ->assertSee(url('/yazarlar'), false)
            ->assertSee(url('/yorum/'.$author->slug.'/1'), false);
    }

    public function test_rss_feeds_render_with_media_content(): void
    {
        $this->article(['title' => 'RSS haberi']);

        $this->get('/rss')->assertOk()
            ->assertHeader('Content-Type', 'application/rss+xml; charset=UTF-8')
            ->assertSee('<rss version="2.0"', false)
            ->assertSee('<channel>', false)
            ->assertSee('RSS haberi', false);

        $this->get('/rss/tum-haberler.xml')->assertOk()
            ->assertSee('media:content', false)
            ->assertSee('width="1280"', false)
            ->assertSee('height="720"', false);
    }

    public function test_pwa_manifest_served(): void
    {
        $response = $this->get('/manifest.webmanifest')->assertOk();

        $response->assertHeader('Content-Type', 'application/manifest+json');
        $response->assertJsonPath('scope', '/');
        $response->assertJsonPath('display', 'browser');
        $response->assertJsonPath('icons.0.sizes', '192x192');
    }

    public function test_legacy_article_url_redirects_permanently(): void
    {
        $news = $this->article(['slug' => 'motorinde-zam', 'news_id' => 3791025]);

        $this->get('/haberler/haber/3791025-motorinde-zam')
            ->assertStatus(301)
            ->assertRedirect($news->url);
    }
}
