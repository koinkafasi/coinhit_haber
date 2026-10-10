<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Çok dilli dinamik içerik (master spec §18.5).
 *
 * Model alanları aktif locale'in çeviri tablosundan okunur; çeviri yoksa
 * fallback diline (`tr`), o da yoksa ana tablodaki değere düşer.
 */
trait HasTranslations
{
    /** @var array<int, Model>|null */
    protected ?array $translationCache = null;

    /**
     * Ana model alanı => çeviri tablosu kolonu eşlemesi.
     *
     * @return array<string, string>
     */
    abstract public function translatable(): array;

    abstract public function translationModel(): string;

    abstract public function translationForeignKey(): string;

    public function translationOwnerKey(): string
    {
        return 'id';
    }

    public function translations(): HasMany
    {
        return $this->hasMany(
            $this->translationModel(),
            $this->translationForeignKey(),
            $this->translationOwnerKey()
        );
    }

    /**
     * @return array<int, Model>
     */
    protected function loadedTranslations(): array
    {
        if ($this->translationCache === null) {
            $this->translationCache = $this->relationLoaded('translations')
                ? $this->getRelation('translations')->all()
                : $this->translations()->get()->all();
        }

        return $this->translationCache;
    }

    public function translation(?string $locale = null): ?Model
    {
        $locale ??= app()->getLocale();

        foreach ($this->loadedTranslations() as $translation) {
            if ($translation->getAttribute('locale') === $locale) {
                return $translation;
            }
        }

        return null;
    }

    /**
     * Belirtilen kolonun aktif (veya istenen) dildeki çeviri değeri.
     */
    public function translationValue(string $column, ?string $locale = null): mixed
    {
        $locale ??= app()->getLocale();

        $translation = $this->translation($locale);

        if ($translation && ! is_null($translation->getAttribute($column))) {
            return $translation->getAttribute($column);
        }

        $fallback = config('locales.fallback', 'tr');

        if ($locale !== $fallback) {
            $fallbackTranslation = $this->translation($fallback);

            if ($fallbackTranslation && ! is_null($fallbackTranslation->getAttribute($column))) {
                return $fallbackTranslation->getAttribute($column);
            }
        }

        return null;
    }

    public function getAttribute($key)
    {
        $map = $this->translatable();

        if (is_string($key) && array_key_exists($key, $map)) {
            $value = $this->translationValue($map[$key]);

            if (! is_null($value)) {
                return $value;
            }
        }

        return parent::getAttribute($key);
    }
}
