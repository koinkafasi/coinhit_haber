<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * i18n yardımcıları (master spec §18.2/§18.3).
 *
 * tr varsayılan ve prefixsiz; diğer etkin diller `/en` gibi prefix ile
 * erişilir. Tüm URL üretimi locale'e duyarlı olmalıdır.
 */
class Locale
{
    /**
     * @return array<int, string>
     */
    public static function supported(): array
    {
        return array_keys((array) config('locales.supported', ['tr' => [], 'en' => []]));
    }

    public static function default(): string
    {
        return (string) config('locales.default', config('app.locale', 'tr'));
    }

    public static function fallback(): string
    {
        return (string) config('locales.fallback', config('app.fallback_locale', 'tr'));
    }

    public static function current(): string
    {
        $locale = app()->getLocale();

        return in_array($locale, self::supported(), true) ? $locale : self::default();
    }

    public static function isDefault(): bool
    {
        return self::current() === self::default();
    }

    /**
     * Varsayılan dışındaki (prefix ile erişilen) diller.
     *
     * @return array<int, string>
     */
    public static function prefixed(): array
    {
        return array_values(array_filter(
            self::supported(),
            fn (string $locale): bool => $locale !== self::default()
        ));
    }

    /**
     * İstek yolundan locale'i çıkarır. Bilinmeyen locale prefix'i için null.
     */
    public static function detect(Request $request): ?string
    {
        $first = $request->segment(1);
        $default = self::default();

        if ($first === null || $first === '') {
            return $default;
        }

        if (preg_match('/^[a-z]{2}$/', $first)) {
            if ($first === $default) {
                return null;
            }

            return in_array($first, self::supported(), true) ? $first : null;
        }

        return $default;
    }

    /**
     * Locale'e duyarlı route üretimi.
     */
    public static function route(string $name, mixed $params = [], bool $absolute = true): string
    {
        return route(self::routeName($name), $params, $absolute);
    }

    public static function routeName(string $name): string
    {
        return self::isDefault() ? $name : self::current().'.'.$name;
    }

    /**
     * Locale önekli göreli URL (`/en/haberler`); tr'de değişmez.
     */
    public static function url(string $path): string
    {
        $path = '/'.ltrim($path, '/');

        if (self::isDefault()) {
            return $path;
        }

        return '/'.self::current().($path === '/' ? '' : $path);
    }

    /**
     * Aynı sayfanın diğer dildeki eşdeğerine geçiş URL'i.
     */
    public static function switchUrl(string $locale): string
    {
        $path = trim(request()->path(), '/');
        $segments = $path === '' ? [] : explode('/', $path);

        if (! empty($segments) && preg_match('/^[a-z]{2}$/', $segments[0]) && in_array($segments[0], self::supported(), true)) {
            array_shift($segments);
        }

        if ($locale !== self::default()) {
            array_unshift($segments, $locale);
        }

        $newPath = '/'.implode('/', $segments);
        $query = request()->getQueryString();

        return $newPath.($query ? '?'.$query : '');
    }

    /**
     * Belirtilen dil için önekli göreli URL (aktif locale'den bağımsız).
     */
    public static function urlFor(string $locale, string $path): string
    {
        $path = '/'.ltrim($path, '/');

        if ($locale === self::default()) {
            return $path;
        }

        return '/'.$locale.($path === '/' ? '' : $path);
    }

    /**
     * Navigasyon etiket çevirisi (lang/nav.php); yoksa TR etiketi aynen döner.
     */
    public static function navLabel(string $label): string
    {
        $key = 'nav.'.$label;

        return app('translator')->has($key) ? __($key) : $label;
    }

    /**
     * Navigasyon bağlantısını locale'e duyarlı hale getirir; dış linklere dokunmaz.
     */
    public static function navUrl(string $url): string
    {
        if (str_starts_with($url, 'http') || str_starts_with($url, '#') || str_starts_with($url, 'mailto:')) {
            return $url;
        }

        return self::url($url);
    }

    public static function ogLocale(?string $locale = null): string
    {
        $locale ??= self::current();

        return (string) (config('locales.supported.'.$locale.'.og') ?? $locale.'_'.strtoupper($locale));
    }

    /**
     * Verilen dil dışındaki etkin dil kodları.
     *
     * @return array<int, string>
     */
    public static function alternates(?string $locale = null): array
    {
        $locale ??= self::current();

        return array_values(array_filter(
            self::supported(),
            fn (string $item): bool => $item !== $locale
        ));
    }
}
