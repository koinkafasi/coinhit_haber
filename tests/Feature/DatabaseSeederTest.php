<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\BreakingNews;
use App\Models\Category;
use App\Models\ChartArchive;
use App\Models\News;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_populates_reference_data(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('categories', 17);
        $this->assertDatabaseCount('authors', 6);
        $this->assertDatabaseCount('news', 114);
        $this->assertDatabaseCount('breaking_news', 10);
        $this->assertDatabaseCount('chart_archives', 5);
        $this->assertGreaterThan(10, SiteSetting::count());

        $this->assertTrue(User::where('email', 'admin@coinhit.local')->first()->isAdmin());
        $this->assertTrue(News::where('news_id', 3791000)->exists());
        $this->assertEquals(9, News::whereNotNull('author_id')->count());
    }
}
