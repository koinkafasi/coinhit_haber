# Birebir BloombergHT Klonu — ÇALIŞMA TALİMATLARI

Bu doküman, **ana sayfayı (`/`) https://www.bloomberght.com ile BİREBİR klonlamayı** yöneten resmi çalışma talimatıdır. CH-27 bu talimatlara uyar.

## 1) Kaynaklar
| Kaynak | Yer |
|---|---|
| Referans ekran görüntüsü (DOĞRU) | `docs/referans/anasayfa.png` (kullanıcının tam sayfa görüntüsü) |
| Görselden çıkarılmış spec | `docs/BLOOMBERGHT_ANASAYFA_SPEC.md` |
| Canlı referans DOM | `chrome --dump-dom https://www.bloomberght.com/` |
| Canlı klon | `https://coinhit.net/` · çalışma dizini `/home/alper/coinhit` (CANLI, in-place) |

## 2) Amaç
Ana sayfanın **yapı, düzen, renk, tipografi, spacing, bölüm sırası** ve **etkileşim** bakımından referansa birebir yakın olması. İçerik (haber/veri) demo veridir, korunur; **görünüm** referanstır.

## 3) Zorunlu Çalışma Döngüsü (doğrulamalı — en az 3 TUR, ana sayfa için)
Her turda:
1. **Screenshot:** `/home/alper/.cache/ms-playwright/chromium-1248/chrome-linux64/chrome --headless=new --no-sandbox --disable-gpu --hide-scrollbars --window-size=1440,3200 --screenshot=/tmp/benim.png https://coinhit.net/`
2. **Referans DOM:** `chrome --headless=new --no-sandbox --disable-gpu --virtual-time-budget=9000 --dump-dom https://www.bloomberght.com/ > /tmp/ref.html`
3. **Görsel karşılaştırma (VL):** hem kendi screenshot'ını hem referans görseli (`docs/referans/anasayfa.png`) gönder:
   `POST http://172.18.128.1:20129/v1/chat/completions` · `model: evren/qwen3-vl-30b` · `Authorization: Bearer $OMNIROUTE_API_KEY` (anahtar `/home/alper/.config/arastam/omniroute.env` — **repoya YAZMA**). İki görsel base64 data-URI; "fark + çözüm" iste.
4. **Düzelt:** farkları blade şablonları / `public/css/app.css` üzerinde uygula.
5. **Build:** `npm run build` (Vite) — `public/build` tazelenir.
6. **Doğrula:** `curl -s -o /dev/null -w '%{http_code}' https://coinhit.net/` = 200 ve `https://coinhit.net/en` = 200.
7. **Kanıt:** her turun screenshot + VL fark çıktısını `/tmp/ch27_<tur>.png|.txt`'e kaydet, issue yorumunda paylaş.

Döngüye **"-en az 3 tur"** uygula; son turda VL'nin "belirgin iyileşme/birebir" demesi hedef.

## 4) Doğrulama KURALI (kritik)
- Stil değeri (renk, font, boyut, spacing, class) önce **referans DOM'dan** (`/tmp/ref.html`) teyit edilir. **DOM ≥ görsel ≥ tahmin**.
- Görselde görünmeyen ama DOM'da olan şeyleri de uygula (örn. `!hidden lg:!block`, dropdown).
- Vision'ın şüpheli değerine körü körüne uyma; DOM ile doğrulanmıyorsa atla ve nedenini yorumda belirt.

## 5) Uygulanacak Öncelik Sırası
1. **Header:** logo (beyaz `bht-logo-white`), Canlı Yayın (`/tv`, `bg-white rounded-3xl` + play ikonu), Radyo (`bloomberghtradyo.com`, `border rounded-full` + radio ikonu), arama (`search-widget-trigger`), hamburger (`menu-button`). Class'lar DOM'dan birebir.
2. **Mega menü + dropdown'lar:** `sub-menu-wrapper`/`drop-down` yapısı; kategoriye özgü alt menü listeleri (spec §3).
3. **SON DAKİKA ticker + piyasa ticker:** kırmızı bant; 4-5 sekme; enstrümanlar; **↑/↓ + yeşil/kırmızı** renk kuralı.
4. **Hero slayder:** görsel + kategori + başlık + özet + oklar/dotlar.
5. **Bölümler (spec §7 sırasıyla):** Öne Çıkanlar, Piyasalar, Programlar, Tüm Haberler, Yayın Akışı, Businessweek+Son Sayılar, Foto Haber, Videolar, Podcast Serileri, Kap/Sektör/Piyasa Haberleri, Piyasa Verileri, Kripto, En Çok Okunanlar.
6. **Footer:** 5 sütun + sosyal ikonlar + uygulama rozetleri + yasal not.
7. **Pageskin + inline REKLAM** alanları (konum/boyut).

## 6) KABUL KRİTERLERİ
1. Ana sayfa, referans görsel + DOM'una belirgin şekilde yakın (VL çıktısıyla, ≥3 tur).
2. Header/mega-menü/dropdown'lar çalışıyor; bölüm sırası referansla eş.
3. `npm run build` hatasız; `php artisan test` yeşil; canlı `/` ve `/en` HTTP 200.
4. Kanıt yorumu: her tur screenshot + VL çıktısı + curl + commit hash.

## 7) GÜVENLİK / KURALLAR
- **CANLI DİZİN** — `php artisan down` KESİNLİKLE KULLANMA; her turda canlı 200 doğrula.
- Yeni modül/sayfa/özellik EKLEME; görünümü birebir yap.
- API anahtarı repoya/issue'ya YAZILMAZ (`.env` commit edilmez).
- Küçük güvenli commit'ler; her turda 1 commit idealdir.
- Şüphede kalırsan/görselde belirsizse: referans DOM'a dön; net değilse DUR ve Türkçe soru yaz (blocked).
