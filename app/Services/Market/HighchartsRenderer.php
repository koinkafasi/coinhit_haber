<?php

namespace App\Services\Market;

/**
 * Referans (bloomberght) Highcharts alan grafiğini SSR olarak üretir.
 *
 * Highcharts JS istemcide çalışır; bu sınıf aynı DOM/class yapısını sunucuda
 * basar: highcharts-container > root > background > plot-background > grid >
 * axis > series(area) > credits. Ölçüler ve renkler referansla aynıdır
 * (grid #e6e6e6, eksen #333333, alan dolgu #DEDEDE, tick #666666).
 */
class HighchartsRenderer
{
    private const GRID = '#e6e6e6';

    private const AXIS = '#333333';

    private const TICK = '#666666';

    private const AREA = '#DEDEDE';

    private const SERIES = '#333333';

    /**
     * @param  array<int, float>  $values  kronolojik seri
     */
    public function areaChart(array $values, int $width = 414, int $height = 201, string $uid = '0'): string
    {
        $values = array_values(array_map('floatval', $values));
        $n = count($values);

        if ($n < 2) {
            return '';
        }

        $margin = 10;
        $plotW = $width - ($margin * 2);
        $plotH = $height - ($margin * 2);
        $xAxisH = 10; // alt tick payı

        $min = min($values);
        $max = max($values);
        $range = $max - $min;
        $range = $range > 0 ? $range : 1.0;

        $stepX = $plotW / ($n - 1);

        // Alan serisi: y = (max - v)/range * plotH  (Highcharts y ekseninde ters çevirir)
        $pts = [];
        foreach ($values as $i => $v) {
            $x = round($i * $stepX, 4);
            $y = round((($max - $v) / $range) * ($plotH - $xAxisH) + 0.5, 4);
            $pts[] = [$x, $y];
        }

        $areaD = $this->areaPath($pts, $plotH, $plotW);
        $lineD = $this->linePath($pts);

        $out = [];
        $out[] = '<div id="highcharts-'.$uid.'" dir="ltr" style="position: relative; overflow: hidden; width: '.$width.'px; height: '.$height.'px; text-align: left; line-height: normal; z-index: 0; padding: 0px;" class="highcharts-container">';
        $out[] = '<svg version="1.1" class="highcharts-root" style="font-family: Helvetica, Arial, sans-serif; font-size: 1rem;" xmlns="http://www.w3.org/2000/svg" width="'.$width.'" height="'.$height.'" viewBox="0 0 '.$width.' '.$height.'" role="img" aria-label="">';
        $out[] = '<desc>Created with Highcharts 11.4.8</desc>';
        $out[] = '<defs><clipPath id="highcharts-'.$uid.'-clip-"><rect x="'.$margin.'" y="'.$margin.'" width="'.$plotW.'" height="'.$plotH.'" fill="none"></rect></clipPath></defs>';

        // background
        $out[] = '<rect fill="transparent" class="highcharts-background" filter="none" x="0" y="0" width="'.$width.'" height="'.$plotH.'" rx="0" ry="0"></rect>';
        $out[] = '<rect fill="none" class="highcharts-plot-background" x="'.$margin.'" y="'.$margin.'" width="'.$plotW.'" height="'.$plotH.'" filter="none"></rect>';

        // x ızgarası (dikey) — 4 bölme
        $out[] = '<g class="highcharts-grid highcharts-xaxis-grid" data-z-index="1">';
        for ($i = 1; $i <= 4; $i++) {
            $x = round($margin + ($plotW / 5) * $i + 0.5, 1);
            if ($x > $margin + $plotW) {
                continue;
            }
            $out[] = '<path fill="none" stroke="'.self::GRID.'" stroke-width="0" stroke-dasharray="none" data-z-index="1" class="highcharts-grid-line" d="M '.$x.' '.$margin.' L '.$x.' '.($margin + $plotH).'" opacity="1"></path>';
        }
        $out[] = '</g>';

        // y ızgarası (yatay) — 5 bölme
        $out[] = '<g class="highcharts-grid highcharts-yaxis-grid" data-z-index="1">';
        for ($i = 0; $i <= 4; $i++) {
            $y = round($margin + ($plotH / 4) * $i + 0.5, 1);
            $out[] = '<path fill="none" stroke="'.self::GRID.'" stroke-width="1" stroke-dasharray="none" data-z-index="1" class="highcharts-grid-line" d="M '.$margin.' '.$y.' L '.($margin + $plotW).' '.$y.'" opacity="1"></path>';
        }
        $out[] = '</g>';

        $out[] = '<rect fill="none" class="highcharts-plot-border" data-z-index="1" x="'.$margin.'" y="'.$margin.'" width="'.$plotW.'" height="'.$plotH.'" stroke-width="1"></rect>';

        // x ekseni
        $out[] = '<g class="highcharts-axis highcharts-xaxis" data-z-index="2">';
        for ($i = 1; $i <= 4; $i++) {
            $x = round($margin + ($plotW / 5) * $i + 0.5, 1);
            if ($x > $margin + $plotW) {
                continue;
            }
            $out[] = '<path fill="none" stroke="'.self::AXIS.'" stroke-width="0" d="M '.$x.' '.($margin + $plotH).' L '.$x.' '.($margin + $plotH + $xAxisH).'" opacity="1"></path>';
        }
        $out[] = '<path fill="none" class="highcharts-axis-line" stroke="'.self::AXIS.'" stroke-width="1" data-z-index="7" d="M '.$margin.' '.($margin + $plotH + 0.5).' L '.($margin + $plotW).' '.($margin + $plotH + 0.5).'"></path>';
        $out[] = '</g>';

        // y ekseni
        $out[] = '<g class="highcharts-axis highcharts-yaxis" data-z-index="2">';
        $out[] = '<path fill="none" class="highcharts-axis-line" stroke="'.self::AXIS.'" stroke-width="0" data-z-index="7" d="M '.($margin + $plotW).' '.$margin.' L '.($margin + $plotW).' '.($margin + $plotH).'"></path>';
        $out[] = '</g>';

        // seri (area)
        $out[] = '<g class="highcharts-series-group" data-z-index="3" filter="none">';
        $out[] = '<g class="highcharts-series highcharts-series-0 highcharts-area-series" data-z-index="0.1" opacity="1" transform="translate('.$margin.','.$margin.') scale(1 1)" clip-path="url(#highcharts-'.$uid.'-clip-)">';
        $out[] = '<path fill="'.self::AREA.'" d="'.$areaD.'"></path>';
        $out[] = '<g class="highcharts-series highcharts-series-0 highcharts-area-series" data-z-index="0.1" opacity="1" transform="translate(0,0) scale(1 1)">';
        $out[] = '<path class="highcharts-graph" fill="none" stroke="'.self::SERIES.'" stroke-width="1" d="'.$lineD.'"></path>';
        $out[] = '</g>';
        $out[] = '<g class="highcharts-markers">';
        $out[] = '<path fill="'.self::SERIES.'" d="M '.round($pts[0][0], 4).' '.round($pts[0][1] - 2.5, 4).' L '.round($pts[0][0] + 2.5, 4).' '.round($pts[0][1], 4).' L '.round($pts[0][0], 4).' '.round($pts[0][1] + 2.5, 4).' L '.round($pts[0][0] - 2.5, 4).' '.round($pts[0][1], 4).' Z" opacity="1"></path>';
        $out[] = '</g>';
        $out[] = '</g>';
        $out[] = '</g>';

        // eksen etiketleri (referans: highcharts-axis-labels > text.highcharts-tick)
        $out[] = '<g class="highcharts-axis-labels highcharts-xaxis-labels" data-z-index="7">';
        for ($i = 1; $i <= 4; $i++) {
            $x = round($margin + ($plotW / 5) * $i + 0.5, 1);
            if ($x > $margin + $plotW) {
                continue;
            }
            $out[] = '<text x="'.$x.'" text-anchor="middle" transform="translate(0,0)" class="highcharts-tick" style="color: '.self::TICK.'; font-size:0.6em; fill: '.self::TICK.';" y="'.($margin + $plotH + 8).'" opacity="1">'.$i.'</text>';
        }
        $out[] = '</g>';

        $out[] = '</svg>';

        // başlık / altyazı / credits (referans)
        $out[] = '<text x="'.($margin).'" class="highcharts-title" style="color: '.self::AXIS.'; font-size:1em; fill: '.self::AXIS.';" y="18" transform="translate(0,0)" opacity="1"></text>';
        $out[] = '<text x="'.($margin).'" class="highcharts-caption" text-anchor="start" style="color: '.self::TICK.'; font-size:0.6em; fill: '.self::TICK.';" y="'.($margin + $plotH + 14).'" transform="translate(0,0)" opacity="1"></text>';
        $out[] = '<text x="'.($width - $margin).'" class="highcharts-credits" text-anchor="end" data-z-index="8" y="'.($height - 5).'" style="cursor: pointer; color: rgb(153, 153, 153); font-size: 0.6em; fill: rgb(153, 153, 153);">bloomberght.com</text>';

        $out[] = '</div>';

        return implode('', $out);
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $pts
     */
    private function areaPath(array $pts, int $plotH, int $plotW): string
    {
        $d = 'M '.round($pts[0][0], 4).' '.$pts[0][1];
        foreach (array_slice($pts, 1) as [$x, $y]) {
            $d .= ' L '.round($x, 4).' '.$y;
        }
        $lastX = round(end($pts)[0], 4);

        return $d.' L '.$lastX.' '.$plotH.' L '.round($pts[0][0], 4).' '.$plotH.' Z';
    }

    /**
     * @param  array<int, array{0: float, 1: float}>  $pts
     */
    private function linePath(array $pts): string
    {
        $d = 'M '.round($pts[0][0], 4).' '.$pts[0][1];
        foreach (array_slice($pts, 1) as [$x, $y]) {
            $d .= ' L '.round($x, 4).' '.$y;
        }

        return $d;
    }
}