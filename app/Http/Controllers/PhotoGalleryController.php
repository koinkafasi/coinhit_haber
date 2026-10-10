<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Support\Locale;
use Illuminate\Contracts\View\View;

class PhotoGalleryController extends Controller
{
    /**
     * /fotohaber — kare görselli galeri grid'i (spec §8).
     */
    public function index(): View
    {
        $galleries = Gallery::with('category')
            ->orderByDesc('published_at')
            ->paginate(24);

        return view('photo-gallery.index', array_merge([
            'galleries' => $galleries,
        ], $this->layoutData()));
    }

    /**
     * /fotohaber/{slug}-{id} — çok görselli foto galeri/slideshow (spec §4).
     */
    public function show(string $slug, string $id): View
    {
        abort_unless(ctype_digit($id), 404);

        $gallery = Gallery::with(['category', 'items', 'news'])
            ->where('id', (int) $id)
            ->first();

        abort_unless($gallery, 404);

        $related = Gallery::with('category')
            ->where('id', '!=', $gallery->id)
            ->orderByDesc('published_at')
            ->take(6)
            ->get();

        return view('photo-gallery.show', array_merge([
            'gallery' => $gallery,
            'related' => $related,
        ], $this->layoutData()));
    }

    public static function url(Gallery $gallery): string
    {
        return $gallery->url ?: Locale::url('/fotohaber/'.$gallery->slug.'-'.$gallery->id);
    }
}
