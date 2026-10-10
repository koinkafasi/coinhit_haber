<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Category;
use App\Models\ChartArchive;
use App\Models\Gallery;
use App\Models\KapDisclosure;
use App\Models\News;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_admin_can_login_and_reach_dashboard(): void
    {
        $response = $this->post('/admin/login', [
            'email' => $this->admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($this->admin);

        $this->actingAs($this->admin)->get('/admin')->assertOk()->assertSee('Haber');
    }

    public function test_inactive_user_is_blocked(): void
    {
        $this->admin->update(['is_active' => false]);

        $this->actingAs($this->admin)->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_admin_can_create_news_with_image_upload(): void
    {
        $category = Category::create(['name' => 'HABERLER', 'slug' => 'haberler']);

        $response = $this->actingAs($this->admin)->post('/admin/news', [
            'category_id' => $category->id,
            'title' => 'Yönetimden test haberi',
            'body' => 'Paragraf bir.',
            'status' => 'published',
            'is_featured' => '1',
            'image_file' => UploadedFile::fake()->image('hero.jpg', 800, 600),
        ]);

        $response->assertRedirect();

        $news = News::where('title', 'Yönetimden test haberi')->firstOrFail();
        $this->assertSame(\Illuminate\Support\Str::slug('Yönetimden test haberi'), $news->slug);
        $this->assertTrue($news->is_featured);
        $this->assertStringStartsWith('/gorsel/news/', $news->image);

        $path = storage_path('app/public/'.str_replace('/gorsel/', '', $news->image));
        $this->assertFileExists($path);

        $this->get($news->image.'?w=200')->assertOk();

        @unlink($path);
    }

    public function test_admin_can_crud_reference_content(): void
    {
        $this->actingAs($this->admin)->post('/admin/authors', [
            'name' => 'Yeni Yazar',
            'is_active' => '1',
        ])->assertRedirect();
        $this->assertDatabaseHas('authors', ['name' => 'Yeni Yazar']);

        $this->actingAs($this->admin)->post('/admin/charts', [
            'title' => 'Yeni Grafik',
            'category' => 'Piyasa',
        ])->assertRedirect();
        $this->assertDatabaseHas('chart_archives', ['title' => 'Yeni Grafik']);

        $this->actingAs($this->admin)->post('/admin/programs', [
            'name' => 'Yeni Program',
            'is_active' => '1',
        ])->assertRedirect();
        $this->assertDatabaseHas('programs', ['name' => 'Yeni Program']);

        $this->actingAs($this->admin)->post('/admin/kap', [
            'company_name' => 'Test A.Ş.',
            'company_code' => 'TEST',
            'title' => 'Özel Durum Açıklaması (Genel)',
            'fields' => [
                'CONSOLIDATION_METHOD' => ['tr' => 'Yok', 'en' => 'None'],
            ],
        ])->assertRedirect();
        $this->assertDatabaseHas('kap_disclosures', ['company_code' => 'TEST']);
        $this->assertDatabaseHas('kap_fields', ['key' => 'CONSOLIDATION_METHOD', 'value_tr' => 'Yok']);

        $this->actingAs($this->admin)->post('/admin/galleries', [
            'title' => 'Yeni Galeri',
        ])->assertRedirect();
        $this->assertDatabaseHas('galleries', ['title' => 'Yeni Galeri']);
    }

    public function test_admin_can_delete_a_news_item(): void
    {
        $news = News::factory()->create();

        $this->actingAs($this->admin)
            ->delete('/admin/news/'.$news->id)
            ->assertRedirect(route('admin.news.index'));

        $this->assertDatabaseMissing('news', ['id' => $news->id]);
    }

    public function test_admin_pages_are_noindex(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/news')
            ->assertOk()
            ->assertSee('noindex', false);
    }
}