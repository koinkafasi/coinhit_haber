<?php

namespace App\Http\Controllers;

use App\Services\Media\ImageProcessor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Yerel görsel ölçekleme servisi (geoim deseninin lokal karşılığı).
 *
 * `GET /gorsel/{folder}/{file}?w=640&h=360&fit=cover`
 */
class ImageController extends Controller
{
    public function __construct(private readonly ImageProcessor $processor) {}

    public function show(Request $request, string $folder, string $file): BinaryFileResponse|Response
    {
        if (! $this->isSafe($folder) || ! $this->isSafe($file)) {
            throw new NotFoundHttpException;
        }

        $source = storage_path('app/public/'.$folder.'/'.$file);

        if (! is_file($source)) {
            throw new NotFoundHttpException;
        }

        $width = $this->positiveInt($request->query('w'));
        $height = $this->positiveInt($request->query('h'));

        if ($width === null && $height === null) {
            return response()->file($source, [
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ]);
        }

        $cover = $request->query('fit') === 'cover';
        $key = ($width ?? 0).'x'.($height ?? 0).($cover ? '-cover' : '');
        $cacheDir = storage_path('app/image-cache/'.$folder.'/'.$key);
        $cacheFile = $cacheDir.'/'.$file;

        if (is_file($cacheFile) && filemtime($cacheFile) >= filemtime($source)) {
            return response()->file($cacheFile, [
                'Cache-Control' => 'public, max-age=31536000, immutable',
            ]);
        }

        if (! is_dir($cacheDir)) {
            mkdir($cacheDir, 0o755, true);
        }

        $bytes = $this->processor->resize($source, $width, $height, $cover);
        file_put_contents($cacheFile, $bytes);

        return response($bytes, 200, [
            'Content-Type' => $this->mime($file),
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }

    private function isSafe(string $value): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9_\-\.]+$/', $value) && ! str_contains($value, '..');
    }

    private function positiveInt(mixed $value): ?int
    {
        if (! is_numeric($value)) {
            return null;
        }

        $int = (int) $value;

        return $int > 0 ? min($int, 4000) : null;
    }

    private function mime(string $file): string
    {
        return match (strtolower(pathinfo($file, PATHINFO_EXTENSION))) {
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };
    }
}