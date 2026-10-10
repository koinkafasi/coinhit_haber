<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsMarketWidget;
use App\Models\ProgramEpisode;
use Illuminate\Contracts\View\View;

/**
 * `/piyasalar` — tek sütun piyasa panosu (master spec §2.2, §15.2).
 *
 * Büyük market-graphic-tab, piyasa haberleri listesi ve üst üste tam genişlik
 * tablolar tek sütunda sunulur (aside yoktur).
 */
class PiyasalarController extends Controller
{
    use BuildsMarketWidget;

    public function index(): View
    {
        return view('piyasalar.index', array_merge([
            'pageTitle' => 'Piyasalar | Bloomberght',
            'pageDescription' => 'Borsa, döviz, altın, emtia ve faiz verileri; canlı piyasa tabloları ve piyasa haberleri.',
            'pageType' => 'markets',
            'marketTables' => $this->marketTableData(),
            'videos' => ProgramEpisode::with('program')->orderByDesc('air_at')->take(8)->get(),
        ], $this->marketWidgetData(), $this->layoutData()));
    }
}
