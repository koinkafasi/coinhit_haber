<?php

return [
    /*
     * Maliyet Optimizasyonu için combo'lar
     * - Geminyi öncelikli yap (en ucuz)
     * - OpenAI ve Anthropic fallback
     * - 0.05 USD limiti ile bütçe kontrolü
     */
    'cost_optimized' => [
        'strategy' => 'auto',
        'providers' => ['gemini', 'openai', 'anthropic'],
        'fallback' => true,
        'cache' => true,
        'budget' => [
            'maxCost' => 0.05,
            'currency' => 'USD'
        ],
        'optimization' => [
            'context_reduction' => true,
            'compression' => 'rtk',
            'token_savings' => true
        ]
    ],

    /*
     * Yüksek Kalite için combo
     * - En iyi modelleri tercih et
     * - Fallback yok (öncelikli modeli kullan)
     */
    'quality_first' => [
        'strategy' => 'auto',
        'providers' => ['openai/gpt-4o', 'anthropic/claude-3-opus', 'gemini/gemini-1.5-pro'],
        'fallback' => false,
        'cache' => true,
        'quality' => [
            'accuracy' => 'high',
            'speed' => 'medium',
            'reasoning' => true
        ]
    ],

    /*
     * Hızlı Yanıt için combo
     * - En hızlı modelleri kullan
     * - Caching etkin
     */
    'fast_response' => [
        'strategy' => 'auto',
        'providers' => ['gemini/gemini-1.5-flash', 'openai/gpt-4o-mini'],
        'fallback' => false,
        'cache' => true,
        'performance' => [
            'latency' => 'low',
            'throughput' => 'high'
        ]
    ],

    /*
     * Otomatik的最佳 combo
     * - Tüm stratejileri birleştir
     * - Dinamik seçim yap
     */
    'auto_best' => [
        'strategy' => 'auto',
        'providers' => ['openai', 'anthropic', 'gemini'],
        'fallback' => true,
        'cache' => true,
        'adaptive' => [
            'performance_based' => true,
            'cost_based' => true,
            'quality_based' => true
        ]
    ],

    /*
     * Geliştirme için combo
     * - Debug modu için özel ayarlar
     */
    'development' => [
        'strategy' => 'auto',
        'providers' => ['openai/gpt-4o-mini', 'gemini/gemini-1.5-flash'],
        'fallback' => true,
        'cache' => false,
        'debug' => [
            'verbose_logging' => true,
            'detailed_metrics' => true
        ]
    ]
];
