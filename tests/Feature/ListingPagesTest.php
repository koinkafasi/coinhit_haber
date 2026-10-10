<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\BreakingNews;
use App\Models\Category;
use App\Models\ChartArchive;
use App\Models\Gallery;
use App\Models\IpoCalendar;
use App\Models\News;
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

    public function test_tumhaberler_page_renders(): void
    {
        $category = Category::factory()->create(['name' => 'HABERLER']);
        News::factory()->create([
            'category_id' => $category->id,
            'title' => 'Demo tüm haberler başlığı',
            'status' => 'published',
        ]);

        $response = $this->get('/tumhaberler');

        $response->assertOk();
        $response->assertSee('Demo tüm haberler başlığı');
    }

    public function test_sondakika_page_renders_share_bar(): void
    {
        $news = News::factory()->create(['title' => 'Demo son dakika', 'status' => 'published']);
        BreakingNews::factory()->create([
            'news_id' => $news->id,
            'title' => 'Demo son dakika',
            'short_code' => 'abc123',
        ]);

        $response = $this->get('/sondakika');

        $response->assertOk();
        $response->assertSee('Demo son dakika');
        $response->assertSee('share-bar', false);
        $response->assertSee('bb.ht/abc123');
    }

    public function test_photo_gallery_index_and_detail_render(): void
    {
        $gallery = Gallery::factory()->create([
            'title' => 'Demo foto galeri',
            'slug' => 'demo-foto-galeri',
        ]);

        $this->get('/fotohaber')->assertOk()->assertSee('Demo foto galeri');
        $this->get('/fotohaber/demo-foto-galeri-'.$gallery->id)->assertOk()->assertSee('Demo foto galeri');
    }

    public function test_opinions_page_renders_author_cards(): void
    {
        $author = Author::factory()->create(['name' => 'Demo Köşe Yazarı', 'slug' => 'demo-kose-yazari']);
        $category = Category::factory()->create(['name' => 'GÖRÜŞ', 'slug' => 'gorus']);
        News::factory()->create([
            'category_id' => $category->id,
            'author_id' => $author->id,
            'title' => 'Demo köşe yazısı',
            'status' => 'published',
        ]);

        $this->get('/gorus')->assertOk()->assertSee('Demo Köşe Yazarı');
    }

    public function test_author_archive_lists_columns(): void
    {
        $author = Author::factory()->create(['name' => 'Arşiv Yazarı', 'slug' => 'arsiv-yazari']);
        News::factory()->create([
            'author_id' => $author->id,
            'title' => 'Arşiv köşe yazısı',
            'status' => 'published',
        ]);

        $this->get('/yorum/arsiv-yazari/'.$author->id)
            ->assertOk()
            ->assertSee('Arşiv köşe yazısı');
    }

    public function test_ipo_calendar_renders_table_and_headers(): void
    {
        IpoCalendar::factory()->create(['company_name' => 'Demo Halka Arz A.Ş.', 'ticker' => 'DMHR']);

        $response = $this->get('/halka-arz-takvimi');

        $response->assertOk();
        $response->assertSee('Talep Toplama Tarihleri');
        $response->assertSee('Şirket (ticker)');
        $response->assertSee('Demo Halka Arz A.Ş.');
    }

    public function test_tag_page_lists_matching_news(): void
    {
        $category = Category::factory()->create(['name' => 'PİYASALAR']);
        News::factory()->create([
            'category_id' => $category->id,
            'title' => 'Merkez Bankası faiz kararı',
            'tags' => ['merkez-bankasi'],
            'status' => 'published',
        ]);

        $this->get('/haberleri/merkez-bankasi')
            ->assertOk()
            ->assertSee('Merkez Bankası faiz kararı');
    }

    public function test_channel_page_renders_with_author_corner(): void
    {
        $category = Category::factory()->create(['name' => 'FİNANSAL TEKNOLOJİ', 'slug' => 'finansal-teknoloji']);
        Author::factory()->create(['name' => 'Sefer Yüksel', 'slug' => 'sefer-yuksel', 'title' => 'Finansal Teknoloji']);
        News::factory()->create([
            'category_id' => $category->id,
            'title' => 'Fintech yatırımları rekor kırdı',
            'status' => 'published',
        ]);

        $response = $this->get('/finansal-teknoloji');

        $response->assertOk();
        $response->assertSee('Finansal Teknoloji');
        $response->assertSee('Sefer Yüksel');
        $response->assertSee('Fintech yatırımları rekor kırdı');
    }

    public function test_search_json_endpoint_returns_results(): void
    {
        $category = Category::factory()->create(['name' => 'ENERJİ']);
        News::factory()->create([
            'category_id' => $category->id,
            'title' => 'Brent petrol fiyatı yükseldi',
            'status' => 'published',
        ]);

        $this->getJson('/arama?q=petrol')
            ->assertOk()
            ->assertJsonPath('total', 1);
    }
}
