<?php

return [
    /*
     * Chat için model ayarları
     */
    'chat' => [
        'default' => 'auto/auto_best',
        'fallback' => 'auto/cost_optimized',
        'quality' => [
            'high_accuracy' => 'openai/gpt-4o',
            'balanced' => 'auto/auto_best',
            'fast' => 'gemini/gemini-1.5-flash'
        ],
        'cost' => [
            'lowest' => 'gemini/gemini-1.5-flash',
            'balanced' => 'auto/cost_optimized'
        ]
    ],

    /*
     * Completion için model ayarları
     */
    'completion' => [
        'default' => 'auto/fast_response',
        'fallback' => 'auto/cost_optimized',
        'performance' => [
            'fastest' => 'gemini/gemini-1.5-flash',
            'highest_quality' => 'openai/gpt-4o'
        ]
    ],

    /*
     * Code için model ayarları
     */
    'code' => [
        'default' => 'auto/auto_best',
        'fallback' => 'openai/gpt-4o-mini',
        'specialized' => [
            'python' => 'openai/gpt-4o',
            'javascript' => 'anthropic/claude-3-haiku',
            'php' => 'gemini/gemini-1.5-pro'
        ]
    ],

    /*
     * Content için model ayarları
     */
    'content' => [
        'default' => 'auto/auto_best',
        'fallback' => 'gemini/gemini-1.5-flash',
        'optimization' => [
            'seo' => 'openai/gpt-4o',
            'creative' => 'anthropic/claude-3-opus',
            'technical' => 'gemini/gemini-1.5-pro'
        ]
    ]
];
