<?php

namespace App\Http\Controllers;

use App\Models\ChartArchive;
use Illuminate\Contracts\View\View;

class ChartArchiveController extends Controller
{
    public function index(): View
    {
        return view('charts.index', array_merge([
            'charts' => ChartArchive::orderByDesc('published_at')->get(),
        ], $this->layoutData()));
    }
}
