<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Category;
use App\Models\News;
use App\Services\Media\ImageProcessor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsAdminController extends Controller
{
    public function __construct(private readonly ImageProcessor $images) {}

    public function index(Request $request): View
    {
        $news = News::with(['category', 'author'])
            ->when($request->query('q'), function ($query, $q) {
                $query->where('title', 'like', '%'.$q.'%');
            })
            ->orderByDesc('published_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.news.index', [
            'news' => $news,
            'q' => (string) $request->query('q', ''),
        ]);
    }

    public function create(): View
    {
        return view('admin.news.form', $this->formData(new News([
            'status' => 'published',
            'editor_id' => auth()->id(),
            'published_at' => now(),
        ])));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $news = News::create($data);

        return redirect()->route('admin.news.edit', $news)->with('status', 'Haber oluşturuldu.');
    }

    public function edit(News $news): View
    {
        return view('admin.news.form', $this->formData($news));
    }

    public function update(Request $request, News $news): RedirectResponse
    {
        $news->update($this->validated($request, $news));

        return redirect()->route('admin.news.edit', $news)->with('status', 'Haber güncellendi.');
    }

    public function destroy(News $news): RedirectResponse
    {
        $news->delete();

        return redirect()->route('admin.news.index')->with('status', 'Haber silindi.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(News $news): array
    {
        return [
            'news' => $news,
            'categories' => Category::orderBy('name')->get(),
            'authors' => Author::orderBy('name')->get(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?News $news = null): array
    {
        $validated = $request->validate([
            'news_id' => ['nullable', 'integer'],
            'category_id' => ['required', 'exists:categories,id'],
            'author_id' => ['nullable', 'exists:authors,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'spot' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'image_file' => ['nullable', 'image', 'max:5120'],
            'image_og' => ['nullable', 'string'],
            'source' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'string'],
            'keywords' => ['nullable', 'string'],
            'status' => ['required', 'in:published,draft'],
            'is_breaking' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'has_video' => ['nullable', 'boolean'],
            'video_url' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'modified_at' => ['nullable', 'date'],
        ]);

        if ($request->hasFile('image_file')) {
            $validated['image'] = $this->images->store($request->file('image_file'), 'news');
        }

        unset($validated['image_file']);

        $validated['slug'] = filled($validated['slug'] ?? null)
            ? Str::slug($validated['slug'])
            : Str::slug($validated['title']);

        $validated['news_id'] = filled($validated['news_id'] ?? null)
            ? (int) $validated['news_id']
            : (($news?->news_id) ?? ((int) News::max('news_id')) + 1);

        $validated['tags'] = $this->csv($validated['tags'] ?? null);
        $validated['keywords'] = $this->csv($validated['keywords'] ?? null);
        $validated['is_breaking'] = $request->boolean('is_breaking');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['has_video'] = $request->boolean('has_video');
        $validated['editor_id'] = auth()->id();
        $validated['modified_at'] = $validated['modified_at'] ?? now();
        $validated['word_count'] = str_word_count(strip_tags((string) ($validated['body'] ?? '')), 0, 'çğıöşüÇĞİÖŞÜ');

        return $validated;
    }

    /**
     * @return array<int, string>
     */
    private function csv(?string $value): array
    {
        if (! filled($value)) {
            return [];
        }

        return collect(explode(',', $value))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}