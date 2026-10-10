<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\GalleryItem;
use App\Services\Media\ImageProcessor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GalleryAdminController extends Controller
{
    public function __construct(private readonly ImageProcessor $images) {}

    public function index(): View
    {
        return view('admin.galleries.index', [
            'galleries' => Gallery::withCount('items')->orderByDesc('published_at')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.galleries.form', [
            'gallery' => new Gallery(['published_at' => now()]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $gallery = Gallery::create($this->validated($request));

        return redirect()->route('admin.galleries.edit', $gallery)->with('status', 'Galeri oluşturuldu.');
    }

    public function edit(Gallery $gallery): View
    {
        return view('admin.galleries.form', [
            'gallery' => $gallery->load(['items' => fn ($q) => $q->orderBy('sort_order')]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Gallery $gallery): RedirectResponse
    {
        $gallery->update($this->validated($request, $gallery));

        return redirect()->route('admin.galleries.edit', $gallery)->with('status', 'Galeri güncellendi.');
    }

    public function destroy(Gallery $gallery): RedirectResponse
    {
        $gallery->delete();

        return redirect()->route('admin.galleries.index')->with('status', 'Galeri silindi.');
    }

    public function storeItem(Request $request, Gallery $gallery): RedirectResponse
    {
        $validated = $request->validate([
            'image_file' => ['required_without:image', 'nullable', 'image', 'max:8192'],
            'image' => ['nullable', 'string'],
            'caption' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $image = $validated['image'] ?? null;

        if ($request->hasFile('image_file')) {
            $image = $this->images->store($request->file('image_file'), 'galleries');
        }

        abort_if($image === null, 422, 'Görsel gerekli.');

        $gallery->items()->create([
            'image' => $image,
            'caption' => $validated['caption'] ?? null,
            'sort_order' => (int) ($validated['sort_order'] ?? $gallery->items()->count()),
        ]);

        $gallery->update(['photo_total_count' => $gallery->items()->count()]);

        return redirect()->route('admin.galleries.edit', $gallery)->with('status', 'Görsel eklendi.');
    }

    public function destroyItem(Gallery $gallery, GalleryItem $item): RedirectResponse
    {
        abort_unless($item->gallery_id === $gallery->id, 404);
        $item->delete();
        $gallery->update(['photo_total_count' => $gallery->items()->count()]);

        return redirect()->route('admin.galleries.edit', $gallery)->with('status', 'Görsel silindi.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Gallery $gallery = null): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'summary' => ['nullable', 'string'],
            'cover' => ['nullable', 'string'],
            'cover_file' => ['nullable', 'image', 'max:8192'],
            'published_at' => ['nullable', 'date'],
        ]);

        if ($request->hasFile('cover_file')) {
            $validated['cover'] = $this->images->store($request->file('cover_file'), 'galleries');
        }

        unset($validated['cover_file']);

        $validated['slug'] = filled($validated['slug'] ?? null)
            ? Str::slug($validated['slug'])
            : Str::slug($validated['title']);

        return $validated;
    }
}