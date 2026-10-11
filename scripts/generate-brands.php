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

/* ---------- Placeholder görseller (haber kartları / grafikler) ----------
   CH-25: editoryal/foto hissi veren zengin görseller — düz renk kutu
   yerine degrade zemin + kod/kategori + haber manşeti taslağı. */

$phThemes = [
    1 => ['#06121e', '#0d2a45', '#1f6feb', '#FFE400', 'BORSA', 'BIST 100 günü yükselişle tamamladı'],
    2 => ['#071b14', '#0f3c2c', '#24A278', '#FFE400', 'DÖVİZ', 'Dolar/TL haftaya yatay başladı'],
    3 => ['#21090d', '#4b141c', '#BD1B2E', '#FFE400', 'ENERJİ', 'Petrol fiyatlarında sert hareket'],
    4 => ['#101826', '#26344a', '#384552', '#FFE400', 'MAKRO', 'Küresel piyasalar Fed mesajlarını izliyor'],
    5 => ['#150a24', '#332052', '#9b59b6', '#FFE400', 'FINTECH', 'Finans teknolojilerinde yeni dönem'],
    6 => ['#231104', '#4b2a12', '#d9822b', '#FFE400', 'TARIM', 'Emtia fiyatlarında güncel görünüm'],
    7 => ['#031b1b', '#0a3838', '#0e8a8a', '#FFE400', 'KRİPTO', 'Bitcoin kritik eşiği test ediyor'],
    8 => ['#17181c', '#2e3036', '#5a5d63', '#FFE400', 'GÜNDEM', "Türkiye'nin ekonomi gündemi dolu dolu"],
];

foreach ($phThemes as $i => [$d1, $d2, $accent, $brand, $label, $headline]) {
    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 360">
  <defs>
    <linearGradient id="bg$i" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="$d1"/>
      <stop offset="1" stop-color="$d2"/>
    </linearGradient>
    <radialGradient id="glow$i" cx="0.75" cy="0.25" r="0.9">
      <stop offset="0" stop-color="$accent" stop-opacity="0.5"/>
      <stop offset="1" stop-color="$accent" stop-opacity="0"/>
    </radialGradient>
  </defs>
  <rect width="640" height="360" fill="url(#bg$i)"/>
  <rect width="640" height="360" fill="url(#glow$i)"/>
  <circle cx="520" cy="90" r="34" fill="none" stroke="$accent" stroke-width="1.5" opacity="0.5"/>
  <circle cx="560" cy="70" r="5" fill="$accent" opacity="0.6"/>
  <path d="M0,300 C120,255 200,340 320,290 S520,250 640,285" fill="none" stroke="#ffffff" stroke-width="2" opacity="0.16"/>
  <g fill="#030a07">
    <rect x="28" y="34" width="8" height="64" fill="$brand"/>
    <text x="48" y="80" font-family="Waterfall, Georgia, serif" font-size="42" font-weight="bold" letter-spacing="2" fill="#ffffff" opacity="0.95">$label</text>
  </g>
  <text x="28" y="52" font-family="Arial, sans-serif" font-size="15" letter-spacing="3" fill="#ffffff" opacity="0.55">BLOOMBERG HT</text>
  <g opacity="0.92">
    <rect x="28" y="274" width="28" height="3" fill="$brand"/>
    <text x="28" y="308" font-family="Georgia, serif" font-size="27" font-weight="bold" fill="#ffffff">$headline</text>
    <rect x="28" y="324" width="340" height="4" fill="#ffffff" opacity="0.35"/>
  </g>
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
