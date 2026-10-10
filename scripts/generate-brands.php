<?php

/**
 * Marka / logo / favicon / placeholder görsel üretici (CH-17 demo varlıkları).
 * Çalıştırma: php scripts/generate-brands.php
 */

$root = dirname(__DIR__) . '/public';

/* ---------- SVG assets ---------- */

// Logo (yatay)
file_put_contents("$root/brand/logo.svg", <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 520 150" role="img" aria-label="Bloomberg HT">
  <rect width="520" height="150" rx="12" fill="#101418"/>
  <text x="26" y="64" font-family="Georgia, 'Times New Roman', serif" font-size="40" font-weight="700" fill="#FFE400">BLOOMBERG</text>
  <text x="28" y="106" font-family="Georgia, 'Times New Roman', serif" font-size="28" font-weight="700" fill="#FFFFFF" letter-spacing="10">H T</text>
  <rect x="26" y="120" width="120" height="6" fill="#FFE400"/>
  <text x="430" y="40" font-family="Arial, sans-serif" font-size="13" fill="#9DA6B0">demo · klon</text>
</svg>
SVG);

// Favicon (kare)
file_put_contents("$root/brand/favicon.svg", <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
  <rect width="64" height="64" rx="12" fill="#101418"/>
  <text x="32" y="44" text-anchor="middle" font-family="Georgia, 'Times New Roman', serif" font-size="34" font-weight="700" fill="#FFE400">HT</text>
</svg>
SVG);

/* ---------- Placeholder görseller (haber kartları / grafikler) ---------- */

$placeholders = [
    1 => ['#1f6feb', '#e8f1ff', 'Borsa'],
    2 => ['#24A278', '#e6f7f0', 'Döviz'],
    3 => ['#BD1B2E', '#fdeaea', 'Enerji'],
    4 => ['#384552', '#eef1f4', 'Makro'],
    5 => ['#9b59b6', '#f3eafb', 'Fintech'],
    6 => ['#d9822b', '#fdf1e3', 'Tarım'],
    7 => ['#0e8a8a', '#e2f6f6', 'Kripto'],
    8 => ['#5a5d63', '#eef0f2', 'Gündem'],
];

foreach ($placeholders as $i => [$bg, $fg, $label]) {
    $line = match (true) {
        $i % 2 === 0 => 'M0,290 C140,220 240,330 380,230 C460,180 520,210 640,150',
        default => 'M0,240 C150,180 260,290 400,210 C480,160 560,190 640,120',
    };
    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 360">
  <rect width="640" height="360" fill="$bg"/>
  <path d="$line" fill="none" stroke="$fg" stroke-width="8" opacity="0.55"/>
  <circle cx="120" cy="236" r="6" fill="$fg"/>
  <circle cx="316" cy="210" r="6" fill="$fg"/>
  <circle cx="520" cy="150" r="6" fill="$fg"/>
  <text x="28" y="330" font-family="Georgia, serif" font-size="34" font-weight="bold" fill="$fg" opacity="0.9">$label</text>
  <text x="28" y="52" font-family="Arial, sans-serif" font-size="16" fill="$fg" opacity="0.7">BLOOMBERG HT · DEMO</text>
</svg>
SVG;
    file_put_contents("$root/img/placeholders/placeholder-$i.svg", $svg);
}

/* ---------- PNG / ICO (GD) ---------- */

$font = '/usr/share/fonts/truetype/dejavu/DejaVuSerif-Bold.ttf';
$fontSans = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';

if (! function_exists('imagecreatetruecolor')) {
    echo "GD yok; PNG/ICO üretilemedi.\n";
    exit(0);
}

function renderCanvas(int $w, int $h, int $bgColor): \GdImage
{
    $img = imagecreatetruecolor($w, $h);
    imagefill($img, 0, 0, $bgColor);
    return $img;
}

function rgb(int $hex): array
{
    return [($hex >> 16) & 0xff, ($hex >> 8) & 0xff, $hex & 0xff];
}

function color(GdImage $img, int $hex): int
{
    [$r, $g, $b] = rgb($hex);
    return imagecolorallocate($img, $r, $g, $b);
}

// logo.png 480x140
$img = renderCanvas(480, 140, 0x101418);
$yellow = color($img, 0xFFE400);
$white = color($img, 0xFFFFFF);
imagettftext($img, 34, 0, 24, 62, $yellow, $font, 'BLOOMBERG');
imagettftext($img, 24, 0, 26, 104, $white, $font, 'H T');
imagefilledrectangle($img, 24, 116, 140, 122, $yellow);
imagepng($img, "$root/brand/logo.png");
imagedestroy($img);

// apple-touch-icon.png 180x180
$img = renderCanvas(180, 180, 0x101418);
$yellow = color($img, 0xFFE400);
imagettftext($img, 76, 0, 24, 130, $yellow, $font, 'HT');
imagepng($img, "$root/apple-touch-icon.png");
imagedestroy($img);

// og-default.png 1200x630 (basit grafik çizgisi)
$img = renderCanvas(1200, 630, 0x101418);
$yellow = color($img, 0xFFE400);
$line = color($img, 0xFFD600);
for ($i = 0; $i < 1200; $i += 4) {
    $y = 470 + intdiv($i % 1200, 5) - 20 * sin($i / 90);
    imagefilledellipse($img, $i, (int) $y, 4, 4, $line);
}
imagettftext($img, 44, 0, 40, 150, $yellow, $font, 'BLOOMBERG HT');
imagettftext($img, 22, 0, 40, 190, color($img, 0xffffff), $fontSans, 'Türkiye\'nin ekonomi platformu');
imagepng($img, "$root/img/og-default.png");
imagedestroy($img);

// PWA manifest ikonları (spec §11.5) — 192x192 ve 512x512
foreach ([192, 512] as $size) {
    $img = renderCanvas($size, $size, 0x101418);
    $yellow = color($img, 0xFFE400);
    $textSize = (int) ($size * 0.42);
    imagettftext($img, $textSize, 0, (int) ($size * 0.13), (int) ($size * 0.72), $yellow, $font, 'HT');
    imagepng($img, "$root/img/manifest-$size.png");
    imagedestroy($img);
}

// favicon.ico — ico container içinde PNG (32px)
function buildIco(string $pngData): string
{
    $header = pack('v3', 0, 1, 1);
    $entry = pack('C4v2V2', 32, 32, 0, 0, 1, 32, strlen($pngData), 22);
    return $header . $entry . $pngData;
}

$img = renderCanvas(32, 32, 0x101418);
$yellow = color($img, 0xFFE400);
imagettftext($img, 20, 0, -1, 24, $yellow, $font, 'HT');
ob_start();
imagepng($img);
$png32 = ob_get_clean();
imagedestroy($img);
file_put_contents("$root/favicon.ico", buildIco($png32));

echo "Varlıklar üretildi.\n";
