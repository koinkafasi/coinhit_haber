<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Services\Media\ImageProcessor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthorAdminController extends Controller
{
    public function __construct(private readonly ImageProcessor $images) {}

    public function index(): View
    {
        return view('admin.authors.index', [
            'authors' => Author::withCount('news')->orderBy('sort_order')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.authors.form', ['author' => new Author(['is_active' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $author = Author::create($this->validated($request));

        return redirect()->route('admin.authors.edit', $author)->with('status', 'Yazar oluşturuldu.');
    }

    public function edit(Author $author): View
    {
        return view('admin.authors.form', ['author' => $author]);
    }

    public function update(Request $request, Author $author): RedirectResponse
    {
        $author->update($this->validated($request, $author));

        return redirect()->route('admin.authors.edit', $author)->with('status', 'Yazar güncellendi.');
    }

    public function destroy(Author $author): RedirectResponse
    {
        $author->delete();

        return redirect()->route('admin.authors.index')->with('status', 'Yazar silindi.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Author $author = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'avatar' => ['nullable', 'string'],
            'avatar_file' => ['nullable', 'image', 'max:3072'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('avatar_file')) {
            $validated['avatar'] = $this->images->store($request->file('avatar_file'), 'authors');
        }

        unset($validated['avatar_file']);

        $validated['slug'] = filled($validated['slug'] ?? null)
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }
}