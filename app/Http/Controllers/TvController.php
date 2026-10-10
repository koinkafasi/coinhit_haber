<?php

namespace App\Http\Controllers;

use App\Models\BroadcastSchedule;
use App\Models\Program;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

/**
 * TV bölümü (spec §8, §14.3, §15.9).
 *
 * `/tv` canlı yayın + 7 günlük yayın akışı, `/yayinakisi`, `/tv/programlar`
 * (Tüm Programlar), `/tv/frekans-listesi` (Frekans Listesi) ve program detay
 * `/tv/program/{slug}` sayfalarını üretir. Tek sütun şablon.
 */
class TvController extends Controller
{
    public function index(): View
    {
        $today = CarbonImmutable::now()->isoWeekday();

        return view('tv.index', array_merge([
            'pageTitle' => 'TV',
            'liveProgram' => $this->liveProgram($today),
            'days' => $this->scheduleDays($today),
            'today' => $today,
        ], $this->layoutData()));
    }

    /**
     * /yayinakisi — 7 gün sekmeli saat sıralı program listesi.
     */
    public function schedule(): View
    {
        $today = CarbonImmutable::now()->isoWeekday();

        return view('tv.schedule', array_merge([
            'pageTitle' => 'Yayın Akışı',
            'days' => $this->scheduleDays($today),
            'today' => $today,
        ], $this->layoutData()));
    }

    /**
     * /tv/programlar — Tüm Programlar.
     */
    public function programs(): View
    {
        $programs = Program::where('is_active', true)
            ->withCount('episodes')
            ->orderBy('name')
            ->get();

        return view('tv.programs', array_merge([
            'pageTitle' => 'Tüm Programlar',
            'programs' => $programs,
            'series' => $programs->where('is_series', true)->values(),
            'tvPrograms' => $programs->where('is_series', false)->values(),
        ], $this->layoutData()));
    }

    /**
     * /tv/frekans-listesi — Frekans Listesi.
     */
    public function frequencies(): View
    {
        return view('tv.frequencies', array_merge([
            'pageTitle' => 'Frekans Listesi',
            'satellite' => [
                ['Türksat 4A', '42.0° Doğu', '12034 V', '27500', '3/4'],
                ['Türksat 4A', '42.0° Doğu', '12126 V', '27500', '5/6'],
                ['Eutelsat', '7.0° Doğu', '11534 V', '30000', '3/4'],
            ],
            'cable' => [
                ['TTNet Tivibu', 'Kanal 102'],
                ['Digiturk', 'Kanal 41'],
                ['D-Smart', 'Kanal 63'],
                ['Turkcell TV+', 'Kanal 28'],
                ['Vodafone TV', 'Kanal 55'],
            ],
        ], $this->layoutData()));
    }

    /**
     * /tv/program/{slug} — program hakkında + bölüm/arşiv listesi.
     */
    public function program(string $slug): View
    {
        $program = Program::where('slug', $slug)->where('is_active', true)->first();

        abort_unless($program, 404);

        $episodes = $program->episodes()
            ->orderByDesc('air_at')
            ->paginate(12)
            ->withQueryString();

        $related = Program::where('is_active', true)
            ->whereKeyNot($program->id)
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        return view('tv.program', array_merge([
            'pageTitle' => $program->name,
            'program' => $program,
            'episodes' => $episodes,
            'related' => $related,
        ], $this->layoutData()));
    }

    /**
     * Bugünün yayın akışından şu an yayında olan program.
     */
    private function liveProgram(int $today): ?BroadcastSchedule
    {
        $now = CarbonImmutable::now()->format('H:i');

        $current = BroadcastSchedule::where('day_of_week', $today)
            ->where('air_time', '<=', $now)
            ->orderByDesc('air_time')
            ->first();

        if ($current) {
            return $current;
        }

        return BroadcastSchedule::where('day_of_week', $today)
            ->orderBy('air_time')
            ->first();
    }

    /**
     * 7 günlük yayın akışı (her gün saat sıralı).
     *
     * @return array<int, array{day: int, name: string, is_today: bool, rows: Collection<int, BroadcastSchedule>}>
     */
    private function scheduleDays(?int $today = null): array
    {
        $today = $today ?? CarbonImmutable::now()->isoWeekday();

        $grouped = BroadcastSchedule::query()
            ->orderBy('air_time')
            ->get()
            ->groupBy('day_of_week');

        $days = [];

        foreach (BroadcastSchedule::DAYS as $number => $name) {
            $days[] = [
                'day' => $number,
                'name' => $name,
                'is_today' => $number === $today,
                'rows' => ($grouped[$number] ?? collect())->values(),
            ];
        }

        return $days;
    }
}