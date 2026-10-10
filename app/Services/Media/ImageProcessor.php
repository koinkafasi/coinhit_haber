<?php

namespace App\Services\Media;

use Illuminate\Http\UploadedFile;
use RuntimeException;

/**
 * Yerel görsel işleme katmanı — `geoim.bloomberght.com/l/.../WxH` deseninin
 * lokal karşılığı (Bölüm 4, 12).
 *
 * Yüklenen görseller orijinal olarak `storage/app/public` altına kaydedilir;
 * isteğe göre ölçeklenmiş varyantlar `/gorsel/{klasör}/{dosya}?w=&h=`
 * ucundan (önbellekli) servis edilir. Bu sayede `srcset` + lazy
 * `transparent.gif` + `data-src` akışı bozulmaz.
 */
class ImageProcessor
{
    /**
     * Yüklenen dosyayı klasöre kaydeder ve servis URL'ini döndürür.
     */
    public function store(UploadedFile $file, string $folder = 'uploads'): string
    {
        $folder = trim($folder, '/');
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $extension = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true) ? $extension : 'jpg';

        $name = hash('sha1', ($file->getClientOriginalName() ?? '').microtime(true).random_int(1, PHP_INT_MAX)).'.'.$extension;

        $file->storeAs($folder, $name, 'public');

        return '/gorsel/'.$folder.'/'.$name;
    }

    /**
     * Kaynak görseli hedef genişlik/yüksekliğe ölçekler.
     *
     * @param  bool  $cover  true ise kırparak doldurur, false ise sığdırır.
     * @return string  Ham görüntü baytları
     */
    public function resize(string $absolutePath, ?int $width = null, ?int $height = null, bool $cover = false): string
    {
        if (! is_file($absolutePath)) {
            throw new RuntimeException('Görsel bulunamadı: '.$absolutePath);
        }

        $info = @getimagesize($absolutePath);

        if ($info === false) {
            throw new RuntimeException('Desteklenmeyen görsel biçimi.');
        }

        [$srcWidth, $srcHeight, $type] = $info;

        $source = $this->load($absolutePath, $type);

        if ($source === null) {
            throw new RuntimeException('Görsel açılamadı.');
        }

        $width ??= $srcWidth;
        $height ??= $srcHeight;

        if (! $cover) {
            $ratio = min($width / $srcWidth, $height / $srcHeight);
            $targetWidth = max(1, (int) round($srcWidth * $ratio));
            $targetHeight = max(1, (int) round($srcHeight * $ratio));
            $offsetX = 0;
            $offsetY = 0;
        } else {
            $ratio = max($width / $srcWidth, $height / $srcHeight);
            $targetWidth = $width;
            $targetHeight = $height;
            $scaledWidth = (int) round($srcWidth * $ratio);
            $scaledHeight = (int) round($srcHeight * $ratio);
            $offsetX = (int) round(($scaledWidth - $width) / 2);
            $offsetY = (int) round(($scaledHeight - $height) / 2);

            $canvas = imagecreatetruecolor($scaledWidth, $scaledHeight);
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            imagecopyresampled($canvas, $source, 0, 0, 0, 0, $scaledWidth, $scaledHeight, $srcWidth, $srcHeight);

            $cropped = imagecreatetruecolor($width, $height);
            imagecopy($cropped, $canvas, 0, 0, $offsetX, $offsetY, $width, $height);
            imagedestroy($canvas);
            imagedestroy($source);

            return $this->encode($cropped, $type);
        }

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $srcWidth, $srcHeight);
        imagedestroy($source);

        return $this->encode($canvas, $type);
    }

    /**
     * @return \GdImage|null
     */
    private function load(string $path, int $type): ?\GdImage
    {
        return match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path) ?: null,
            IMAGETYPE_PNG => @imagecreatefrompng($path) ?: null,
            IMAGETYPE_GIF => @imagecreatefromgif($path) ?: null,
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? (@imagecreatefromwebp($path) ?: null) : null,
            default => null,
        };
    }

    private function encode(\GdImage $image, int $type): string
    {
        ob_start();

        match ($type) {
            IMAGETYPE_PNG => imagepng($image),
            IMAGETYPE_GIF => imagegif($image),
            IMAGETYPE_WEBP => function_exists('imagewebp') ? imagewebp($image, null, 82) : imagejpeg($image, null, 82),
            default => imagejpeg($image, null, 82),
        };

        $bytes = (string) ob_get_clean();
        imagedestroy($image);

        return $bytes;
    }
}