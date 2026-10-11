# BloombergHT.com — BİREBİR CSS/JS SPEC (gerçek kaynaklardan)

Bu spec, https://www.bloomberght.com sitesinin **gerçek kaynak dosyalarından** çıkarılmıştır. Kaynak dosyaları indirildi ve `docs/referans/` içinde. Amacı: coinhit.net'in **stil (CSS) ve davranışını (JS)** referansla birebir hizalamak.

## 1) KAYNAKLAR (gerçek, indirildi)
| Kaynak | Konum | Boyut |
|---|---|---|
| Referans ana CSS (Tailwind+Swiper+custom) | `docs/referans/app-style.css` | 101 KB |
| Referans ana JS (davranış) | `docs/referans/app-script.js` | 40 KB |
| Referans üyelik JS | `docs/referans/member.js` | 3,5 KB |
| İkon font (woff2/woff/ttf/eot/svg) | `docs/referans/icon-font/` | ~140 KB |
| Referans ekran görüntüsü | `docs/referans/anasayfa.png` | 6,7 MB |
| Renk/ölçü doğrulama | `chrome --dump-dom https://www.bloomberght.com/` | — |

Gerçek referans sayfa HTML'i yalnızca şu kaynakları kullanır:
`<link href="/build/common/app-style.css?v=127">`, `<script src="/build/common/app-script.js">`, `/build/common/member.js`, `/adsbht/cbglout.js` (ad-stack).

## 2) ÖNCEKİ SPEC'LERİN EKSİKLERİ (tespit)
1. **bloomberght-icon-font YOK** — HTML'de `bloomberght-icon-font-*` class'ları kullanılıyor ama font dosyası + @font-face yüklenmemiş → ikonlar (menu/search/play/radio/ok/sosyal) görünmüyor/bozuk. **EN KRİTİK EKSİK.**
2. **Referans custom CSS bileşenleri birebir değil** — `app-style.css` içindeki gerçek bileşen stilleri (pageskin, masthead, finance-slider, breaking-news, nav-sub-menu/sub-menu-wrapper, news-wrapper, count-down-timer, headlinesOfTheDay, most-read, scroll-to-top, share-popup vb.) bizim `public/css/app.css`'e birebir taşınmadı; biz kendi versiyonumuzu yazdık.
3. **Referans JS davranışları birebir değil** — `app-script.js` içindeki gerçek init/modüller (Swiper seçenekleri, menu aç/kapa + hover, arama modalı, lazy `IntersectionObserver`, finance-slider, breaking ticker animasyonu, scroll-to-top, share) bizim `public/js/site.js`'te birebir yok.
4. **Renk değişkenleri eksik** — `:root` içindeki `--news-link:#1C64A8`, `--news-link-hover:#134B80` gibi değerler tanımlı değil.
5. Bazı görsel-çıkarımlı spec değerleri (ör. başlık renkleri) gerçek CSS'ten sapıyordu — artık gerçek kaynak esas alınır.

## 3) FONTLAR (birebir, self-host)
- **Poppins** (300/500/700/900/regular) → `public/fonts/poppins/` — MEVCUT ✓ (doğrulandı)
- **Unna** (Regular/Italic/Bold) → `public/fonts/unna/` — MEVCUT ✓
- **bloomberght-icon-font** → `public/fonts/bloomberght-icon-font/` — **EKSİK → YAPILACAK**: `docs/referans/icon-font/*.woff2|woff|ttf|eot|svg` dosyalarını `public/fonts/bloomberght-icon-font/` altına kopyala ve `public/css/app.css`'e **referans `app-style.css`'teki @font-face bloğunu birebir** ekle (tüm formatlar + `bloomberght-icon-font` family).

## 4) İKON FONT CLASS'LARI
Referans `app-style.css` içindeki **tüm `.bloomberght-icon-font-*:before{content:"..."}` bloklarını birebir** taşı (69+ ikon: menu, search, play, radio, arrow-up/down/left/right, chevron-*, close, time, fire, facebook, instagram, quote-*, avatar, stats, home, pause, play-skip-*, full-screen-*, graphic-* …). HTML'de kullanılan class adlarıyla birebir eşleşmeli.

