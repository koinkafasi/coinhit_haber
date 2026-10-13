<?php

require __DIR__.'/../vendor/autoload.php';

use App\Models\MarketInstrument;
use App\Models\MarketSnapshot;

$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// En son taze snapshot kaynaklarını yükle (60 dk içi)
$fresh = MarketSnapshot::where('captured_at', '>', now()->subMinutes(60))
    ->get()
    ->unique('market_instrument_id')
    ->keyBy('market_instrument_id');

$insts = MarketInstrument::where('is_active', true)->get()->groupBy('group');

$groupNames = MarketInstrument::GROUPS;

foreach ($insts as $group => $items) {
    $name = $groupNames[$group] ?? $group;
    $real = 0;
    $sim = [];
    foreach ($items as $it) {
        $s = $fresh->get($it->id);
        if ($s !== null) {
            $real++;
        } else {
            $sim[] = $it->code;
        }
    }
    $total = count($items);
    $pct = $total > 0 ? round($real / $total * 100) : 0;
    printf("%-18s %2d enstr => GERÇEK %3d (%3d%%), SİMÜLE %2d%s\n",
        $group,
        $total,
        $real,
        $pct,
        count($sim),
        count($sim) ? ' => '.implode(',', array_slice($sim, 0, 40)) : ''
    );
}
