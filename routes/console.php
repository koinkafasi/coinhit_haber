<?php

use App\Console\Commands\SnapshotMarket;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// CH-2 — CoinGecko kripto fiyat anlık görüntüsü: saatte bir, çakışmasın.
Schedule::command(SnapshotMarket::class)->hourly()->withoutOverlapping();

// Gerçek piyasa verisi toplayıcı (Go): TCMB + Yahoo + goldprice — 15 dk'da bir.
Schedule::exec(base_path('market-collector/run.sh'))
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->description('Market collector (TCMB/Yahoo/goldprice)');
