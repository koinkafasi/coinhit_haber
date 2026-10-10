<?php

namespace Database\Factories;

use App\Models\KapDisclosure;
use App\Models\KapField;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KapField>
 */
class KapFieldFactory extends Factory
{
    protected $model = KapField::class;

    public function definition(): array
    {
        return [
            'kap_disclosure_id' => KapDisclosure::factory(),
            'key' => 'CONSOLIDATION_METHOD',
            'label_tr' => 'Konsolidasyon Yöntemi',
            'label_en' => 'Consolidation Method',
            'value_tr' => 'Yok',
            'value_en' => 'None',
            'sort_order' => 0,
        ];
    }
}