<?php

namespace Tests\Feature;

use App\Models\BroadcastSchedule;
use App\Models\Podcast;
use App\Models\Program;
use App\Models\ProgramEpisode;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TV / Yayın Akışı / Video / Podcast bölümü (CH-14, spec §8, §14.3, §15.9).
 */
class MediaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_tv_page_renders_live_and_seven_day_schedule(): void
    {
        $response = $this->get('/tv');

        $response->assertOk();
        $html = $response->getContent();

        foreach (['Canlı Yayın', 'Yayın Akışı', 'Şu An Yayında', 'Tüm Programlar', 'Frekans Listesi'] as $label) {
            $this->assertStringContainsString($label, $html, "Eksik /tv öğesi: {$label}");
        }

        // 7 gün sekmesi (Pazartesi..Pazar)
        foreach (['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar'] as $day) {
            $this->assertStringContainsString('>'.$day.'</button>', $html, "Eksik gün sekmesi: {$day}");
        }

        $this->assertSame(7, substr_count($html, 'class="tab-button broadcast-day-tab'));
        $this->assertSame(7, substr_count($html, 'class="tab-content broadcast-day-panel'));
        $this->assertStringContainsString('data-widget-type="broadcast-days"', $html);
    }

    public function test_broadcast_schedule_covers_seven_days_with_prime_and_night_band(): void
    {
        $this->assertSame(7, BroadcastSchedule::distinct()->count('day_of_week'));

        // Her gün en az 24 satır (spec §8, §15.9).
        foreach (range(1, 7) as $day) {
            $this->assertGreaterThanOrEqual(24, BroadcastSchedule::where('day_of_week', $day)->count(), "Gün {$day} satır sayısı düşük.");
        }

        // Doğrulanmış prime kuşak (spec §14.3)
        $this->assertTrue(BroadcastSchedule::where('day_of_week', 1)->where('air_time', '19:00')->where('program_name', '60 Dakika')->exists());
        $this->assertTrue(BroadcastSchedule::where('day_of_week', 1)->where('air_time', '20:00')->where('program_name', "Aslı Şafak'la İşin Aslı")->exists());
        $this->assertTrue(BroadcastSchedule::where('day_of_week', 1)->where('air_time', '21:00')->where('program_name', 'Ekonomik Görünüm')->exists());
        $this->assertTrue(BroadcastSchedule::where('day_of_week', 1)->where('air_time', '22:00')->where('program_name', 'Küresel Piyasalar')->exists());
        $this->assertTrue(BroadcastSchedule::where('day_of_week', 1)->where('air_time', '23:10')->where('program_name', 'Spor Kulübü')->exists());

        // Cuma varyantı
        $this->assertTrue(BroadcastSchedule::where('day_of_week', 5)->where('air_time', '23:10')->where('program_name', 'Akustikhane')->exists());

        // Gece tekrar bandı 00:30–05:20
        $this->assertTrue(BroadcastSchedule::where('day_of_week', 1)->where('air_time', '00:30')->exists());
        $this->assertTrue(BroadcastSchedule::where('day_of_week', 1)->where('air_time', '05:20')->exists());
    }

    public function test_schedule_page_lists_programs_as_links(): void
    {
        $html = $this->get('/yayinakisi')->assertOk()->getContent();

        $this->assertStringContainsString('Yayın Akışı', $html);
        $this->assertStringContainsString('href="/tv/program/60-dakika"', $html);
        $this->assertStringContainsString('href="/tv/program/asli-safakla-isin-asli"', $html);
    }

    public function test_tv_programs_and_frequency_pages(): void
    {
        $programs = $this->get('/tv/programlar')->assertOk()->getContent();
        $this->assertStringContainsString('Tüm Programlar', $programs);
        $this->assertStringContainsString('Video Serileri', $programs);
        $this->assertStringContainsString('href="/tv/program/60-dakika"', $programs);
        $this->assertStringContainsString('href="/video/ust-duzey"', $programs);

        $freq = $this->get('/tv/frekans-listesi')->assertOk()->getContent();
        $this->assertStringContainsString('Frekans Listesi', $freq);
        $this->assertStringContainsString('Türksat', $freq);
        $this->assertStringContainsString('Digiturk', $freq);
    }

    public function test_program_detail_page_renders_about_and_archive(): void
    {
        $program = Program::where('slug', 'ust-duzey')->firstOrFail();

        $response = $this->get('/tv/program/ust-duzey');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString($program->name, $html);
        $this->assertStringContainsString('Bölümler', $html);
        $this->assertStringContainsString('/ust-duzey/video/', $html);
    }

    public function test_unknown_program_slug_returns_404(): void
    {
        $this->get('/tv/program/olmayan-program')->assertNotFound();
    }

    public function test_video_page_renders_series_sections_duration_badges_and_tumunu(): void
    {
        $response = $this->get('/video');

        $response->assertOk();
        $html = $response->getContent();

        foreach ([
            'Video', 'Oyun Dışı', 'Tarih Keyfi', 'Macera Sever', 'Üst Düzey',
            'Gündem Teknoloji', 'Yarının Zekası', 'Fon Saati',
        ] as $label) {
            $this->assertStringContainsString($label, $html, "Eksik /video bölümü: {$label}");
        }

        // Süre rozeti (M:SS veya HH:MM:SS) — trait/hero ve kenar kartlarında
        $this->assertMatchesRegularExpression('/class="program-duration"/', $html);

        // Her seri bölümünde TÜMÜ linki (link-type1: çizgili ayraç + "TÜMÜ" + 3 chevron)
        $this->assertStringContainsString('data-type="link-type1"', $html);
        $this->assertStringContainsString('TÜMÜ', $html);
        $this->assertStringContainsString('bloomberght-icon-font-chevron-right', $html);

        // Seri sayfası bağlantısı (TÜMÜ)
        $this->assertStringContainsString('href="/video/oyun-disi"', $html);
        $this->assertStringContainsString('href="/video/iste-zirve"', $html);
        $this->assertStringContainsString('href="/video/fon-saati"', $html);
    }

    public function test_video_series_page_lists_episodes(): void
    {
        $program = Program::where('slug', 'oyun-disi')->firstOrFail();

        $response = $this->get('/video/oyun-disi');

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString($program->name, $html);
        $this->assertStringContainsString('Bölümler', $html);
        $this->assertSame(8, $program->episodes()->count());
    }

    public function test_video_detail_page_renders_player_and_related(): void
    {
        $program = Program::where('slug', 'tarih-keyfi')->firstOrFail();
        $episode = $program->episodes()->orderByDesc('air_at')->firstOrFail();

        $response = $this->get('/tarih-keyfi/video/'.$episode->slug.'/'.$episode->id);

        $response->assertOk();
        $html = $response->getContent();

        $this->assertStringContainsString($episode->title, $html);
        $this->assertStringContainsString('<video', $html);
        $this->assertStringContainsString('İlgili Videolar', $html);
    }

    public function test_video_detail_resolves_by_prefix_route(): void
    {
        $episode = ProgramEpisode::firstOrFail();

        $this->get('/haber/video/ornek-slug/'.$episode->id)->assertOk();
    }

    public function test_video_embed_renders_standalone_player(): void
    {
        $episode = ProgramEpisode::firstOrFail();

        $response = $this->get('/video/embed/'.$episode->id);

        $response->assertOk();
        $response->assertSee('<video', false);
    }

    public function test_podcast_index_and_detail_pages(): void
    {
        $response = $this->get('/podcastler/');
        $response->assertOk();
        $html = $response->getContent();

        $this->assertSame(12, substr_count($html, 'class="podcast-card"'));

        $podcast = Podcast::with('episodes')->firstOrFail();
        $detail = $this->get('/podcastler/'.$podcast->slug);

        $detail->assertOk();
        $detailHtml = $detail->getContent();
        $this->assertStringContainsString($podcast->title, $detailHtml);
        $this->assertStringContainsString('Bölümler', $detailHtml);
        $this->assertStringContainsString('<audio', $detailHtml);
    }

    public function test_unknown_media_slugs_return_404(): void
    {
        $this->get('/video/olmayan-seri')->assertNotFound();
        $this->get('/podcastler/olmayan-podcast')->assertNotFound();
        $this->get('/video/embed/99999999')->assertNotFound();
    }
}