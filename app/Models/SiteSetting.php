<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    /** @use HasFactory<SiteSettingFactory> */
    use HasFactory, HasTranslations;

    protected $with = ['translations'];

    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * @return array<string, string>
     */
    public function translatable(): array
    {
        return [
            'value' => 'value',
        ];
    }

    public function translationModel(): string
    {
        return SiteSettingTranslation::class;
    }

    public function translationForeignKey(): string
    {
        return 'key';
    }

    public function translationOwnerKey(): string
    {
        return 'key';
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $setting = static::where('key', $key)->first();

        return $setting?->value ?? $default;
    }
}
