<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\ProgramEpisode;
use App\Services\Media\ImageProcessor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProgramAdminController extends Controller
{
    public function __construct(private readonly ImageProcessor $images) {}

    public function index(): View
    {
        return view('admin.programs.index', [
            'programs' => Program::withCount('episodes')->orderBy('sort_order')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.programs.form', ['program' => new Program(['is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $program = Program::create($this->validated($request));

        return redirect()->route('admin.programs.edit', $program)->with('status', 'Program oluşturuldu.');
    }

    public function edit(Program $program): View
    {
        return view('admin.programs.form', [
            'program' => $program->load(['episodes' => fn ($q) => $q->orderByDesc('air_at')]),
        ]);
    }

    public function update(Request $request, Program $program): RedirectResponse
    {
        $program->update($this->validated($request, $program));

        return redirect()->route('admin.programs.edit', $program)->with('status', 'Program güncellendi.');
    }

    public function destroy(Program $program): RedirectResponse
    {
        $program->delete();

        return redirect()->route('admin.programs.index')->with('status', 'Program silindi.');
    }

    public function storeEpisode(Request $request, Program $program): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'guest' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:8'],
            'video_url' => ['nullable', 'string', 'max:255'],
            'thumbnail_file' => ['nullable', 'image', 'max:5120'],
            'air_at' => ['nullable', 'date'],
        ]);

        if ($request->hasFile('thumbnail_file')) {
            $validated['thumbnail'] = $this->images->store($request->file('thumbnail_file'), 'programs');
        }

        $program->episodes()->create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'guest' => $validated['guest'] ?? null,
            'duration_seconds' => $this->toSeconds($validated['duration'] ?? ''),
            'thumbnail' => $validated['thumbnail'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'air_at' => $validated['air_at'] ?? now(),
        ]);

        return redirect()->route('admin.programs.edit', $program)->with('status', 'Bölüm eklendi.');
    }

    public function destroyEpisode(Program $program, ProgramEpisode $episode): RedirectResponse
    {
        abort_unless($episode->program_id === $program->id, 404);
        $episode->delete();

        return redirect()->route('admin.programs.edit', $program)->with('status', 'Bölüm silindi.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Program $program = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'host' => ['nullable', 'string', 'max:255'],
            'air_time' => ['nullable', 'string', 'max:5'],
            'day_of_week' => ['nullable', 'integer', 'between:1,7'],
            'thumbnail' => ['nullable', 'string'],
            'thumbnail_file' => ['nullable', 'image', 'max:5120'],
            'is_live' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('thumbnail_file')) {
            $validated['thumbnail'] = $this->images->store($request->file('thumbnail_file'), 'programs');
        }

        unset($validated['thumbnail_file']);

        $validated['slug'] = filled($validated['slug'] ?? null)
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_live'] = $request->boolean('is_live');
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }

    /**
     * "HH:MM:SS" veya "M:SS" metnini saniyeye çevirir (spec §4).
     */
    private function toSeconds(?string $duration): int
    {
        if (! filled($duration)) {
            return 0;
        }

        $parts = array_map('intval', explode(':', $duration));

        return match (count($parts)) {
            3 => $parts[0] * 3600 + $parts[1] * 60 + $parts[2],
            2 => $parts[0] * 60 + $parts[1],
            default => (int) $parts[0],
        };
    }
}