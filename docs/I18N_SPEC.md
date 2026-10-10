# §18 — i18n / Çok Dillilik (tr + en)

Bu bölüm master spec'in bir eki olup projeyi **çok dilli** hale getirir. Mevcut tüm challenge'lar (CH-17..CH-16) TR tek dilli varsayımıyla üretildi; bu challenge (CH-20) tüm arayüzü ve dinamik içeriği çift dilli yapar.

## 18.1 Desteklenen diller
- `tr` (varsayılan) ve `en`. Yön ikisi de `ltr`.
- `config/locales.php`: her dil için `code`, `native`, `direction`, `enabled`.

## 18.2 URL stratejisi (prefix)
- **tr (varsayılan):** kök, prefix YOK → mevcut "birebir" URL'ler korunur (`/haberler`, `/{slug}-{newsId}`, `/borsa`, ...).
- **en:** `/en` prefix ile → `/en/haberler`, `/en/{slug}-{newsId}`, `/en/borsa`, ...
- `/en` kökü İngilizce ana sayfayı verir. Bilinmeyen locale → 404.
- Dil değiştirici, aynı sayfanın diğer dildeki eşdeğerine yönlendirir (prefix ekle/kaldır).

## 18.3 Locale altyapısı
- `SetLocale` middleware: URL prefix'inden locale çıkarır, `App::setLocale()` yapar, `app()->getLocale()` her yerde doğrudur.
- Tüm URL üretimi locale-aware olmalı: `route()` yerine gerektiğinde `localized_route()` / prefix'li helper.
- Varsayılan dil isteklerinde prefix eklenmez.

## 18.4 Arayüz stringleri (UI)
- `lang/tr/*.php` ve `lang/en/*.php` (nav, footer, header, buton, form, hata sayfaları, meta, ticker, modül başlıkları...).
- Blade'lerdeki tüm sabit metinler `__('...')` / `@lang` ile değiştirilir.
- Tarih/sayı/para biçimleri locale'e göre (`Carbon` locale, TR `1.234,56`, EN `1,234.56`).

## 18.5 Dinamik içerik (DB) çevirisi — TAM
- Ayrı çeviri tabloları (index'lenebilir, sorgu dostu):
  - `news_translations` (news_id, locale, title, slug, summary, body, meta_title, meta_description)
  - `category_translations` (category_id, locale, name, slug, description)
  - `author_translations` (author_id, locale, name, bio, title)
  - `breaking_news_translations` (breaking_news_id, locale, text)
  - `site_settings_translations` (key, locale, value)
- `HasTranslations` trait: `$news->title` aktif locale'e göre döner; `trans('en')` ile belirli dil; yoksa fallback (`tr`).
- Slug çeviriye bağlı; `/en/{en-slug}-{id}` çalışır.
- Seeder: mevcut TR demo içeriğin tamamı için EN çeviri (demo kalitesinde, gerçekçi başlık/özet/gövde). Kategoriler ve yazarlar da iki dilli.

## 18.6 SEO (çok dilli)
- Her sayfada `<html lang="{locale}">`.
- `hreflang` alternate linkler: `tr`, `en` ve `x-default` (= tr).
- `og:locale` (tr_TR / en_US) + `og:locale:alternate`.
- `canonical` her dilde self-referans (tr kök, en `/en/...`).
- `sitemap.xml`: hem tr hem en URL'leri (hreflang dahil).

## 18.7 Kabul kriterleri
1. `/` ve `/en` ilgili dilde render olur; dil değiştirici doğru eşdeğere götürür.
2. UI stringleri **ve** DB içeriği (haber/kategori/yazar/son dakika) dile göre değişir.
3. Slug'lar ve `/{slug}-{id}` yapısı her dilde çalışır.
4. `hreflang` (tr, en, x-default) ve `og:locale`/`og:locale:alternate` doğru.
5. TR (prefixsiz) URL'ler korunur; EN yalnız `/en` ile erişilir; bilinmeyen locale 404.
6. `php artisan test` yeşil (mevcut + yeni i18n testleri: locale switch, fallback, slug, hreflang).
7. Kanıt yorumu: komutlar + çıktı + commit hash. Şifre/token repoya/issue'ya yazılmaz; `.env` commit edilmez.

## 18.8 Notlar
- Fallback locale = `tr`.
- Migration'lar mevcut TR verisini `*_translations` tablolarına taşımalı (veri kaybı yok).
- Performans: çeviri tabloları `locale` + FK ile index'lenir; N+1'e dikkat (eager load).