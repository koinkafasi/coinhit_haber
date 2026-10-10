<?php

namespace App\Support;

class Media
{
    /**
     * Adlandırılmış görsel boyut sözleşmesini döndürür.
     *
     * @return array{width?: int|null, height?: int|null, srcset?: array<int, int>}
     */
    public static function contract(string $name): array
    {
        return (array) config("media.sizes.{$name}", []);
    }

    /**
     * Lazy-load placeholder (1x1 şeffaf GIF) yolu.
     */
    public static function transparentGif(): string
    {
        return (string) config('media.transparent_gif', '/img/transparent.gif');
    }

    /**
     * Bir URL'den srcset üretir: `url?w=320 320w, url?w=640 640w`.
     *
     * @param  array<int, int>  $widths
     */
    public static function srcset(string $url, array $widths): string
    {
        $parts = [];

        foreach ($widths as $width) {
            $parts[] = self::variant($url, (int) $width).' '.$width.'w';
        }

        return implode(', ', $parts);
    }

    /**
     * URL'e genişlik varyant parametresi ekler.
     */
    public static function variant(string $url, int $width): string
    {
        $separator = str_contains($url, '?') ? '&' : '?';

        return $url.$separator.'w='.$width;
    }
}
