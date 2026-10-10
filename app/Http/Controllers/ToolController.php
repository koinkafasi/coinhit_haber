<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * Araç sayfaları (master spec §3): `/yuzde-hesaplama`.
 */
class ToolController extends Controller
{
    public function percentage(): View
    {
        return view('tools.percentage', array_merge([
            'pageTitle' => 'Yüzde Hesaplama | Bloomberght',
            'pageDescription' => 'Yüzde artış, azalış ve oran hesaplama aracı.',
            'pageType' => 'tool',
            'breadcrumbs' => [['label' => 'Yüzde Hesaplama', 'url' => '/yuzde-hesaplama']],
        ], $this->layoutData()));
    }
}