## 5) CUSTOM CSS BİLEŞENLERİ (app-style.css'ten birebir taşı)
Aşağıdaki bileşenlerin CSS bloklarını `docs/referans/app-style.css` içinden bulup, bizim `public/css/app.css` ile **uyumlu/birebir** entegre et (çakışmaları, bizim sınıflar referansla aynı kalsın):
- `pageskin` / `page-skin-left` / `page-skin-right` (22 blok) — sticky reklam sütunları
- `masthead` / `masthead-wrap` / `masthead-top` / `masthead-main-page`
- `finance-slider` (sekmeler + kayan şerit)
- `breaking-news` (SON DAKİKA bandı)
- `nav-sub-menu` / `sub-menu-wrapper` / `sub-menu-wrapper.drop-down` (mega nav + dropdown)
- `news-wrapper` / `article-wrapper` (+ `--news-link` renkleri)
- `count-down-timer`, `headlinesOfTheDay`, `most-read-slider`, `scroll-to-top`, `share-popup`, `inf-line`, `adv-news`, `ets-banner`
- `body[data-*]` / `@media print` (footer/header/aside gizleme) gibi global kurallar
- `:root` ve bileşen değişkenleri (`--news-link`, `--news-link-hover`, `--swiper-*`)

## 6) JS DAVRANIŞLARI (app-script.js'ten birebir)
`docs/referans/app-script.js` içindeki gerçek kod üzerinden aşağıdaki davranışları `public/js/site.js`'e (veya yeni `public/js/app-script.js`) birebir uygula:
- **Swiper init'leri** (`Swiper(` çağrıları): hero/headline slider, most-read, finance-slider, yayın akışı, podcast — seçenekleri (loop, pagination, navigation el'ler, lazy, breakpoints) **referanstaki gibi**.
- **Mega menü**: `menu-button` tıklayınca mega menü aç/kapa; dropdown hover davranışı (`group-hover`) — DOM'daki `ul.drop-down` geçişi.
- **Arama**: `search-widget-trigger` → arama modalı aç; arama input + listele → `/arama/{q}`.
- **Lazy-load**: `IntersectionObserver` ile `data-src`/`b-lazy` görselleri yükle (referans `b-lazy` mekanizması).
- **Ticker/breaking**: SON DAKİKA bandının kaydırma animasyonu (referansın JS'i hangi yöntemi kullanıyorsa).
- **finance-slider sekme değişimi**, **scroll-to-top**, **share** (varsa).
- Analitik/ad-stack (googletag/prebid) gerekli değil (yoksa); `cbglout.js` benzeri reklam motoru bizde zaten `adsbht/cbglout.js` olarak var.

## 7) DOĞRULAMA YÖNTEMİ
1. `chrome --headless=new --no-sandbox --disable-gpu --hide-scrollbars --window-size=1440,3200 --screenshot=/tmp/x.png https://coinhit.net/` + referans `docs/referans/anasayfa.png`.
2. VL karşılaştırma: `POST http://172.18.128.1:20129/v1/chat/completions` `model=evren/qwen3-vl-30b`, `Authorization: Bearer $OMNIROUTE_API_KEY` (anahtar `/home/alper/.config/arastam/omniroute.env`; repoya YAZMA).
3. DOM/CSS diff: referans `app-style.css` ile bizim `public/css/app.css` class kapsamı karşılaştır (`grep -o` count); eksik `bloomberght-icon-font-*`, `pageskin`, `finance-slider`, `breaking-news` vs. kalmamalı.
4. Canlı `/` ve `/en` 200; `php artisan test` yeşil; `npm run build` hatasız (Vite `@vite`/manifest çıktısı).

## 8) KABUL KRİTERLERİ
1. `bloomberght-icon-font` fontu yüklü ve **menü/arama/play/radio/ok/sosyal ikonlar görünür** (screenshot'ta kanıt).
2. `public/css/app.css` referans `app-style.css`'in **tüm custom bileşenlerini** (≥ bölüm 5 listesi) kapsıyor; class sayıları eşleşiyor (grep diff).
3. Hero, finance-slider, yayın akışı, podcast Swiper'ları referanstaki gibi çalışıyor (screenshot + VL).
4. Menü/dropdown, arama modalı, lazy-load, breaking ticker davranışları çalışıyor.
5. Canlı `/`, `/en` 200; testler yeşil; build hatasız; commit + kanıt.

## 9) NOTLAR
- **CANLI DİZİN** (`/home/alper/coinhit`) — `php artisan down` KULLANMA; her adımda canlı 200 doğrula.
- Telif: ikon font ve CSS referansın self-host dosyaları; kullanım bu projenin (klon) görsel birebirliği içindir — API anahtarı/token repoya yazılmaz.
- Önce **ikon font** (bağımsız, hızlı kazanım), sonra custom CSS, sonra JS davranışları.
