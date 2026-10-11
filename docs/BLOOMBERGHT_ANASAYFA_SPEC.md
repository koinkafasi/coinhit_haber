# BloombergHT.com Ana Sayfa — Birebir Klon SPEC (görselden çıkarılmış)

**Kaynak:** `docs/referans/anasayfa.png` (kullanıcının aldığı tam sayfa ekran görüntüsü)
**Çıkarım:** evren/qwen3-vl-30b (görsel model)
**ÖNEMLİ:** Bu dosya görselden çıkarılan bir **kılavuzdur**. Tüm değerleri uygulamadan önce referans DOM ile doğrula:
`chrome --headless=new --no-sandbox --disable-gpu --virtual-time-budget=8000 --dump-dom https://www.bloomberght.com/`
Vision tahminleri (renk/font) gerçek DOM/CSS'ten sapabilir — DOM kazanır.

---

## 1) GENEL
- Container / max genişlik: ~1440px (özellikle 1200-1440px; DOM'daki `container`/`master` class'larını doğrula).
- Arka plan: genel beyaz (#fff); header/footer koyu (#384552 — DOM'dan), bazı modüller siyah/zemin.
- Grid: 12 sütun (`grid-cols-12`), `flex` + max-width.
- Font: referansın kullandığı aile (DOM `font-family`; Arial/Helvetica tahmini — Unna başlıklarını doğrula).
- Renk kuralları: artan=yeşil, azalan=kırmızı; bağlantı/buton vurgu mavi; son dakika bandı kırmızı (#c00000).

## 2) HEADER
- Üst bar: 2 kısım — sol menü, sağ canlı yayın/radyo/arama/hamburger.
- Logo: üst solda; boyut DOM'dan (md:h-16 önerisi); beyaz (logo.svg `bht-logo-white`).
- **Canlı Yayın** butonu: beyaz/mavi vurgu, yuvarlak (rounded-3xl), ikon `bloomberght-icon-font-play` + "Canlı Yayın".
- **Radyo** butonu: `border border-white rounded-full sm:rounded-3xl`, ikon `bloomberght-icon-font-radio` + "Radyo".
- **Arama**: `search-widget-trigger bloomberght-icon-font-search`.
- **Hamburger**: `menu-button bloomberght-icon-font-menu` (referans class'ları; CH-24'te yapıldı — doğrula).
- Header yüksekliği: `h-8 sm:h-12 md:h-16` logoya göre (DOM).

## 3) MEGA MENÜ
- Ana öğeler: `PİYASALAR, BORSA, DÖVİZ, ALTIN, YATIRIM FONLARI, HABERLER, TARIM, ZİRVE, VİDEO, YAYIN AKIŞI` (+ AKPortföy ve Businessweek varsa).
- **Dropdown (alt menü) yapısı** (spec §2 — DOM ile birebir):
  - `sub-menu-wrapper relative ... group h-full`
  - bağlantı `py-2.5 px-1.5 flex items-center gap-1 font-medium group-hover:bg-[#4D596A]`
  - `ul.drop-down absolute top-12 left-0 bg-[#4D596A] z-90 text-sm w-max hidden`
  - `li.hover:bg-[#384552] border-b border-[#384552]` > `a.block.p-4`
- Alt menü listeleri **kategoriye özgü** (BORSA→Hisse/Yükselen/Kap/Dünya/Endeks/Kapanış/Alternatif; DÖVİZ→Dolar/Euro/Sterlin/İsviçre/Parite; ALTIN→Ons/Gram/Çeyrek/Cumhuriyet/Ziynet/Yarım/14 Ayar/22 Ayar/Emtia; HABERLER→Fintech/Enerji/Makro/Halka Arz/Görüş). Görseldeki tekrarlayan "Borsa/Döviz/Altın..." listesini DEĞİL, gerçek DOM'daki kategorik listeyi uygula.

## 4) SON DAKİKA TICKER
- Menü barının hemen altında; yükseklik ~40px; arka plan koyu kırmızı (#c00000), metin beyaz.
- Format: `SON DAKİKA` etiketi + kayan manşetler (~10) + `>` — tamamı `/sondakika`.

## 5) PİYASA TICKER / finance-slider
- Üst bar altında; 4-5 sekme (Borsa / Döviz / Altın-Emtia / Faiz ...; DOM'u doğrula).
- Her sekmede enstrümanlar: örn. BIST 100, DOW, DAX, FTSE, SHANGHAI; USD/TRY, EUR/TRY, EUR/USD, DOLAR ENDEKSİ; ALTIN/ONS, GRAM ALTIN, GÜMÜŞ, BRENT, WTI, BALTIK; FAİZ (TR 2Y), TR 10Y, ABD 10Y vb. (spec §2.4).
- Her satır: `değer | fark | %` + artış/azalış oku; artan yeşil, azalan kırmızı.

## 6) HERO SLAYDER
- Menü/ticker altında; geniş görsel (orana yaklaşık 16:9 / yüksek); içerik: görsel + kategori etiketi + büyük başlık + kısa özet + (varsa) buton.
- Navigasyon: sol/sağ oklar + alt dotlar.

## 7) ANA SAYFA BÖLÜMLERİ (sıralı) — her biri için başlık + kart/sütun düzeni
1. **Öne Çıkanlar** — görselli kartlar (görsel + kategori + başlık + özet), 3-4 sütun.
2. **Piyasalar widget** — büyük tablo/grafik (enstrüman kartları, renkli oklar, mini sparkline).
3. **Programlar** — görsel + başlık + süre, 4 sütun kart.
4. **Tüm Haberler** — 3 sütun görselli kartlar.
5. **Yayın Akışı** — 4 sütun kartlar (görsel + başlık + süre).
6. **Businessweek + Son Sayılar** — 2 sütun.
7. **Foto Haber** — görselli kartlar.
8. **Videolar** — görsel + başlık + süre + oynatma ikonu.
9. **Podcast Serileri** — görselli kartlar.
10. **Piyasa Haberleri / Kap Haberleri / Sektör Haberleri** — 3 sütun görselli kartlar.
11. **Piyasa Verileri widget** — tablolar.
12. **Kripto** — 3 sütun kartlar.
13. **En Çok Okunanlar** — Bugün/Hafta/Ay sekmeli liste.
- Başlık stili: büyük + kalın (DOM'daki boyut; örn. H2 ~30px), sola hizalı, bölüm başına solda/çizgili bar varsa birebir.

## 8) FOOTER
- Koyu zemin; üstte logo; 5 sütun link grid (`grid-cols-2 sm:grid-cols-5`) — link seti spec §2.7'de listelendi (Businessweek/Canlı Yayın/...; Altın Fiyatları/...; Döviz; Canlı Borsa/...; Kişisel Veriler/...).
- Sosyal ikonlar (x/facebook/instagram/youtube) + uygulama rozetleri (AppStore/GooglePlay/AppGallery).
- Ciner grup şeridi + yasal/veri notu (Foreks, 15 dk gecikme, telif).

## 9) REKLAM ALANLARI
- pageskin sol/sağ (sticky, ~160px), masthead, bölümler arası inline REKLAM etiketleri (CH-24/25'te eklendi; konum/boyutu DOM + görselle doğrula).

## UYGULAMA NOTU
- Ana hedef: **`/` (ana sayfa)** birebir. Sonra sayfa şablonları header/footer'ı zaten paylaşıyor.
- Her değeri önce referans DOM'dan doğrula; vision tahminine körü körüne uyma.
- `npm run build` + `php artisan test` yeşil; canlı `/` ve `/en` 200 (CANLI DİZİN — `php artisan down` kullanma).
