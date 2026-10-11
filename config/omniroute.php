<?php

return [
    'base_url' => env('OMNIR_ROUTE_BASE_URL', 'http://localhost:20128/v1'),
    'api_key' => env('OMNIR_ROUTE_API_KEY', ''),
    'default_combo' => env('OMNIR_ROUTE_COMBOS', 'best-effort'),
    'enabled' => env('OMNIR_ROUTE_ENABLED', false),
];
