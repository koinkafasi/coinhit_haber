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
            'pageTitle' => __('content.percentage_title').' | Bloomberght',
            'pageDescription' => __('content.percentage_meta_description'),
            'pageType' => 'tool',
            'breadcrumbs' => [['label' => __('content.percentage_title'), 'url' => \App\Support\Locale::url('/yuzde-hesaplama')]],
        ], $this->layoutData()));
    }
}
