<?php

if (!function_exists('omniroute_combo')) {
    /**
     * OmniRoute combo seçimi
     *
     * @param string $type
     * @param array $options
     * @return string
     */
    function omniroute_combo($type = 'auto_best', $options = []) {
        $combos = [
            'cost_optimized' => 'auto/cost_optimized',
            'quality_first' => 'auto/quality_first',
            'fast_response' => 'auto/fast_response',
            'auto_best' => 'auto/auto_best',
            'development' => 'auto/development'
        ];

        return $combos[$type] ?? $combos['auto_best'];
    }
}

if (!function_exists('omniroute_model')) {
    /**
     * OmniRoute model seçimi
     *
     * @param string $category
     * @param string $quality
     * @return string
     */
    function omniroute_model($category = 'chat', $quality = 'balanced') {
        $models = [
            'chat' => [
                'high_accuracy' => 'openai/gpt-4o',
                'balanced' => 'auto/auto_best',
                'fast' => 'gemini/gemini-1.5-flash'
            ],
            'completion' => [
                'fastest' => 'gemini/gemini-1.5-flash',
                'highest_quality' => 'openai/gpt-4o'
            ],
            'code' => [
                'python' => 'openai/gpt-4o',
                'javascript' => 'anthropic/claude-3-haiku',
                'php' => 'gemini/gemini-1.5-pro'
            ]
        ];

        $category_models = $models[$category] ?? $models['chat'];
        return $category_models[$quality] ?? $category_models['balanced'];
    }
}

if (!function_exists('omniroute_cache_key')) {
    /**
     * Cache anahtarı oluşturma
     *
     * @param string $prefix
     * @param array $params
     * @return string
     */
    function omniroute_cache_key($prefix, $params = []) {
        $key = $prefix . ':' . md5(serialize($params));
        return substr($key, 0, 64); // 64 karakter sınırı
    }
}
