<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Contracts\View\View;

class AuthorController extends Controller
{
    public function index(): View
    {
        return view('authors.index', array_merge([
            'authors' => Author::withCount('news')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ], $this->layoutData()));
    }
}
