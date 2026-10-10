# Deploy Notları (CH-18)

Bloomberg HT klonu (Laravel 12 + Blade + PostgreSQL) için üretim kurulumu.
Bu doküman Stage 0 hazırlık adımıdır; sunucuya özel değerler (kullanıcı adı,
yol, şifre) **repoya yazılmaz**, ortam değişkeni / panel üzerinden verilir.

## 1. Gereksinimler

- PHP 8.2+ (pdo_pgsql, mbstring, openssl, tokenizer, xml, ctype, json, bcmath)
- PostgreSQL 14+
- Composer 2
- Node 20+ (yalnızca front-end asset üretimi için; SSR öncelikli)

## 2. Ortam dosyası

`.env.example` dosyasını `.env` olarak kopyalayın ve doldurun:

```bash
cp .env.example .env
php artisan key:generate
```

Zorunlu değerler:

| Anahtar | Açıklama |
| --- | --- |
| `APP_ENV` | Üretimde `production` |
| `APP_DEBUG` | Üretimde **`false`** |
| `APP_URL` | Gerçek alan adı (`https://...`) |
| `APP_TIMEZONE` | `Europe/Istanbul` |
| `APP_LOCALE` / `APP_FALLBACK_LOCALE` | `tr` |
| `DB_CONNECTION` | `pgsql` |
| `DB_HOST` / `DB_PORT` / `DB_DATABASE` | PostgreSQL bağlantısı |
| `DB_USERNAME` / `DB_PASSWORD` | Veritabanı kullanıcısı (panel/secret) |
| `ADMIN_EMAIL` / `ADMIN_PASSWORD` | İlk admin kullanıcısı (seed) |

> `ADMIN_PASSWORD` tanımlı değilse `db:seed` rastgele bir geçici şifre üretip
> konsola yazar. Şifre hiçbir zaman repoya veya `.env.example`'a yazılmaz.

## 3. Kurulum adımları

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force          # şemayı kur
php artisan db:seed --force          # admin + demo/referans veri
php artisan storage:link             # public/storage -> storage/app/public
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

`storage:link` sembolik bağlantısı yüklenen medyanın `/storage/...` üzerinden
sunulmasını sağlar. Bağlantıyı doğrulamak için:

```bash
php artisan storage:link   # "The [public/storage] link has been connected" benzeri çıktı
ls -l public/storage       # symlink -> ../storage/app/public
```

## 4. İzinler

`storage/` ve `bootstrap/cache/` web sunucusu kullanıcısı tarafından yazılabilir
olmalıdır:

```bash
chmod -R 775 storage bootstrap/cache
```

## 5. Docroot

Laravel'in kök dizini **`public/`** olacak şekilde ayarlanmalıdır. Paylaşımlı
cPanel hosting'de docroot değiştirilemiyorsa `public/` içeriği `public_html/`
altına kopyalanır (bkz. `.cpanel.yml` son adımı) ve uygulama dizini docroot
dışında tutulur.

## 6. cPanel otomatik deploy

`.cpanel.yml` dosyası cPanel **Git Version Control → Deploy HEAD Commit** akışına
hazırdır. İçindeki `<cpanel-kullanici>` ve PHP/Composer yollarını kendi
sunucunuza göre güncelleyin. Dosya sırasıyla:

1. üretim bağımlılıklarını kurar,
2. `migrate --force` çalıştırır,
3. `storage:link` kurar,
4. önbellekleri temizleyip yeniden oluşturur,
5. docroot `/public` değilse public içeriğini yayınlar.

## 7. Yayın sonrası doğrulama

```bash
php artisan route:list          # rotalar yükleniyor mu
php artisan about               # ortam/locale/timezone özeti
curl -I https://<alan-adi>/     # 200
curl -I https://<alan-adi>/bu-adres-yok   # 404 (özel hata sayfası)
```

## 8. Rollback

- Kod: cPanel Git panelinden önceki commit'e dönün ve yeniden deploy edin.
- Önbellek: `php artisan optimize:clear`.
- Veritabanı: `migrate` geri alınmaz; yayın öncesi `pg_dump` yedeği alın
  (`pg_dump "$DB_DATABASE" > backup-$(date +%F).sql`).