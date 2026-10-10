<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\ChartArchive;
use App\Models\Gallery;
use App\Models\KapDisclosure;
use App\Models\MarketInstrument;
use App\Models\News;
use App\Models\Podcast;
use App\Models\Program;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'news' => News::count(),
                'authors' => Author::count(),
                'programs' => Program::count(),
                'podcasts' => Podcast::count(),
                'galleries' => Gallery::count(),
                'kap' => KapDisclosure::count(),
                'charts' => ChartArchive::count(),
                'instruments' => MarketInstrument::count(),
            ],
        ]);
    }
}