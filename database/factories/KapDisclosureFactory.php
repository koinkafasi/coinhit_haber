<?php

namespace Database\Factories;

use App\Models\KapDisclosure;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KapDisclosure>
 */
class KapDisclosureFactory extends Factory
{
    protected $model = KapDisclosure::class;

    public function definition(): array
    {
        $company = fake('tr_TR')->company();

        return [
            'kap_id' => fake()->unique()->numberBetween(340000, 399999),
            'company_code' => strtoupper(fake()->lexify('????')),
            'company_name' => $company,
            'company_slug' => str($company)->slug(),
            'title' => 'Özel Durum Açıklaması (Genel)',
            'category' => 'Özel Durum Açıklaması (Genel)',
            'summary' => fake('tr_TR')->sentence(12),
            'body' => fake('tr_TR')->paragraphs(3, true),
            'published_at' => now()->subHours(fake()->numberBetween(1, 120)),
        ];
    }
}