<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartArchive;
use App\Services\Media\ImageProcessor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChartAdminController extends Controller
{
    public const CATEGORIES = ['Grafik', 'Piyasa', 'Makro Ekonomi', 'Aktüel'];

    public function __construct(private readonly ImageProcessor $images) {}

    public function index(): View
    {
        return view('admin.charts.index', [
            'charts' => ChartArchive::orderByDesc('published_at')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.charts.form', [
            'chart' => new ChartArchive(['category' => 'Grafik', 'published_at' => now()]),
            'categories' => self::CATEGORIES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $chart = ChartArchive::create($this->validated($request));

        return redirect()->route('admin.charts.edit', $chart)->with('status', 'Grafik oluşturuldu.');
    }

    public function edit(ChartArchive $chart): View
    {
        return view('admin.charts.form', [
            'chart' => $chart,
            'categories' => self::CATEGORIES,
        ]);
    }

    public function update(Request $request, ChartArchive $chart): RedirectResponse
    {
        $chart->update($this->validated($request, $chart));

        return redirect()->route('admin.charts.edit', $chart)->with('status', 'Grafik güncellendi.');
    }

    public function destroy(ChartArchive $chart): RedirectResponse
    {
        $chart->delete();

        return redirect()->route('admin.charts.index')->with('status', 'Grafik silindi.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?ChartArchive $chart = null): array
    {
        $validated = $request->validate([
            'chart_id' => ['nullable', 'integer'],
            'category' => ['required', 'in:Grafik,Piyasa,Makro Ekonomi,Aktüel'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'summary' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'image_file' => ['nullable', 'image', 'max:5120'],
            'published_at' => ['nullable', 'date'],
        ]);

        if ($request->hasFile('image_file')) {
            $validated['image'] = $this->images->store($request->file('image_file'), 'charts');
        }

        unset($validated['image_file']);

        $validated['slug'] = filled($validated['slug'] ?? null)
            ? Str::slug($validated['slug'])
            : Str::slug($validated['title']);
        $validated['chart_id'] = filled($validated['chart_id'] ?? null)
            ? (int) $validated['chart_id']
            : (($chart?->chart_id) ?? ((int) ChartArchive::max('chart_id')) + 1);

        return $validated;
    }
}