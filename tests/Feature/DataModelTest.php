<?php

namespace Tests\Feature;

use App\Models\BroadcastSchedule;
use App\Models\Gallery;
use App\Models\KapDisclosure;
use App\Models\News;
use App\Models\Program;
use App\Models\ProgramEpisode;
use App\Models\Trend;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_populates_new_content_tables(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('galleries', 5);
        $this->assertDatabaseCount('gallery_items', 30);
        $this->assertDatabaseCount('programs', 45);
        $this->assertDatabaseCount('program_episodes', 190);
        $this->assertDatabaseCount('broadcast_schedules', 207);
        $this->assertDatabaseCount('podcasts', 12);
        $this->assertDatabaseCount('podcast_episodes', 36);
        $this->assertDatabaseCount('kap_disclosures', 64);
        $this->assertDatabaseCount('kap_fields', 448);
        $this->assertDatabaseCount('ipo_calendars', 8);
        $this->assertDatabaseCount('trends', 6);
        $this->assertGreaterThan(200, \App\Models\MarketInstrument::count());
        $this->assertGreaterThan(0, \Illuminate\Support\Facades\DB::table('news_trend')->count());
    }

    public function test_gallery_relation_and_photo_count(): void
    {
        $this->seed(DatabaseSeeder::class);

        $gallery = Gallery::with('items')->firstOrFail();

        $this->assertCount(6, $gallery->items);
        $this->assertSame(6, $gallery->photo_total_count);
        $this->assertStringContainsString('/fotohaber/', $gallery->url);
    }

    public function test_program_episode_duration_and_title_pattern(): void
    {
        $this->seed(DatabaseSeeder::class);

        $program = Program::with('episodes')->firstOrFail();
        $this->assertNotEmpty($program->episodes);

        $episode = new ProgramEpisode([
            'title' => 'Piyasa değerlendirmesi',
            'guest' => 'Konuk Analist',
            'duration_seconds' => 3661,
            'air_at' => now(),
        ]);
        $episode->setRelation('program', $program);

        $this->assertSame('01:01:01', $episode->duration);

        $short = new ProgramEpisode(['title' => 'Kısa bölüm', 'duration_seconds' => 95]);
        $this->assertSame('1:35', $short->duration);
        $this->assertStringContainsString($program->name, $episode->display_title);
        $this->assertStringContainsString('Konuk Analist', $episode->display_title);
    }

    public function test_kap_disclosure_has_tr_en_field_dictionary(): void
    {
        $this->seed(DatabaseSeeder::class);

        $disclosure = KapDisclosure::with('fields')->firstOrFail();

        $this->assertCount(7, $disclosure->fields);
        $this->assertContains('CONSOLIDATION_METHOD', $disclosure->fields->pluck('key')->all());
        $this->assertStringContainsString('/borsa/hisse/', $disclosure->url);
        $this->assertStringContainsString('/kap-haberi/', $disclosure->url);
    }

    public function test_trend_belongs_to_many_news(): void
    {
        $this->seed(DatabaseSeeder::class);

        $trend = Trend::with('news')->firstOrFail();

        $this->assertNotEmpty($trend->news);
        $this->assertInstanceOf(News::class, $trend->news->first());
    }

    public function test_news_content_blocks_and_analytics_helpers(): void
    {
        $news = new News([
            'title' => 'Test haber başlığı',
            'body' => "Birinci paragraf.\n\nİkinci paragraf.",
            'inline_images' => [['src' => '/gorsel/news/a.jpg', 'alt' => '', 'class' => 'inline']],
            'embeds' => [['src' => 'visualisation/123', 'title' => 'Grafik']],
            'comment_count' => 7,
            'has_video' => true,
        ]);

        $blocks = $news->contentBlocks();

        $this->assertCount(4, $blocks);
        $this->assertSame('paragraph', $blocks[0]['type']);
        $this->assertSame('image', $blocks[2]['type']);
        $this->assertSame('flourish', $blocks[3]['type']);
        $this->assertGreaterThan(0, $news->wordCount());
        $this->assertGreaterThan(0, $news->textLength());
    }

    public function test_broadcast_schedule_day_name(): void
    {
        $this->seed(DatabaseSeeder::class);

        $schedule = BroadcastSchedule::firstOrFail();
        $this->assertNotEmpty($schedule->day_name);
    }
}