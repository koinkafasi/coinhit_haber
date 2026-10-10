# Bloomberg HT Klonu — Birebir Site Replikası

`https://www.bloomberght.com` sitesinin her sayfasını, her alanını ve özelliğini birebir kopyalamayı hedefleyen finans haber ve piyasa veri portalı.

- **Teknoloji:** Laravel 12 + Blade (SSR) + PostgreSQL
- **Frontend:** Blade/SSR + Tailwind (tasarım aşamasında), Swiper, Chart.js v4.5.1, Highcharts, blazy
- **Veri:** PostgreSQL (`coinhit`); test ortamı `sqlite :memory:`
- **Durum:** Stage 0 — Hazırlık A (CH-17): çalışan Laravel iskeleti + DB + temel migration/seed + demo içerik.

## Ortam & Kurulum

```bash
cp .env.example .env      # DB bağlantısını .env içinde düzenleyin
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed       # admin + taksonomi + ~92 demo haber + son dakika + yazar + grafik
php artisan serve         # http://localhost:8000
```

`.env.example` içindeki varsayılan veritabanı ayarları:

```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=coinhit
DB_USERNAME=arastam
```

Test ortamı (`phpunit.xml`) `sqlite :memory:` kullanır; hiçbir harici servise bağımlı değildir.

## Komutlar

```bash
php artisan migrate:fresh --seed   # DB'yi sıfırla + doldur
php artisan route:list             # rota dökümü
php artisan test                   # testler (yeşil olmalı)
php scripts/generate-brands.php    # marka/logo/favicon + placeholder görselleri üretir
```

## Yapı

| Alan | Açıklama |
|---|---|
| `app/Http/Controllers/` | Sayfa denetleyicileri |
| `app/Models/` | Eloquent modelleri (News, Category, Author, BreakingNews, ChartArchive, SiteSetting, User) |
| `database/migrations/` | Şema (`users`, `categories`, `authors`, `news`, `breaking_news`, `chart_archives`, `site_settings`) |
| `database/seeders/` | Demo içerik ve taksonomi seed'leri |
| `resources/views/` | Blade şablonları (layout + ana sayfa + haber listesi/detay + son dakika + grafik arşivi + yazarlar) |
| `public/` | Marka görselleri, favicon, placeholder'lar, CSS |
| `docs/` | MASTER SPEC + görev genel bilgileri (task1..task13) |

## Dokümantasyon

- [BLOOMBERGHT_CLONE_SPEC.md](docs/BLOOMBERGHT_CLONE_SPEC.md) — Master spec (2026-10-10, v-final2)
- [task1.md](docs/task1.md) … [task13.md](docs/task13.md) — Görev genel bilgileri

## Admin Kullanıcı (demo)

- E-posta: `admin@coinhit.local`
- Şifre: `password`  *(yalnızca yerel demo; üretimde değiştirin)*

## Kapsam Dışı (sonraki görevler)

Tasarım sistemi (Tailwind + fontlar), piyasa veri katmanı, sayfa bazlı modüller, SEO/sitemap/RSS/PWA ve reklam katmanı sonraki görevlerde (CH-4 ve sonrası) yapılır.

## Lisans

Demo eğitim/kopya projesidir; gerçek Bloomberg HT adı/logosu marka sahiplerine aittir.
