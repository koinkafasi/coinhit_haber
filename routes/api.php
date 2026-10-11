<?php

use App\Http\Controllers\API\OmniRouteController;
use Illuminate\Support\Facades\Route;

Route::middleware(['omniroute.auth'])->group(function () {
    Route::post('/omniroute/chat', [OmniRouteController::class, 'chat']);
    Route::get('/omniroute/combos', [OmniRouteController::class, 'combos']);
});