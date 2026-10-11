<?php

return [
    /*
     * Cache stratejileri
     */
    'cache_strategies' => [
        'semantic' => [
            'type' => 'semantic',
            'ttl' => 3600, // 1 saat
            'max_size' => 1000,
            'compression' => 'rtk'
        ],
        'contextual' => [
            'type' => 'contextual',
            'ttl' => 1800, // 30 dakika
            'max_size' => 500,
            'compression' => 'caveman'
        ],
        'response' => [
            'type' => 'response',
            'ttl' => 7200, // 2 saat
            'max_size' => 2000,
            'compression' => 'none'
        ]
    ],

    /*
     * Performans optimizasyonu
     */
    'performance' => [
        'prefetch' => [
            'enabled' => true,
            'threshold' => 100,
            'strategy' => 'predictive'
        ],
        'batch_processing' => [
            'enabled' => true,
            'max_batch_size' => 10,
            'timeout_ms' => 1000
        ],
        'memory_management' => [
            'enabled' => true,
            'max_memory_mb' => 512,
            'gc_threshold' => 0.8
        ]
    ],

    /*
     * Maliyet optimizasyonu
     */
    'cost_optimization' => [
        'token_saving' => [
            'enabled' => true,
            'strategy' => 'rtk+caveman',
            'target_reduction' => 0.7
        ],
        'budget_control' => [
            'enabled' => true,
            'daily_limit' => 0.50,
            'monthly_limit' => 15.00,
            'alerts' => [
                'threshold' => 0.8,
                'notifications' => true
            ]
        ]
    ]
];
