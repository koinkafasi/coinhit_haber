# BLOOMBERG HT KLONU — BİREBİR SİTE REPLİKASI (MASTER SPEC)

Hedef: `https://www.bloomberght.com` sitesinin ürettiği her sayfanın, her alanın, her özelliğin ve davranışın birebir kopyasını üretmek.
Kaynak tarama tarihi: 2026-10-10. Bu doküman MASTER SPEC'tir; alt görevler kendi kapsamlarına göre bu dosyadan kesit alır.

---

## 0. KAPSAM & TEKNİK YIĞIN (ÖNERİ)
- Backend: PHP Laravel (11/12) + Blade + PostgreSQL (mevcut altyapıyla uyumlu) VEYA tercihe göre Node/Nuxt — TUTARLI TEK STACK kullan; bu repo tek başına çalışmalı.
- Frontend: Blade/SSR + Vazgeçilmez JS widget'ları (tabs, ticker, swiper, gráfico, hesap makineleri, sonsuz kaydırma, canlı güncelleme). Vanilla JS veya küçük çerçeve; ikonlar tek bir ikon-font (örn. `bloomberght-icon-font-*` sınıfları).
- Veri sağlayıcı: Gerçek Bloomberg HT verileri çekilemez; kendi örnek/statik ve zamanlayıcılı "Foreks benzeri" veri katmanı yaz (aşağıda Bölüm 5). Canlı güncelleme zamanlayıcı (ör. 3-5sn poll) ile simüle edilir.
- Görseller: yerel storage/CDN deseni `geoim.bloomberght.com/l/.../WxH` ve `im.bloomberght.com` benzeri bir görüntü işleme katmanı (thumbnail yeniden boyutlandırma + lazy-load `transparent.gif` → `data-src` deseni).
- Amac: her sayfada otomatik üretilen haberler + market data ile birebir görünüm/davranış.

## 1. TASARIM SİSTEMİ / GÖRSEL KİMLİK
- Marka: sarı/siyah Bloomberg HT kimliği; kırmızı "SON DAKİKA" rozeti.
- Tipografi: Başlıklar için serif display (sitede `.font-unna`, deskop H1 ~44px); gövde sans serif; bold üst-başlık etiketleri; küçük büyük harfli bölüm başlıkları (örn. "PİYASALAR", "DÖVİZ").
- İkon seti: paylaşım (facebook/twitter-x/nsosyal/linkedin/whatsapp/mail/kopyala), chevron (breadcrumb `bloomberght-icon-font-chevron-right`), çeşitli yardımcı ikonlar tek font.
- Görsel boyut sözleşmeleri (birebir):
  - Haber kartı (geniş): 640x360 (srcset 320x180/400x225/640x360/1280x720)
  - Haber kartı (kare): 640x640
  - Video küçük resmi: 360x203 (transparan gif lazy)
  - Yayın akışı program: ~500px genişlik
  - Dergi kapağı: 260x342
  - Businessweek ana: 750x425; yan: 400x400
  - OG görseli: 960x540, twitter summary_large_image
  - Lazy-load deseni: `transparent.gif` src + gerçek URL `data-src`.
- Layout: sticky header + mega nav; içerik alanı geniş (fluid 12-col veya max ~1200-1440px container); makale detayında sağ sidebar ~400px; footer 5 sütunlu. Her sayfada tekrar eden "çerçeve" (aşağıda Bölüm 2).

### 1.1 FONT & TİPOGRAFİ ENVANTERİ (canlı CSS birebir, 2026-10-10)
- Tek stylesheet: `/build/common/app-style.css?v=127` (ve `app-script.js?v=127` + `member.js?v=127`); `window.AssetsVersion = 127`; NO preload/preconnect/Google-Fonts/webfontloader/FontFace API; tek `dns-prefetch` seti (`im/geoim.bloomberght.com`, `im.haberturk.com`, `adsp.haberturk.com`, `mo.ciner.com.tr`, `tr.hit.gemius.pl`, `doubleclick`, `googletagmanager`...).
- **@font-face (same-origin `/fonts/...`, `format("woff")`, `font-display:swap`, unicode-range YOK):**
  - `Poppins` 300/400/500/700/900 → `/fonts/poppins/poppins-v15-latin-ext_latin-{300,regular,500,700,900}.woff`
  - `Unna` 400 (Regular), 700 (Bold), ve İtalik QUIRK: `Unna-Italic.woff` **font-weight:300, font-style:normal** olarak tanımlı (birebir korunmalı) → `/fonts/unna/Unna-{Regular,Bold,Italic}.woff`
- **font-family yığınları (yalnız şu değerler):** `html,:host → Poppins,sans-serif` (line-height:1.5); `.font-sans{Poppins,sans-serif}`; `.font-unna{Unna,serif}`; ikon: `[class^=bloomberght-icon-font-],[class*=" bloomberght-icon-font-"] → font-family:bloomberght-icon-font`.
- **İkon font:** `bloomberght-icon-font`; tek @font-face multi-format (eot#iefix → woff2 → woff → ttf → svg#bloomberght-icon-font), cache-buster `?t=1784548949927`, weight/style/font-display tanımı YOK; her glyph `:before{content}`. Örnek sınıflar: `-arrow-*`, `-avatar`, `-home`, `-stats`, `-chevron-bottom/left/right/top`, `-time`, `-close`, `-exclamation`, `-facebook`, `-fire`, `-full-screen-*`, `-graphic-*`, `-instagram`, `-menu`, `-pause`, `-play(-skip-back/forward)`, `-quote-*`, `-radio`, `-search`, `-share`, `-stop`, `-text-increase`, `-whatsapp`, `-x`, `-youtube`, `-compare`, `-notification`, `-user`, `-settings`, `-twitter-x`, `-linkedin`, `-copy`, `-print`, `-mail`, `-arrow-change`, `-bluesky`, `-web`; **`-nsosyal` glyph değil**: SVG background `https://geoim.bloomberght.com/images/common/icons/nsosyal.svg` (.7em×.7em).
- **Boyut tablosu (birebir):** H1 makale `title font-unna font-bold text-3xl md:text-[44px] md:leading-[3.0rem]` (mobil 36px/2.25rem; ≥768px 44px/48px); H2 spot `.description text-base md:text-lg` (16→18px); H3 (KAP/legal) 1.25rem/1.75rem/700; gövde `p{1rem;line-height:1.5rem}`; sektör/blok kicker H2 `font-unna font-bold text-3xl`; "REKLAM" başlığı `!text-xxs` → 0.625rem; SON DAKİKA `text-xl font-unna font-bold leading-none`; nav `text-sm lg:text-base font-medium` (mega-menu başlıkları `font-unna font-bold text-2xl`); footer `text-sm`, copyright `text-xxs`; finance-slider ticker: ad `text-xs`, son fiyat `.lastPrice font-bold` (renk: düşüş `#BD1B2E`, yatay `#9D9D9D`, yükseliş `#24A278`).
- **letter-spacing:** yalnız `.025em`/`.05em`; **tabular-nums/font-variant-numeric YOK**; 600 (semibold) @font-face'te yok → faux-bold üretir (birebir).
- **Metin boyutu aracı:** `.text-increase` → html `text-increase-base/lg/xl` (H1 lg 36→48px, xl 48→60px).
- ÜMO/lazy placeholder: `https://mo.ciner.com.tr/assets/images/v3/transparent.gif`.
- Tailwind arbitrary sınıflar birebir kritik: `text-[44px]`, `leading-[3.0rem]`, `!text-3xl`, `!text-xxs`, `bg-[#384552]`, `w-[calc(100%-400px)]`; `text-md` derlenmemiş (ölü sınıf).

## 2. GLOBAL SAYFA ÇERÇEVESİ (TÜM SAYFALARDA)
1) **Üst bar / Header (sticky):** Logo (→ `/`), **Canlı Yayın** (`/tv`), **Radyo** (`https://www.bloomberghtradyo.com`), ana mega navigasyon.
2) **Mega navigasyon (dropdown'lar):**
   - PİYASALAR → `/piyasalar`
   - BORSA → `/borsa` (alt: Hisse Senetleri `/borsa/hisseler`, Yükselen/Düşen Hisseler `/borsa/en-cok`, Kap Haberleri `/borsa/hisseler/kap-haberleri`, Dünya Borsaları `/borsa/dunya-borsalari`, Endeksler `/borsa/endeksler`, Borsa Kapanış `/borsa/borsa-kapanis`, Alternatif Piyasalar `/alternatif`)
   - DÖVİZ → `/doviz` (alt: Dolar `/doviz/dolar`, Euro `/doviz/euro`, Sterlin `/doviz/ingiliz-sterlini`, İsviçre Frangı `/doviz/isvicre-frangi`, Pariteler `/doviz/pariteler`)
   - ALTIN → `/altin` (alt: Ons Altın `/altin/altin-ons`, Gram `/altin/gram-altin`, Çeyrek `/altin/ceyrek-altin`, Cumhuriyet `/altin/cumhuriyet-altini`, Ziynet `/altin/ziynet-altini`, Yarım `/altin/yarim-altin`, 14 Ayar Bilezik `/altin/14-ay-bilezik-gr`, 22 Ayar Bilezik `/altin/22-ay-bilezik-gr`, Diğer Emtia `/emtia`)
   - YATIRIM FONLARI → `/yatirim-fonlari`
   - HABERLER → `/haberler` (alt: Fintech `/finansal-teknoloji`, Enerji `/enerji`, Makro `/ekonomik-veriler-ve-gundem`, Halka Arz Takvimi `/halka-arz-takvimi`, Görüş `/gorus`)
   - TARIM → `/tarim` · ZİRVE → `/zirve` · VİDEO → `/video` · YAYIN AKIŞI → `/yayinakisi`
   - BusinessWeek dış bağlantı (`https://www.businessweek.com.tr/`)
3) **SON DAKİKA ticker:** Başlıkta "SON DAKİKA" + otomatik kayan son dakika manşet şeridi (~10 adet, hepsi `/sondakika`'ya gider).
4) **Piyasa ticker widget'ı ("Borsa Döviz Emtia Faiz"):** 4 sekme; her satır `değer | fark | %`:
   - Borsa: BIST 100, DOW, DAX, FTSE 100, SHANGHAI
   - Döviz: USD/TRY, EUR/TRY, GBP/TRY, EUR/USD, DOLAR ENDEKSİ
   - Emtia: ALTIN/ONS, GRAM ALTIN, GÜMÜŞ ONS, BRENT, WTI PETROL, BALTIK KURU YÜK ENDEKSİ
   - Faiz: FAİZ (TR 2 YIL), TR 10 YILLIK TAHVİL, ABD 10 YILLIK TAHVİL, ALMANYA 10 YILLIK TAHVİL, İNGİLTERE 10 YILLIK TAHVİL
5) **REKLAM slotları & ad-stack (birebir, canlı site 2026-10-10):** Her boş/harici zone `REKLAM advertisementN` etiketiyle render edilir; masaüstü + mobil varyant (`data-adv="bht"`, `data-device="DESKTOP|MOBILE"`, `data-zone-id`).
   - **Ad-stack (JS):** `cbgAds` motoru — `AdsHeader/AdsInline/AdsBody/RefreshZone`; `/adsbht/cbglout.js` + `/adsbht/adsFrame.html`; Prebid `pbjs` + googletag DFP; ölçüm: Ogezio (`__OGEZIO_ASYNC_OPTIONS`), Gemius (`tr.hit.gemius.pl`), GTM-`GTM-5XBP88`; native ad (`customdimension.nativeAd`).
   - **Slayt/masthead & zone haritası (şablona göre değişir):**
     - Ana sayfa: pageskin **1251/1252**; masthead **YOK**; inline REKLAM 970×250 (zone havuzu: 1257, 2167×5, 1260, 1265, 1395, 1400, 2015, 2026, 2029, 2129, 22801, 22802, 12652 — 11 adet)
     - Makale detay: pageskin **2126/2127**; masthead **1393/1394**; gövde sonrası inline zone'lar (2 adet)
     - Statik (künye/davos): pageskin **1253/1254**; masthead **1264/1394**
     - Alt kategori (örn. hava-durumu): pageskin **23041/23031**; masthead **23022/2199**
     - /piyasalar: pageskin **2228/2229/2230/2267**
   - **Mobil:** sticky alt bant `widget-sticky-ads` **zone 2280** (`#zone_auto_refresh_sticky`) tüm mobil sayfalarda; `reklammobiletepe` (mobil üst ad) ana sayfa.
   - **Video reklam:** videojs + DFP + `_avp` (preroll/overlay) video ve makale sayfalarında.
   - Sayfa içi yerleşim: aşağıda her modül şemasında "REKLAM" olarak işaretlenir. (Detay için Bölüm 14.6.)
6) **Cookie onay bannerı:** "Çerez Politikası / Aydınlatma Metni" bağlantıları + **Kabul Et** + kapat.
7) **Footer:** 5 bağlantı sütunu:
   - (1) Businessweek, Canlı Yayın, Ekonomi Haberleri, Borsa Haberleri, Dünya Borsaları, Kripto Fiyatları, Künye
   - (2) Altın Fiyatları, Gram Altın Fiyatı, Çeyrek Altın Fiyatı, Tam Altın Fiyatı, Yarım Altın Fiyatı, Ata Altın Fiyatı, Altın ONS Fiyatı
   - (3) Döviz Kurları, Döviz Çevirici, Dolar TL, Euro TL, Sterin Kaç TL (sitedeki birebir yazımı, typo korunur — hedef `/doviz/ingiliz-sterlini`), Riyal Kaç TL, Kanada Doları
   - (4) Canlı Borsa, Gümüş Fiyatları, Brent Petrol Fiyatı, Bilezik Fiyatları, BIST 100 Endeksi, Altın Hesaplama, Yüzde Hesaplama
   - (5) Kişisel Verilerin Korunması Politikası, İzleyici Temsilcisi, Kullanım Koşulları, Aydınlatma Metni, Çerez Politikası, Biyografi, İletişim
   - **Mobil uygulama rozetleri:** AppStore, GooglePlay, AppGallery (linkler).
   - **Sosyal ikonlar:** x/Twitter, Facebook, Instagram, YouTube.
   - **Ciner grubu site şeridi:** Habertürk, Habertürk TV, HT Spor, Bloomberg HT, Businessweek, Show TV, Show Türk, Show Max + Can Medya Grubu.
   - **Yasal/veri notu:** "Sitemizdeki veriler Foreks tarafından sağlanmaktadır..." + gecikme notu (NASDAQ, Dow Jones Industrial Average, SHCOM, FTSE 100, CAC 40, DAX 30, Hang Seng, BOVESPA, VİOP, Tahvil-bono verileri 15 dk; CME, NYMEX VE S&P verileri 10 dk) + "YASAL UYARI © 2026" + KEP `cgorsel@hs03.kep.tr`.
   - **Arama kutusu:** placeholder "BİRKAÇ KELİME YAZARAK SİZE YARDIMCI OLABİLİRİZ!" + **Listele** → `/arama/{q}`.
8) **JSON-LD (her sayfa):** `Organization`, `WebSite` (SearchAction → `/arama/{queryKeyword}`), `BreadcrumbList`; makale: `NewsArticle`.

## 3. URL ŞEMASI (BİREBİR)
- Haber: `/{title-slug}-{numericId}` (örn. `/motorinde-zam-benzinde-indirim-3791025`)
- Sonsuz kaydırma API: `/infinite/news/{prevId}` → bir sonraki (id-1) makale HTML parçası
- Endeks: `/borsa/endeks/<slug>` — gerçek okunur slug örnekleri: `bist-100`, `dow-jones`, `s-p-500`, `kore-kospi`, `nasdaq-100`, `s-p-fut`, `dolar-endeksi`, `bist-banka`, ... (spec'teki eski `xub/vix/bcom/dow` örnekleri GÖZDEN GEÇİR; `xub` gerçekte yok)
- Hisse detay: `/borsa/hisse/<kod>-<kisa-slug>-detay`; hisse liste: `/borsa/hisseler` + `/borsa/hisseler/<harf>`; borsa kapanış: `/borsa/borsa-kapanis` + `/borsa/borsa-kapanis/<harf>`
- KAP haber: `/borsa/hisse/<firma-slug>/kap-haberi/<numericId>`
- Parite: `/doviz/parite/eur-usd`; kripto: `/doviz/bitcoin`, `/doviz/ethereum`, `/doviz/ripple`; kripto liste: `/kripto/liste/<harf>`
- Altın çeşitleri ve emtia: `/altin/...`, `/emtia/...`, `/emtia/bdiy` (Baltık)
- Faiz: `/faiz-bono`, `/tahvil/tr-10-yillik-tahvil`, `/tahvil/faiz`, `/repo/repo-gecelik`, `/eurobond/tr-eurobond-1-yil`
- Fon: `/yatirim-fonlari`, `/yatirim-fonlari/fon-karsilastirma`, `/yatirim-fonlari/fon-portfoy-dagilim`, `/yatirim-fonlari/<fon-slug>`
- Video: `/<seri>/video/<slug>/<id>`; haber klibi `/haber/video/<slug>/<id>`; tarım/zirve video `/tarim/video/...`, `/zirve/video/...`; ayrıca (sitemap_video ile doğrulandı) prefix varyantları: `/roportaj/video/...`, `/ozel/video/...`, `/konuk/video/...`, `/analiz/video/...`; oynatıcı embed: `/video/embed/{id}`; program `/tv/program/<slug>`; seri `/video/<seri>`
- Podcast: `/podcastler/`, `/podcastler/<slug>`
- Yazar/görüş: `/yorum/<yazar-slug>/<id>-<title-slug>` (sitemap_authors'ta ~5859 URL; spec'teki `<id>` kalıbı eksikti); yorum arşivi `/yorum/<yazar-slug>/<numara>` (örn. `/yorum/irfan-donat/2287`)
- Arşiv/etiket: `/grafik-arsivi/<slug>-<id>`, `/haberleri/{tag}`, `/arama/{q}`
- Takvimler: `/halka-arz-takvimi`, `/grafik-arsivi`
- Statik listeneler: `/sondakika`, `/tumhaberler`, `/fotohaber`, `/haberler`, `/haberler/sektor-haberleri`
- Statik/legal sayfalar (GERÇEK slug'lar, canlı site 2026-10-10): `/kunye`, `/bizeulasin` (İletişim formu), `/bizeulasin/izleyicitemsilcisi`, `/cerez-politikasi`, `/aydinlatma-metni`, `/kullanimkosullari`, `/privacy` (Kişisel Verilerin Korunması Politikası), `/biyografi`
- TV alt sayfaları: `/tv/programlar` (Tüm Programlar), `/tv/frekans-listesi` (Frekans Listesi)
- Araç sayfaları: `/doviz-cevirici`, `/altin-cevirici`, `/yuzde-hesaplama`
- Endeks okunur form (footer linki): `/borsa/endeks/bist-100`
- Eski makale kalıbı (legacy, üretimde Disallow): `/haberler/haber/{numericId}-{slug}` → yeni `/slug-id`'e yönlendirme
- Üyelik: `/uyelik` · Arama kökü: `/arama` (Disallow), `/liste/arama`
- Kategori sayfaları (sitemap_categories'ta VAR, spec'te EKSİKti): `/hava-durumu`, `/davos`, `/haberler/turkiye-ekonomisi`, `/haberler/kuresel-piyasalar`, `/haberler/emtia-enerji`
- Döviz takip kalıpları (sitemap_piyasa): `/doviz/sepet-kur`, `/doviz/tl-euro`
- Baltık/emtia varyantları (sitemap_piyasa): `/emtia/bity`, `/emtia/bidy`, `/emtia/petrol`
- RSS: `/rss`, `/rss/tum-haberler.xml` (media:content 1280×720)
- PWA: `/manifest.webmanifest` (icons, scope `/`, display browser; SW yok)
- Mobil web-view (Disallow): `/web-view/yatirim-fonlari/fon-portfoy-dagilim`
- Video embed: `/video/embed/{id}`

## 4. VERİ MODELİ & İÇERİK
- **news:** id, slug (slugsuz-...), numeric `newsId`, kategori label (HABERLER/PİYASALAR/ENERJİ/EKONOMİK VERİLER VE GÜNDEM/FİNANSAL TEKNOLOJİ/TARİM/ZİRVE/KRİPTO/SİYASET/SEKTÖR HABERLERİ/EMTİA/HAVA DURUMU/DAVOS/TÜRKİYE EKONOMİSİ/KÜRESEL PİYASALAR...), başlık, spot (lead, madde işaretli `ul > li`), gövde (p etiketleri), iç görseller (hash adlı dosyalar, alt'sız), inline keyword bağlantıları (`/haberleri/{etiket}`), Flourish embed desteği, yayın tarihi + "Güncelleme" zamanı, editorID, etiketler, görsel (hero + srcset), og görsel (960x540).
   - **Kategori taksonomisi (kök + sitemap_categories'ta doğrulanmış ek yaprak kategoriler):** HABERLER, PİYASALAR, ENERJİ, EKONOMİK VERİLER VE GÜNDEM, FİNANSAL TEKNOLOJİ, TARIM, ZİRVE, KRİPTO, SİYASET, SEKTÖR HABERLERİ, EMTİA, GÖRÜŞ **+ yeni**: Hava Durumu (`/hava-durumu`), Davos (`/davos`), Türkiye Ekonomisi (`/haberler/turkiye-ekonomisi`), Küresel Piyasalar (`/haberler/kuresel-piyasalar`), Emtia&Enerji combo (`/haberler/emtia-enerji`).
- **son dakika:** tek satır kısa/üst geçen başlıklar + zaman (HH:MM) + tarih grubu + kısa link paylaşım.
- **fotohaber/galeri:** çok görselli slayt haberler (kart grid'de kare 640x640).
- **görüş (yazar):** yazar avatarı, isim, `/yorum/...` bağlantısı, köşe başlığı + özet.
- **program/epizot:** program (şov) + bölüm (başlık "{{Şov}} - {{Konuk}} | {{tarih}}", süre HH:MM:SS veya M:SS), thumbnail 360x203, yayın saati.
- **podcast serisi:** kapak, başlık, açıklama (yayın günleri/saatleri), `/podcastler/<slug>` epizotlar.
- **KAP:** firma kod/ad, açıklama başlığı (Özel Durum Açıklaması (Genel), Esas Sözleşme, Kar Payı Dağıtımı, Genel Kurul, Finansal Takvim, Payların Geri Alınması...), tarih-saat, TR/EN ikili alan tablosu (`CONSOLIDATION_METHOD`, `MaterialEventDisclosureGeneralAbstract`, `UpdateNotificationFlag`, `CorrectionNotificationFlag`, `DateOfThePreviousNotification`, `DelayedAnnouncementFlag`, `ExplanationTextBlock` vb.).
- **grafik arşivi:** kategori (Grafik/Piyasa/Makro Ekonomi/Aktüel) + başlık + tek görsel + sayfalama.
- **halka arz takvimi:** Talep Toplama Tarihleri | Şirket (ticker) | Halka Arz Değeri (Lot) | Halka Arz Fiyatı.
- **trendler/#:** etiket + açıklama + ilişkili haber listesi.

## 5. PİYASA VERİ KATMANI
Instrument grupları (her biri ayrı tablo/sayfa):
- **BIST endeksler** (tablo 48 satır; sitemap_piyasa'da 65 endeks detay sayfası — bölgesel bist-adana/msci-euro-bolgesi vb.): XU100/50/30, XBANK, XUSIN, XBLSM, XELKT, XFINK, XGIDA, XGMYO, XHARZ, XHOLD, XILTM, XINSA, XKAGT, XKMYA, XKURY, XMADN, XMANA, XMESY, XSPOR, XTAST, XTCRT, XTEKS, XTM25, XTMTU, XTRZM, XTUMY, XUHIZ, XULAS, XUMAL, XUTEK, XUTUM, XYORT, XYUZO, ... AYLIK% + YILLIK% sütunları.
- **Hisse senetleri:** kod, ad, SON, DÜN, %, YÜKSEK, DÜŞÜK, HACİM LOT, HACİM TL; A-Z filtre; Endeks seçiniz dropdown.
- **Döviz (TRY)** (~14): USD, EUR, GBP, CHF, 100 JPY, SAR, NOK, DKK, AUD, CAD, SEK, RUB, CNY, AED. 4 ondalık.
- **Pariteler** (~40) ve **Gelişen Piyasa Kurları** (USD/MXN, CZK, PLN, HUF, RUB, ZAR).
- **Altın (serbest piyasa)** (17): ALTIN/ONS (TL ve $), GRAM ALTIN, CUMHURİYET ALTINI, TAM ALTIN, ZİYNET ALTINI, ÇEYREK ALTIN, YARIM ALTIN, ATA ALTIN, 14 AYAR BİLEZİK, 22 AYAR BİLEZİK, GREMSE ALTIN, HAS ALTIN, İKİBUÇUK ALTIN, BEŞLİ ALTIN, KÜLÇE EURO, KÜLÇE DOLAR. ALIŞ/SATIŞ/%/SAAT (her satırda güncelleme saati).
- **Emtia:** Kıymetli Metaller (GÜMÜŞ GRAM, GÜMÜŞ ONS, PLATİN, PALADYUM), Tarım Emtia (PAMUK, KAKAO, KAHVE), Sanayi Emtia (BAKIR, ALÜMİNYUM, ÇİNKO, NİKEL, KALAY, KURŞUN) + 3 Aylık vadeli, Enerji (WTI, BRENT).
- **Faiz-Bono:** Tahvil (TR 2/5/10Y — gerçek sitemap: `/tahvil/tr-5-yillik-tahvil` var ama `tr-2` YOK; ALMANYA killesi sitemap'te `almayna` TYPO'suyla geçiyor — birebir korunur), Repo (GECELİK/HAFTALIK/AYLIK), Eurobondlar (TR 1Y→30Y, 11 satır), Uluslararası (ABD/ALMANYA/İNGİLTERE/JAPONYA 2/5/10Y).
- **Alternatif:** VIX, BCOM (Bloomberg Emtia Endeksi).
- **Baltık:** BDIY, BCI14, BPIY, BSI58A, BHSI + sitemap varyantları `/emtia/bity`, `/emtia/bidy`, `/emtia/petrol`.
- **Kripto** (~20): BTC, ETH, USDT, BNB, XRP, USDC, SOL, TRX, DOGE, ADA, LINK, XMR, LTC, AVAX, DOT, ... sütunlar: FİYAT($), %(24s), %(7g), 24s Hacim($), Piyasa Değ($).
- Alan formatları: SON/%, FARK, AYLIK%, YILLIK%, ALIŞ, SATIŞ, ÖNCEKİ KAPIŞ, EN YÜKSEK, EN DÜŞÜK, HACİM, HACİM(LOT), Güncelleme (YYYY/MM/DD HH:MM:SS), SATIŞ /% (altın), TRY 4 ondalık, JPY 2 ondalık.
- **Canlı davranış:** zamanlayıcı ile güncellenen panolar; HARİCİ veri üzerinde 15dk/10dk gecikme etiketleri; her enstrüman satırı kendi detay sayfasına link verir.

## 6. ORTAK WIDGET'LAR / ARAÇLAR
- **Döviz Çevirici:** A↔B kur seçimi (22 para: TRY, USD, EUR, GBP, CHF, JPY, SAR, NOK, DKK, AUD, CAD, SEK, BRL, MXN, CZK, PLN, HUF, RUB, ZAR, KWD, CNY, CNH), Tutar, **HESAPLA**.
- **Altın Hesaplama:** Alış/Satış modu, A↔B enstrüman (18 seçenek), Tutar, **HESAPLA**.
- **Güniçi Verileri grafiği:** **Highcharts** tabanlı interaktif intraday grafik + zaman aralığı seçici (varsayılan **"Tümü"**), her detay sayfasında (borsa endeks/hisse, döviz, parite, altın); mini/sparkline grafikler **Chart.js v4.5.1** (canlıda doğrulandı; "TradingView tarzı" DEĞİL).
- **Yüzde Hesaplama** (footer).
- **Fon Karşılaştır / Fon Portföy Dağılımı** araç sayfaları.
- **En Çok Okunanlar:** Bugün / Bu Hafta / Bu Ay sekmeli sıralı liste.

## 7. SAYFA BAZLI MODÜL ŞEMALARI (ANA SAYFA — yukarıdan aşağıya, SSR HTML ile doğrulandı 2026-10-10)
> Not: Çift "REKLAM" = aynı zonun desktop + mobil kopyası. "Piyasa haberleri / Hızlı Linkler / Grafik" AYRI blok değildir — "PİYASALAR" widget'ının İÇİNDEDİR. "Piyasa Verileri" ana sayfada "5 büyük sekme" DEĞİL; her sekme ayrı bir h2/`<table>` dizisidir.

1. Masthead ad (zone_1264 desktop / zone_1394 mobil) — ana sayfada header ÜSTÜNDE
2. Header + mega-nav + SON DAKİKA ticker + finance-slider (piyasa ticker 4 sekme)
3. **Hero/main-slider:** `grid-cols-12` swiper, başlık kartları (`dataType="headline"`, col-span varyantları)
4. finance-slider mobil şerit (md:hidden, border-t)
5. **ÖNE ÇIKANLAR** (h2): yatay haber başlık şeridi
6. REKLAM
7. **PİYASALAR** (h2) BÜYÜK widget (`market-graphic-tab`): hisse arama kutusu + **4 sekme: Borsa | Döviz | Emtia | Faiz**; her sekmede enstrüman alt-sekmeleri + intraday mini grafikler (BIST100/DOW/DAX/FTSE/SHANGHAI · USD/EUR/GBP-TRY, EUR/USD, DOLAR ENDEKSİ · ALTIN ONS, GRAM ALTIN, GÜMÜŞ ONS, BRENT, WTI, BALTIK · TR/ABD/ALM/İNG 10Y). Widget İÇİNDE: **piyasa haberleri** (list + TÜMÜ `/piyasalar`), **Hızlı Linkler** (etiket cips: Rezerv, Hisse, Waller, Fon ödemeleri, FTSE, Enflasyon + haber + TÜMÜ `/trendler`), **Grafik** (küçük resimler + TÜMÜ `/grafik-arsivi`)
8. **PROGRAMLAR** (h2): program/video listesi (süre rozeti + başlık + tarih) + TÜMÜ `/video`
9. REKLAM x2
10. **ÖNE ÇIKANLAR** (h2): kategori etiketli haber kartları (HABERLER/PİYASALAR) + TÜMÜ
11. **TÜM HABERLER** (h2): başlık listesi + TÜMÜ `/tumhaberler`
12. **YAYIN AKIŞI** (h2): HH:MM günlük TV akışı (`/yayinakisi`)
13. REKLAM x2
14. **Businessweek** kapak widget'ı + **SON SAYILAR** (h2, `businessWeekTurkey`, haftalık sayı tarihleri) → businessweek.com.tr
15. **Foto Haber** (h3): foto-haber kartları + TÜMÜ `/fotohaber`
16. **VİDEOLAR** (h2): video listesi (süre + başlık) + TÜMÜ `/video`
17. REKLAM x2
18. **PODCAST SERİLERİ** (h2): seri kartları (Bu Fikir Tutar, Çağlar Ötesi, Metasal Konular, Sabah Raporu, Sermaye Pusulası…) → `/podcastler/`
19. **KAP HABERLERİ** (h2): KAP duyuru listesi + TÜMÜ `/borsa/hisseler/kap-haberleri`
20. **Piyasa verileri widget** (her sekme kendi h2 + tablo SON|% + "TÜMÜNÜ GÖR"):
    - Döviz · Pariteler · Gelişen Piyasa Kurları (DÖVİZ|SON|%)
    - BIST · En çok Artanlar · En çok Azalanlar
    - Tahvil · Repo · Eurobondlar
    - Altın (ALIŞ|SATIŞ) · Kıymetli Metaller · Tarım Emtia · Alternatif Piyasalar
    - Alternatif
21. REKLAM x2
22. **SEKTÖR HABERLERİ** (h2) → içinde **Siyaset Haberleri** bloğu + TÜMÜ `/haberler/sektor-haberleri`
23. **KRİPTO PARA** (h2): fiyat swiper'ı (Bitcoin, Ethereum, XRP, Solana…) → `/kripto`
24. REKLAM x2
25. **EN ÇOK OKUNANLAR** (h2): sekmeler **Bugün | Bu Hafta | Bu Ay**, sıralı liste
26. Footer (5 sütun + rozetler + marka şeridi + Foreks notu + arama modalı + cookie bar + scroll-to-top + footer billboard zone_1260 + mobil sticky 2280)

## 8. DİĞER SAYFALARIN MODÜL ŞEMALARI (ÖZET)
- **/piyasalar:** hero → piyasa ticker → Döviz tablosu → Pariteler → Gelişen Piyasa Kurları → BIST → Artanlar → Azalanlar → Dünya (Asya/Amerika/Avrupa) → Faiz-Bono minileri → Altın minileri → Piyasa Haberleri → Kap Haberleri → Alternatif → Videolar.
- **/borsa:** BIST tablosu (SON,%,FARK,AYLIK%,YILLIK%) → En Çok Artanlar (HİSSE ADI,SON,%) → En Çok Azalanlar → En Çok İşlem Görenler (HACİM TL) → İlgili Haberler.
- **/borsa/hisseler:** Endeks seçiniz dropdown → A-Z sekmeler → tablo (HİSSE,SON,DÜN,%,YÜKSEK,DÜŞÜK,HACİM LOT,HACİM TL) → sayfalama 1..5/Sonraki → Kenar çubuğu: Kap Haberleri mini.
- **/borsa/en-cok:** Artanlar/Azalanlar/İşlem Görenler sıralamaları.
- **/borsa/hisseler/kap-haberleri:** ters kronolojik KAP akışı (kod/firma adı, başlık, tarih-saat) + Öne Çıkan Videolar + Pariteler sidebar.
- **/borsa/dunya-borsalari:** Amerika (DOW,SP500,NASDAQ), Avrupa (DAX,FTSE100,CAC40), Asya Pasifik (SHANGHAI,NIKKEI,KOSPI,HANG SENG), Vadeliler (SP FUT, NASDAQ100) + CME lisans notu.
- **/borsa/endeksler:** 48 endeks tek tablo (ENDEKSLER,SON,%,AYLIK%,YILLIK%) → `/borsa/endeks/<slug>` (footer "BIST 100 Endeksi" → `/borsa/endeks/bist-100`; sitemap'te 65 endeks detay sayfası).
- **/borsa/borsa-kapanis:** A-Z sekmeler + tarih seçici (gün/ay/yıl) + tablo (Hisse,DÜN,KAPIŞ,YÜKSEK,DÜŞÜK,DEĞİŞİM%,HACİM LOT,HACİM TL) + sayfalama.
- **/alternatif:** Baltık grubu + Alternatif (VIX, BCOM) + Öne Çıkanlar.
- **Hisse detay `/borsa/hisse/<kod>-...-detay`:** firma adı + (KOD), fiyat %, kutu istatistikler (ALIŞ,SATIŞ,FARK,ÖNCEKİ KAPIŞ,EN YÜKSEK,EN DÜŞÜK,HACİM,HACİM(LOT)) + Güncelleme zamanı + **Güniçi Verileri** grafiği (Tümü) + Endeks seç/din A-Z + Endeksler önizleme.
- **KAP detay:** breadcrumb KAP HABERLERİ → firma adı + başlık → meta tarih → paylaş çubuğu (fb,x,nsosyal,linkedin,whatsapp,mail) → TR/EN alan tablosu (açıklamalar) → İlgili KAP bildirimleri sidebar.
- **/doviz:** Döviz Kurları (14) → Pariteler (6+DOLAR ENDEKSİ) → Gelişen Piyasa Kurları → mini kartlar (USD/TRY,EUR/TRY,EUR/USD: SON,%, ALIŞ,SATIŞ,FARK,ÖNCEKİ,KAPIŞ,EN YÜKSEK,EN DÜŞÜK,Güncelleme) → İlgili Haberler → Kap Haberleri → **Döviz Çevirici**.
- **/doviz/dolar ve /doviz/parite/eur-usd ve diğer detay:** hero kart (SON +%, ALIŞ,SATIŞ,FARK,ÖNCEKİ KAPIŞ,EN YÜKSEK,EN DÜŞÜK,Güncelleme) → **Güniçi Verileri** grafik (Tümü) → Döviz Kurları tablosu → Pariteler → İlgili Haberler → Öne Çıkanlar → Döviz Çevirici.
- **/altin:** Altın Fiyatları hero (ALIŞ/SATIŞ/%) → **Serbest Piyasada Altın** (17 satır, ALIŞ,SATIŞ,%,SAAT) → İlgili Haberler → Piyasalar widget → Diğer Emtia → **Altın Hesaplama** → CME/Türkiye lisans notu.
- **/altin/altin-ons, /altin/gram-altin, /altin/ceyrek-altin:** detay hero + Güniçi grafiği + Serbest Piyasada Altın (17) + İlgili Haberler + Öne Çıkanlar + Altın Hesaplama.
- **/emtia:** Kıymetli Metaller → Tarım Emtia → Sanayi Emtia → Sanayi Emtia 3 Aylık → Enerji → İlgili Haberler → Piyasalar → Serbest Piyasada Altın listesi → CME notu.
- **/faiz-bono:** Tahvil (3) → Repo (3) → Eurobondlar (11) → Uluslararası Tahviller (12: ABD/ALM/İNG/JAP 2/5/10Y) → İlgili Haberler.
- **/kripto:** hero kartlar → A-Z filtre çubuğu → Kripto Para tablosu (20 satır: AD,FİYAT($),%(24s),%(7g),24s HACİM($),PYŞ DEĞ($)) → İlgili Haberler → BTC/ETH/XRP hızlı widget paneli.
- **/yatirim-fonlari:** fon haberleri grid → alt-tool nav (Fon Karşılaştır / Fon Sınıflarında Bugün / Fon Portföy Dağılımı) → **Fon Saati** (video epizot listesi) → **Günün Özeti** (KPI: Portföy Değeri, Portföy %, Fon Sayısı, Yatırımcı Sayısı) → **Fon Sınıflarında Bugün** (7 sınıf sekmesi + Yükselen/Düşen toggle + tablo Kod,Ad,Fon Fiyatı TL,Değişim TL %,Değişim USD %) → **Karşılaştırmalı Fon Getirileri** (Kod,Fonlar,Fiyat,Günlük%,Haftalık%,Aylık%,3A%,6A%,1Y%,3Y%,5Y%,Risk Değeri; kod → fon detay; Tümünü Görüntüle).
- **/haberler:** 2 sütunlu masonry haber grid (kare 640x640, HABERLER etiketi, lead öğe).
- **/tumhaberler:** tek sütun haber listesi (kategori etiketi + başlık + 2-3 satır özet) + sayfalama 1 2 3 4 Sonraki + piyasa widget.
- **/sondakika:** ters kronolojik, zaman (HH:MM) + tarih grubu "09 Ekim 2026, Cuma" + büyük harf başlık + her öğede paylaş çubuğu (6 ikon + bb.ht kısa link) + sayfalama.
- **/fotohaber:** kare görsel masonry grid (kategori etiketi).
- **/gorus:** yazar kartları (avatar + isim + başlık + özet + TÜM YAZILAR).
- **/finansal-teknoloji:** haber grid → yazar köşesi (Sefer Yüksel) → TV programı videoları → "Finansal Teknoloji Haberleri" uzun liste.
- **/halka-arz-takvimi:** giriş/legal paragraf → Talep Toplama Tarihleri tablosu (Tarih | Şirket(ticker) | Halka Arz Değeri | Fiyat) → İlgili Haberler → Öne Çıkanlar.
- **/tarim:** haber grid → **Tarım Analiz** (yazar: İrfan Donat; /yorum/irfan-donat/<id>) → videolar → Tarım Haberleri uzun liste.
- **/zirve:** hero lead haber → Zirve haber grid → piyasa widget (4 sekme) → Zirve Videoları (`/zirve/video/...`) → Zirve Haberleri listesi.
- **/grafik-arsivi:** Son Eklenen Grafikler → Kategori sekmeleri (Grafik|Piyasa|Makro Ekonomi|Aktüel) → grafik kart grid → sayfalama → Businessweek grafik arşivi linki.
- **/trendler:** "# Hızlı Linkler" etiket çipleri (Tümü|Rezerv|Hisse|Waller|Fon ödemeleri|FTSE|Enflasyon) → trend haber listesi (etiketi + başlık + özet + thumbnail).
- **/tv:** Canlı Yayın hero (şu an yayında program) → haftanın 7 gün sekmesi → saat sıralı yayın akışı + [Tüm Programlar](/tv/programlar) [Frekans Listesi](/tv/frekans-listesi). Prime kuşak (canlı doğrulandı): 19:00 **60 Dakika**, 20:00 **Aslı Şafak'la İşin Aslı**, 21:00 **Ekonomik Görünüm**, 22:00 **Küresel Piyasalar**, 23:10 **Spor Kulübü** (Pzt) — hafta sonu/Cuma varyantları: Açık Hava Sineması, Akustikhane, Pandora'nın Merakı.
- **/yayinakisi:** 7 gün sekmesi → saat sıralı program listesi (thumb + saat + program adı link + açıklama).
- **/video:** hero "Video" → seri blokları (başlık + TÜMÜ) → bölüm kartları (360x203 + süre rozeti) → kategoriler/filtre sırası → "Listele" lazy-load.
- **/podcastler/:** 12 seri kartı (kapak + başlık + açıklama).
- **Arama `/arama/{q}` ve `/haberleri/{tag}`:** giriş/özet + "toplam N haber" + Güncelleme + haberi listesi (kare thumb + başlık + özetli/özetli - "X dakika/1 gün önce") + İlgili Etiketler + Piyasalar widget.
- **Makale detay:** (aşağıda 9.)

## 9. MAKALE DETAY SAYFASI (BİREBİR)
- URL `/{slug}-{id}`; kategori breadcrumb "Bloomberg HT › Enerji vd."
- **NewsArticle JSON-LD:** headline, alternativeHeadline, description (=spot), articleBody, articleSection; `BreadcrumbList`; `Organization`; `WebSite`. Ek tipler (canlıda doğrulandı): makaleye author→/kunye `Person`, `ImageObject` (960×540), `WebPage`; kategori/statik sayfalarda `og:image` 180×180 (manifest PNG).
- Başlık `<title>` desenleri: `{Başlık} | {Kategori} haberleri` (örn. "| Enerji haberleri", "| Son dakika ekonomi haberleri").
- **H1:** serif display (~44px).
- **Spot (lead):** madde işaretli liste (`ul > li h2.description`).
- **Meta satırı:** `10 Ekim 2026, 10:46` + `Güncelleme : <time>`; yazar adı yok (editorID ile takip).
- **Gövde:** p paragrafları; **inline keyword bağlantıları** (`<a href="/haberleri/zam">`); iç görsel `<img>` hash-adlı; **Flourish embed** (`div.flourish-embed.flourish-chart data-src="visualisation/…"` + `flourish.studio/resources/embed.js`).
- **Paylaş satırı:** `text-increase` yazı boyutu butonu + hover dropdown'da 6 servis (Facebook, X/Twitter, nsosyal, LinkedIn, WhatsApp, Mail) + **Kopyala**; kısa link `bb.ht/{kod}` ([f/t/w] varyantları).
- **Google News promo:** tam genişlik koyu buton "Türkiye'nin ekonomi platformu Bloomberg HT'yi Google listesine ekleyin".
- **Reklam zone'ları:** gövde sonrası bht zone'ları (desktop/mobile), sayfa kenarı pageskin sol/sağ.
- **İlgili Haberler:** Swiper yatay karusel (masaüstü) / mobil düşey liste; 640x360/640x640; ~10 öğe.
- **Sidebar (400px):** "Öne Çıkanlar" widget'ı (text kart + thumb kartlar).
- **Sonsuz kaydırma:** `article data-id`, `data-it='{"index":"1","next":"/infinite/news/{id-1}",...}'` → alt gerektiğinde bir sonraki makale parçasını ekler (önceki/sonraki butonu YOK).
- **No paywall / no yorum / no newsletter** varsayılan.

## 10. ETKİLEŞİMLER & BEHAVIOR
- Tabs (piyasa widget'ları, fon sınıfları, En Çok Okunanlar zaman sekmesi, yayın akışı gün sekmeleri, grafik arşivi kategorileri).
- A-Z filtre çubukları; Endeks seçiniz dropdown'ları.
- Sayfalama: `1 2 3 4 · Sonraki`; veya `TÜMÜNÜ GÖR`/`TÜMÜ`.
- Sonsuz kaydırma: makale detayda `/infinite/news/{id}`; haber listelerinde lazy-load/Load-more ("Listele").
- Canlı veri güncelleme: polling (BIST ~3-5sn), `Güncelleme` zaman damgası, per-satır `SAAT`.
- Lazy görsel: `transparent.gif` + `data-src`; srcset responsive.
- Paylaşım dropdown + kısa link oluşturma (bb.ht benzeri kendi shortener veya takip parametreli URL).
- Yazı boyutu büyütme (`text-increase`).
- Güniçi grafik: zaman aralığı seçici + interaktif canvas.
- Hesaplama araçları (Döviz Çevirici, Altın Hesaplama, Yüzde Hesaplama).
- Mini video oynatıcı (`mini-player-area`/videojs) — tüm sayfalarda köşede sürüklenebilir.
- "Yukarı dön" (scroll-to-top) butonu — tüm sayfalarda.
- RSS bağlantıları (`/rss`, `/rss/tum-haberler.xml`) + PWA manifest (`/manifest.webmanifest`).
- **Etkinlik/Bilet modülü (Zirve/Davos):** bilet satış akışı widget'ları `ticket-sale/participants/summary/billing/payment/checkout/bank-transfer` (app-script.js DOM-tespitli yüklenir) + `[data-ticket-bar]` sepette özet çubuğu.
- **Push bildirim:** Netmera SDK + kendi `/build/common/websocket.js` (canlı veri/push kanalı).

## 11. TEKNİK SEO / ANALİTİK / YASAL (birebir)

### 11.1 Sayfa tipi SEO şablonu (title/description/canonical/OG/JSON-LD)
| Sayfa tipi | `<title>` deseni | OG görseli | JSON-LD | Öncelik kuralları |
|---|---|---|---|---|
| Makale | `{Başlık} | {Kategori} haberleri` (örn. "…| Enerji haberleri", "…| Son dakika ekonomi haberleri") | 960×540 | NewsArticle + Person(author→/kunye) + ImageObject(960×540) + WebPage + BreadcrumbList | canonical self; og:locale tr_TR |
| Statik/legal | `{Sayfa} | Bloomberght` (örn. "Künye | Bloomberght", "Bloomberght Canlı Yayın | Canlı TV İzle") | 180×180 (manifest PNG) | WebPage + Organization | canonical self |
| Kategori/Kanal | `{Kategori}` başlığı + site markası; makale kartları | 180×180 | WebPage + ItemList (makaleler) | canonical self |
| İnstrument liste (döviz/altın/borsa/emdia/kripto/fon) | `{Alan} | Bloomberght` | 180×180 | WebPage + BreadcrumbList | canonical self; sayfa SSR |
| İnstrument detay (coin, parite, endeks) | `{Enstrüman}` + kategori soneki | 960×540 hero / 180×180 destek | WebPage + BreadcrumbList (+ Product gerekirse) | fiyat verisi SSR; JS-only render YOK |
| Video | `{Program/Bölüm} | {Seri}` | 180×180 (+ video:player) | WebPage + ItemList | video sitemap namespace |
| Arama `/arama/{q}` | Arama sonuçları deseni | 180×180 | WebPage | sayfa `noindex,follow` (arama sonuçları) |
| 404 | 404 deseni | 180×180 | — | mutlaka 404 HTTP + özel sayfa |
- **Başlık yazım kuralı:** kategori soneki kategoriye özgüdür (Enerji→"Enerji haberleri", son dakika→"Son dakika ekonomi haberleri", genel→"…haberleri"); ana sayfa marka/portföy deseni korunur.
- **Meta description:** makaleler spot/lede'den; liste sayfaları açıklayıcı tek satırdan; keduğalanmış/boş bırakılmaz.
- **Canonical:** her sayfada self-referential; `og:url` canonical ile aynı; hreflang yok (tek dil TR) — `lang="tr"` + self-referential canonical.

### 11.2 Teknik SEO kuralları
- **robots.txt (birebir Disallow seti):** `/images/dummy`, `/Flash`, `/piyasa/intradaydata`, `/piyasa/refdata`, `/uyelik`, `/dynamics`, `/bizeulasin`(form), `/haberler/haber/…`(legacy haber örnekleri), `/index`, `/arama`, `/liste/arama`, `/yorumlar/liste`, `/etiket`, `/grafik/*`, `/info/gizlilik-politikasi`, `/cerez-politikasi`, `/tcmb/detay/*`, `/haberdetay/siblings/*`, `/haberdetay/preview/*`, `/infinite/*`, `/infinite/kategori/*`, `/settings`, `/finance-slider-data`, `/test/*`, `/breaking-news-widget`, `/most-read-news`, `/most-read-news-vertical`, `/menu-widget`, `/web-view/yatirim-fonlari/fon-portfoy-dagilim`, `/detaytest/`; `Allow: /dynamics/vdo/`; `Sitemap: sitemap.xml` + `sitemap_google_news.xml`.
- **Sitemap:** Bölüm 14.2'deki index yapısı (261 haber + özel parçalar; `sitemap_video` 18'li index + `video:video` namespace + vmcdn mp4; `sitemap_google_news` son ~48s).
- **Pagination:** `1 2 3 4… Sonraki`; canonical sayfanın kendi URL'si; rel=next/prev gerekmiyor (sitede görülmedi).
- **Sonsuz kaydırma & SEO:** makale detayda `/infinite/news/{id-1}` parçaları ayrı URL üretmez indexlenemez (Disallow `/infinite/*`); liste sayfalarında load-more benzeri `data-connection` yine indekslenmez.
- **Legacy yönlendirme:** eski `/haberler/haber/{numericId}-{slug}` → yeni `/{slug}-{numericId}` (301); `/index`, ölü `/tag/…` tipleri 404.
- **Görsel SEO:** makale iç görselleri hash-adlı, alt YOK (birebir); hero srcset; lazy `transparent.gif`+`data-src`; OG 960×540 (makale) / 180×180 (kategori/statik).
- **hreflang:** yok (tek dil); `og:locale tr_TR`.
- **Indexability:** robots Disallow listesi klon URL matrisine taşınır; arama/üyelik/admin noindex; içerik SSR (JS-only değil).

### 11.3 JSON-LD türleri
NewsArticle (headline, alternativeHeadline, description=spot, articleBody, articleSection), Person (author→/kunye), ImageObject (960×540), WebPage, BreadcrumbList, Organization (logo + `founders`/`sameAs` sosyal), WebSite + SearchAction (`/arama/{queryKeyword}`), Product gerekirse; video: `video:video` namespace (sitemap) + VideoObject gerekirse; web stories yok.

### 11.4 Analitik
- `window.customdimension` (~30+ alan, canlı doğrulandı): newsID, newsTitle, editorID, detailType, pageType, textLength, wordCount, commentCount, videoIncluded, datePublished, dateModified, columnistName, hierarchy, mainCategory, mainCategory2, category1..category4, layoutType, photoTotalCount, adBlockUser, loginStatus, memberId, gender, platform, cinerSource, nativeAd, webVersion, ageGroup, disYazarlar, isSpecialNews.
- Ölçüm: Ogezio (`__OGEZIO_ASYNC_OPTIONS`), Gemius (`tr.hit.gemius.pl`), GTM `GTM-5XBP88`; GA4 entegrasyonu.
- Ad ölçümü ile etkileşim: adBlockUser/nativeAd alanları; ad-stack Bölüm 2.5/14.6.

### 11.5 RSS & PWA
- RSS: `/rss` (ana), `/rss/tum-haberler.xml` (media:content 1280×720); kategori/yazar RSS'leri.
- PWA: `/manifest.webmanifest` (icons, scope `/`, display browser); site SW KULLANMIYOR (birebir not).

### 11.6 Yasal sayfalar & veri notları
- Yasal sayfalar (gerçek slug): İletişim `/bizeulasin`, Künye `/kunye`, Biyografi `/biyografi`, İzleyici Temsilcisi `/bizeulasin/izleyicitemsilcisi`, Kullanım Koşulları `/kullanimkosullari`, Aydınlatma Metni `/aydinlatma-metni`, Çerez Politikası `/cerez-politikasi`, Kişisel Verilerin Korunması Politikası `/privacy`.
- Veri kaynağı notları birebir: Foreks veri notu + gecikme süreleri (NASDAQ/Dow/FTSE/CAC/DAX/HangSeng/BOVESPA/VİOP/tahvil-bono 15 dk; CME/NYMEX/S&P 10 dk) + CME lisans + "no AI/ML training use" uyarısı (CME bloğu) + pageskin reklam davranışı.
- KEP `cgorsel@hs03.kep.tr`; "YASAL UYARI © 2026".

## 12. PERFORMANS & RESPONSIVE
- Desktop (~1280) ve mobil (≤767) iki davranış: karusel→düşey liste, sidebar→alt saf, masaüstü/mobil reklam varyantları.
- Görsel optimizasyon (srcset + lazy), CSS/JS küçük, önbellekleme, CDN görüntü işleme.
- Ticker/hari kulakta kayan; IntersectionObserver ile lazy tetikleme.

## 13. TESLİMAT / KABUL KRİTERLERİ
- Her sayfa sıralanacak URL seti birebir çalışır (kopyala/yapıştır test).
- Örnek veri ile tüm modül/widget'lar dolu görünür; boş durumlar da zarif.
- Ana sayfa birebir modül sırası; her "TÜMÜNÜ GÖR"/"TÜMÜ" linki doğru hedef.
- YASAL/data notları ve ikon/font kullanımı birebir.
- Gerçek veri yokluğunda anlamlı sahte/örnek veri; canlı güncelleme simülasyonu.

## 14. EK: CANLI SİTE DENETİMİ VE İÇ VERİ SERVİSLERİ (2026-10-10)

### 14.1 İç veri servisleri / widget endpoint'leri (robots'ta Disallow, klon aynılarını taklit eder)
- `/breaking-news-widget` — SON DAKİKA şeridi verisi
- `/most-read-news`, `/most-read-news-vertical` — En Çok Okunanlar widget verisi
- `/menu-widget` — mega navigasyon verisi
- `/finance-slider-data` — ana sayfa hero/slider verisi
- `/infinite/kategori/p{n}/{slug}` — kategori bazlı sonsuz kaydırma (gerçek kalıp `p{n}/slug`; örn. `p2/hava-durumu`; spec §3'te yalnız `/infinite/news/{id}` vardı)
- `/tcmb/detay/*` — TCMB detay sayfaları (Disallow)
- Legacy: `/haberler/haber/{numericId}-{slug}` (eski makale URL kalıbı) → yeni `/slug-id`'e yönlendirilir

### 14.2 Sitemap yapısı (birebir)
- `sitemap.xml` = `<sitemapindex>`: `sitemap_google_news.xml`, `sitemap_piyasa.xml`, `sitemap_authors.xml`, `sitemap_categories.xml`, `sitemap_video.xml`, `sitemap_news_260.xml` … `sitemap_news_0.xml` (261 parça)
- `sitemap_google_news.xml` = yalnızca son ~48 saatlik haberler; `<urlset>` + Google News namespace; her URL: `loc` + `lastmod` + `image:image` (1920x1080) + `news:news` (publication "BloombergHT"/tr, `news:title`, `news:publication_date`)
- `sitemap_video.xml` = `<sitemapindex>` 18 parça (`sitemap_video_0.xml` … `_17.xml`) + Google `video:video` namespace (thumbnail_loc, title, description, content_loc → `vmcdn.ciner.com.tr` mp4, `player_loc` → `/video/embed/{id}`)
- robots.txt: `Sitemap: https://www.bloomberght.com/sitemap.xml` + `sitemap_google_news.xml`

### 14.3 TV/video program adları — doğrulanmış yazımlar
- Prime kuşak hafta içi: 19:00 **60 Dakika** · 20:00 **Aslı Şafak'la İşin Aslı** · 21:00 **Ekonomik Görünüm** · 22:00 **Küresel Piyasalar** · 23:10 **Spor Kulübü** (Pzt; Cuma `Akustikhane`, hft sonu `Açık Hava Sineması`/`Pandora'nın Merakı`)
- Sabah kuşağı: 06:10 Aslı Şafak'la İşin Aslı (tekrar) · 07:00 **İlk Söz** · 08:15 **Sabah Raporu** · 09:00 **Yatırım Bülteni** · 09:45 **Piyasa Masası** · 10:15 Yatırım Kulübü · 11:00 Grafik Düellosu · 11:15 Fokus
- Öğle/öğleden sonra: 12:00 30 Dakika · 12:30 Girişimcilik Dünyası · 13:00 Fon Saati · 13:30 Seans Ortası · 14:00 Piyasa Hattı · 14:45 İş Dünyası · 15:15 Akıllı Para · 16:00 Finans Merkezi · 17:00 Kapanışa Doğru · 17:45 Seans Notları · 18:20 Günden Kalanlar
- Gece tekrarları: 00:30–05:20 bandı (Aslı Şafak, HT Kulüp, Fon Saati, Varlık Yönetimi, Spor Kulübü, Aile Ekonomisi, Akıllı Tarım, Üst Düzey, Gündem Teknoloji, Oyun Dışı, Tarım Analiz, İş Dünyası, Ece Saruhan'la Oyuna Devam, Akustikhane, Bloomberg Investigates, Gözde Atasoy ile İşin Zirvesinde...)
- Video serileri: `Aslı Şafak'la İşin Aslı`, `Oyun Dışı` (spec'te yanlışlıkla "Oyun Düşü" yazılmıştı — DÜZELTİLDİ), `İş'te Zirve` (seri) **ve** `Gözde Atasoy ile İşin Zirvesinde` / `İşin Zirvesinde` (program) — **iki farklı program**; `Yarının Zekası`, `Tarih Keyfi`, `Dört Kadın Bir Dünya`, `Akustikhane`, `Açık Hava Sineması`, `Pandora'nın Merakı`, `Spor Kulübü`, `Üst Düzey`, `Gündem Teknoloji`, `Fon Saati`, `Finansal Teknoloji`, `Ali İle Aramızda` (sitemap_video'da seri), `Haber`, `Ece Saruhan'la Oyuna Devam`, `Macera Sever`
- Program detay rotası: `/tv/program/{slug}` (örn. `/tv/program/ust-duzey`, `/tv/program/gundem-teknoloji`, `/tv/program/isin-zirvesinde`, tarım programı `<slug>` = `eko-tarim`); video serisi `/video/<seri>` (örn. `/video/oyun-disi`, `/video/iste-zirve`, `/video/fon-saati`)

### 14.4 Künye/iletişim verisi (klon için örnek içerik)
- Şirket: **C Görsel Yayınlar A.Ş.** · Tüzel Kişi Temsilcisi: Ergün Örentel · Reklam: `dijitalreklam@cyh.com.tr` · Editör: `editor@bloomberght.com` · Tel: +90 212 313 6000 · Adres: Abdülhakhamit Cad. No:25 Beyoğlu/İstanbul · KEP: `cgorsel@hs03.kep.tr`
- Yayın: Logo/Çağrı İşareti Bloomberg HT · Lisans Tipi U-TV, K-TV · Yayın Ortamı Uydu + Kablo Ortamı
- İletişim formu (`/bizeulasin`): `Seçiniz (Öneri/İstek/Şikayet/Teşekkür/Yardım)` + Ad Soyad + Eposta + Telefon + Konu + Mesaj + **Mesajı Gönder**

### 14.5 Diğer doğrulamalar
- `/kripto` tablo başlıkları birebir: `KRİPTO PARA | FİYAT($) | %(24s) | %(7g) | 24s Hacim($) | Piyasa Değeri($)` (ekranda kısaltmalı "Pys Değ($)")
- `/borsa/hisseler/kap-haberleri` KAP detay rotası birebir: `/borsa/hisse/{firma-slug}/kap-haberi/{numericId}` (örn. `aksigorta/349081`)
- Grafik arşivi örnek rotası: `/grafik-arsivi/{slug}-{id}` (örn. `yurt-ici-yerlesiklerin-tl-tercihi-guclu-seyrini-koruyor-3790692`)
- Makale URL örneği: `/motorinde-zam-benzinde-indirim-3791025` (`{slug}-{numericId}`) — spec'in örneğiyle birebir
- Üyelik/auth: `/uyelik`; eski makale kalıbı hariç robots Disallow listesi klon URL matrisine taşınır.

### 14.6 Reklam zone yerleşim tablosu (şablona göre, canlı HTML taraması)
| Şablon / sayfa | Pageskin (sol/sağ) | Masthead 970×250 | İnline REKLAM 970×250 | Mobil |
|---|---|---|---|---|
| Ana sayfa | 1251/1252 | yok | 1257, 2167(×5), 1260, 1265, 1395, 1400, 2015, 2026, 2029, 2129, 22801, 22802, 12652 (11 adet) | `reklammobiletepe` + sticky 2280 |
| Makale detay | 2126/2127 | 1393/1394 | gövde sonrası zone (2) | sticky 2280 |
| Statik (künye, davos vb.) | 1253/1254 | 1264/1394 | — | sticky 2280 |
| Alt kategori (hava-durumu vb.) | 23041/23031 | 23022/2199 | — | sticky 2280 |
| /piyasalar | 2228/2229/2230/2267 | — | — | sticky 2280 |
| Video | videojs + DFP + `_avp` (preroll/overlay) | — | — | sticky 2280 |

- Genel etiket kuralı: `REKLAM advertisementN` + `data-adv="bht"` + `data-device="DESKTOP|MOBILE"` + `data-zone-id`; ölçüm Ogezio + Gemius + GTM-5XBP88; native ad `customdimension.nativeAd`.

## 15. SAYFA BAZLI YERLEŞİM ŞABLONLARI (her sayfanın alana özel yapısı/taslağı — SSR ile doğrulandı)

> Ortak çerçeve (tüm sayfalar): masthead ad → header → mega-nav → SON DAKİKA ticker → finance-slider → `<main>` (pageskin sol/sağ sticky ad varsa) → içerik → footer (5 sütun + rozetler + marka şeridi + arama modalı + cookie bar + scroll-to-top + footer billboard zone_1260 + mobil sticky 2280). Çift ad = desktop+mobil kopya.

### 15.1 Ana sayfa `/`
Bölüm 7'deki punch list: Masthead → header → hero main-slider → mobil finance strip → ÖNE ÇIKANLAR → REKLAM → PİYASALAR büyük widget (4 sekme + içinde piyasa haberleri/Hızlı Linkler/Grafik) → PROGRAMLAR → REKLAMx2 → ÖNE ÇIKANLAR → TÜM HABERLER → YAYIN AKIŞI → REKLAMx2 → Businessweek+SON SAYILAR → Foto Haber → VİDEOLAR → REKLAMx2 → PODCAST SERİLERİ → KAP HABERLERİ → Piyasa verileri widget (her sekme h2+tablo) → REKLAMx2 → SEKTÖR HABERLERİ (+Siyaset) → KRİPTO PARA → REKLAMx2 → EN ÇOK OKUNANLAR (Bugün/Hafta/Ay) → Footer.

### 15.2 `/piyasalar` (tek sütun; aside YOK)
1. Breadcrumb + h1 Piyasalar → 2. Foto-haber şeridi (kart swiper) → 3. **Büyük market-graphic-tab (4 sekme: Borsa|Döviz|Emtia|Faiz + enstrüman alt-sekmeleri + intraday grafikler)** → 4. Piyasa haberleri listesi (görselli) → 5. Üst üste tam genişlik tablolar (her biri kendi h2, sticky th): Döviz `DÖVİZ|SON|%` · Pariteler · Gelişen Piyasa Kurları · BIST `BİST|SON|%` · En çok Artanlar · En çok Azalanlar · Asya Pasifik · Amerika Borsaları · Avrupa · Tahvil · Repo · Eurobondlar · Altın (ALIŞ|SATIŞ) · Kıymetli Metaller · Tarım Emtia · Kap Haberleri · Alternatif Piyasalar · Alternatif → 6. VİDEOLAR (h2 swiper) → Footer.

### 15.3 `/borsa` (2 kolon: ana + 400px aside)
1. h1 Borsa → alt-quick-links: `BIST | Hisse Senetleri | Yükselen/Düşen Hisseler | KAP Haberleri | Dünya Borsaları | Endeksler | Borsa Kapanış | Alternatif Piyasalar` → 2. BIST tablosu `BIST|SON|%|FARK|AYLIK(%)|YILLIK(%)` → 3. En Çok Artanlar / Azalanlar / İşlem Görenler → 4. İlgili Haberler. Aside: REKLAM advertisement1 (çift) + **Öne Çıkan Haberler**.

### 15.4 `/doviz` (2 kolon)
Ana: h1 Döviz → alt-linkler `Dolar|Euro|Sterlin|Frank|Pariteler` → **Döviz Kurları** `DÖVİZ|SON|%` → Pariteler → Gelişen Piyasa Kurları → İlgili Haberler. Aside: REKLAM + **USD/TRY, EUR/TRY, EUR/USD mini kartlar** (ALIŞ/SATIŞ + 3 grafik sekmesi tab-0..2) + **Kap Haberleri** (list + TÜMÜ) + **Döviz Çevirici** (Mevcut/Çevrilecek döviz + Tutar + HESAPLA).

### 15.5 `/altin` (2 kolon)
Ana: h1 Altın → alt-linkler `Ons Altın|Gram Altın|Çeyrek Altın|Cumhuriyet Altını|Ziynet Altını|14 Ayar Bilezik|22 Ayar Bilezik|Diğer Emtia` → **Altın Fiyatları** → **Serbest Piyasada Altın** (ALIŞ|SATIŞ) → İlgili Haberler. Aside: REKLAM + market-graphic-tab (4 sekme). Alt: **Diğer Emtia** (tablo) + **Altın Hesaplama** (araç).

### 15.6 `/kripto` (2 kolon)
Ana: h1 Kripto → haber slider (ana foto kart) → **Kripto Para** fiyat listesi → Kripto swiper → İlgili Haberler. Aside: REKLAM + **BITCOIN/ETHEREUM/RIPPLE mini kartlar** (grafik tab-bitcoin|ethereum|ripple).

### 15.7 `/faiz-bono` (2 kolon)
Ana: h1 Faiz Bono → **Tahvil** `TAHVİL|SON|%` (TR 2/5 YILLIK…) → Repo → Eurobondlar → **Uluslararası Tahviller** → İlgili Haberler. Aside: REKLAM + market-graphic-tab (4 sekme + tab-graph-0..5).

### 15.8 `/yatirim-fonlari` (2 kolon)
Anim: h1 → **Fon Saati** (h2 video/hero) → **Fon Sınıflarında Bugün** (h2; sekme hapları: Karma ve Değişken · Borçlanma Araçları · Fon Sepeti · Hisse Senedi · Katılım · Kıymetli Madenler · Para Piyasası + Yükselen/Düşen toggle; tablo `Kod|Ad|Fon Fiyatı (TL)|Değişim (TL) %|Değişim (USD) %`) → **Karşılaştırmalı Fon Getirileri** (h2 + grafik sekmeleri tab-graph-0/1). Aside: REKLAM çifti. (Fon Saati video + Günün Özeti KPI bloğu da bu şablonda.)

### 15.9 `/tv` (tek sütun)
**Canlı Yayın** (h2; canlı player liveBroadcast + kırmızı nokta + şu anki program) → **Yayın Akışı** (h1; 7 gün sekmesi, bugün vurgulu) → gün başına dikey liste `HH:MM | program | açıklama` (yuvarlak görsel) → her gün sonunda **[Tüm Programlar](/tv/programlar)** + **[Frekans Listesi](/tv/frekans-listesi)** butonları → Footer. Aside/reklam metni yok.

### 15.10 `/haberler` (tek sütun, masonry)
Dekoratif başlık bandı + **Haberler** (h2) → **3 sütunlu kart grid** (`grid-cols-1 md:grid-cols-3`: type1 metin / type2 görselli / type4, kategori etiketi ENERJİ/HABERLER vb.) → sonsuz kaydırma (data-connection load-more + iskelet) → Footer.

### 15.11 `/sondakika` (tek sütun)
h1 Son Dakika → satır listesi `HH:MM | gün tarih (09 Ekim 2026, Cuma) | Başlık` + **WhatsApp paylaş (wa.me)**; ~10 kayıt + gerisi data-connection (JS) → Footer.

### 15.12 `/tumhaberler` (2 kolon)
Ana `w-[calc(100%-400px)]`: h1 → kartlar (dönüşümlü type1/2/4; kategori + başlık + özet) → sayfalama `1|2|3|4…|Sonraki`. Aside `400px`: market-graphic-tab (4 sekme, data-connection).

### 15.13 `/trendler` (2 kolon)
h1 Hızlı Linkler → ana: trend sekmeleri `Tümü|Rezerv|Hisse|Waller|Fon ödemeleri|FTSE|Enflasyon` (her sekme etiketin haber kartları) → aside: market-graphic-tab. Not: `/tag/bitcoin` 404 (etikete `/trendler`/kartlar üzerinden erişilir; `/haberleri/{tag}` modeli ayrı).

### 15.14 `/grafik-arsivi` (tek sütun)
h1 Grafik Arşivi → **Son Eklenen Grafikler** (h2, küçük resim kart grid) → Footer. Kart → `/grafik-arsivi/{slug}-{id}`.

### 15.15 Makale `/{slug}-{id}` (2 kolon + sonsuz kaydırma)
data-it (next `/infinite/news/{id-1}`) → breadcrumb (font-bold) → **h1** (`title font-unna font-bold text-3xl md:text-[44px]`) → **spot** (h2.description) → Google CTA ("Google listesine ekleyin") → ana görsel → meta **`Güncelleme : {tarih, saat}`** → gövde p paragrafları → **paylaş ikon çubuğu** → **REKLAM advertisement1** (çift 1258/1393, gövde arası) → **İlgili Haberler** (h2 swiper). Aside (400px): **Öne Çıkanlar** (h2 başlık listesi). Alt: sonsuz kaydırma (data-it), footer.

### 15.16 Alt kategori anasayfa (örn. `/hava-durumu`) (tek sütun)
Başlık bandı (h2) → **3 sütunlu kart grid** (type5 büyük kare görsel + type2/1/4; kategori etiketleri) → sonsuz kaydırma `data-it next=/infinite/kategori/p2/{slug}` + iskelet animasyonu → Footer. Not: hava durumu widget'ı YOK — sadece "Hava Durumu" haber kategorisidir.

### 15.17 `/davos` (2 kolon)
**Davos** (h2) → öne çıkan kart slider → **Davos Haberleri** (h2 kartlar, etiket DAVOS) → aside 400px (ad 1253/1254/1260).

## 16. TEKNOLOJİ YIĞINI — CANLI SİTTEN BİREBİR TESPİT (2026-10-10)
> Bu bölüm doğrudan produksiyon HTTP/HTML/JS/CSS den analizle çıkarılmıştır. Klon birebir görünüm + davranış için bu yığına uymalıdır; §0'daki "Laravel+Blade" önerisi gerçek siteyle birebir örtüşmektedir.

- **Backend:** PHP **Laravel + Blade (SSR)**. Kanıt: `<meta name="csrf-token">`, Blade SSR HTML (`lang="tr"`), JSON config endpoint'i `/settings`, temiz rota desenleri. (Kesin Laravel/sürüm sayfa başlığı CDN tarafından sıyrıldığı için HTTP'den doğrulanamadı; csrf-token + Blade deseni yeterli.)
- **Frontend build:** **Vite** (ESM preload helper — `Event("vite:preloadError")`, hashed chunk'lar `_commonjsHelpers.CqkleIqs.js`, `autoplay.D--uTPhe.js`); modüler **on-demand yükleyici**: `app-script.js?v=127` DOM'da eleman var mı diye bakar, varsa `<script type="module" src="/build/common/<widget>.js?v={N}">` enjekte eder (BHTJS.settings → `/settings` JSON).
- **CSS/UI:** **Tailwind CSS** (preflight `html,:host` reset, utility + arbitrary sınıflar `text-[44px]`, `!text-xxs`, `z-400`, `w-[calc(100%-400px)]`); tek `app-style.css?v=127`; `window.AssetsVersion=127`.
- **Fontlar:** Poppins (300/400/500/700/900) + Unna (400/700 + Italic quirk) + `bloomberght-icon-font` — §1.1'deki birebir tanım.
- **JS kütüphaneleri (bundle içinden):** **Swiper** (ESM + Autoplay; finance-slider/hero/en çok okunan), **Chart.js v4.5.1** (`chartjs.js` — mini/sparkline), **Highcharts** (`highchart.js`, `.highchart-container` — büyük "Güniçi" grafikler), **blazy v1.8.2** (beLazy; `transparent.gif`+`data-src`+`.b-lazy`), countdown-plugin, **Netmera** push SDK + özel `/build/common/websocket.js?v=72`. jQuery/Vue/React/Alpine/GSAP/Livewire/Inertia **YOK**
- **Widget modül envanteri (`app-script.js` DOM-tespitli yüklenen):** live-stream, highchart, player-init, radio-player, native-share, swiper, tab-menu, select-tab-menu, date-selection, most-read-resizer, discount-calc, kdv-calc, currency-converter, gold-converter, percent-calc, acc-menu, live-tv-player-box, widget-check(suggested-news), sub-menu, search, image-comparison, copy-link, image-zoom, opinion-slider-tab, share-modal, search-modal, streaming-tab, text-increase, data-tab-section, cookie-policy, scroll-to-top, audio-player, auto-complete, countdown, contact-us, infinite-page, load-more, ticket-sale, ticket-participants, ticket-summary, ticket-billing, ticket-payment, ticket-checkout, ticket-bank-transfer.
- **Veri/config endpoint'leri (JSON):** `/settings` (extras.version.assets, date; body: navigationBar, footerLinks, stores, social, brandList, copyright, financeSlider, deeplinkSettings, infiniteHomePage, infiniteNewsByCategory...), `/finance-slider-data`, `/breaking-news-widget`, `/most-read-news`, `/most-read-news-vertical`, `/menu-widget`, `/infinite/news/{id}`, `/infinite/kategori/p{n}/{slug}` (Bölüm 14.1).
- **Reklam:** özel `cbgAds` (`.ads-zone:not(.not-refreshed)`, `zone_{id}`, `RefreshZone`/`RefreshZoneEx`, `data-zone-id`, `#ciner-pageskin-left/right`, sticky `zone_auto_refresh_sticky` 30sn refresh, `data-device="DESKTOP|MOBILE"`, `data-adv="bht"`), `/adsbht/cbglout.js` + `adsFrame.html`; DFP/Prebid; GTM-5XBP88 (Bölüm 2.5/14.6).
- **3rd-party/ölçüm:** GTM (`googletagmanager`, `dataLayer`), GA4 (GTM üzeri), Ogezio, Gemius (`tr.hit.gemius.pl`), Netmera (push), lazy görsel CDN `mo.ciner.com.tr`, görsel CDN `im/geoim.bloomberght.com`.
- **Altyapı/CDN:** Önde **MerlinCDN** (`Server: MerlinCDN`, `via: HTTP/1.1 Merlin CDN`, `x-cache-status: HIT`, `x-l1-cache`, `age`), arkada **Kubernetes** (`x-sub-sys: sub-64fc98db48-pszf4` = pod adlandırma; `x-main-sys/midx`, `cbglayer`); güvenlik başlıkları: HSTS(preload), `content-security-policy: upgrade-insecure-requests; frame-ancestors 'self'`, `x-frame-options: SAMEORIGIN`, `x-content-type-options: nosniff`, `Cache-Control: max-age=30`.
- **Klon birebir eşleşmesi:** Laravel 12 + Blade SSR + Vite + Tailwind + Swiper + Chart.js v4.5.1 + Highcharts + blazy; `/settings` eşdeğeri JSON config; canlı veri için own WebSocket; ad-stack `cbgAds` eşdeğeri; pageskin/sticky ad davranışı Bölüm 2.5/14.6. (Üyelik yok denecek kadar az — `/uyelik` mevcut ama zayıf.)

## 17. BİLEŞEN & AREA TASARIM KURALLARI — CSS ENVANTERİ (canlı `app-style.css?v=127` tanımlı + markup, 2026-10-10)

> **Kritik bulgu:** Sitenin tasarımı YOĞUN Tailwind utility'siyle markup'a gömülüdür — `.news-card*`, `.tab-menu*`, `.pagination`, `.btn*`, `.share-*`, `.aside`, `.section-title` gibi sınıflar için **derlenmiş CSS'te özel kural YOKTUR**. Özel CSS yalnız: KAP/widget tabloları, makale tipografisi + `.text-increase`, swiper pagination `type1..4`, pageskin/sticky ad, AK Portföy logosu, `@media print`, font/ikon @font-face, finance autocomplete, lazyload blur. Klon bu yüzden **markup'taki gerçek sınıf desenini değil, sınıf→görsel davranış ilişkisini birebir** kurmalı.

### 17.1 Renk paleti (arbitrary, birebir RGB)
- Üst bar / mega nav zemin: `bg-[#384552]` = rgb(56,69,82)
- Dropdown hover zemin: `bg-[#4D596A]` = rgb(77,89,106)
- SON DAKİKA şeridi: `bg-[#bd1b2d]` = rgb(189,27,45); canlı nokta `bg-[#a00207]`
- Footer çizgileri: `border-[#707070]` = rgb(112,112,112)
- Gövde gri zeminler: `bg-[#f5f5f5]` / `bg-gray-100/50/300`
- "En Çok Okunanlar" (special): `text-special-dark-maroon` = rgb(97,8,42)
- Fiyat renkleri: yükseliş `text-green-700` / `#24A278`(ticker), düşüş `text-red-700` / `#BD1B2E`, yatay `#9D9D9D`; JS `lastPrice/setTextColor` ile atanır (`security-{slug}` satırına göre)
- Standart: `bg-black`, `bg-white`, `bg-gray-800` (Google News barı), `text-gray-400/500/700/900`, `bg-red-400` (animate-ping)

### 17.2 Özel derlenmiş CSS (birebir korunacaklar)
```css
/* KAP tabloları */ .kap-content .widget-table-kapnews{font-size:.875rem;line-height:1.25rem}
  .kap-content .widget-table-kapnews td{border-width:1px;border-color:rgb(156 163 175);padding:.5rem;font-size:.875rem}
  .kap-content .widget-table-kapnews h3{margin-top:1.5rem;font-size:1.25rem;font-weight:700}
/* İçerik tabloları */ .widget-table{display:block;max-width:100%;overflow-x:auto;margin:1rem 0 1.25rem}
  .widget-table thead tr{border-bottom-width:1px;background:#000;font-size:.875rem}
  .widget-table th{position:sticky;left:0;min-width:11rem;padding:1rem;color:#fff;text-align:left}
  .widget-table td{background:#fff;padding:.75rem}
/* Makale tipografisi */ article .article-wrapper p{font-size:1rem;line-height:1.5rem}
  article .article-wrapper p:not(:last-child){margin-bottom:1rem}
  article .article-wrapper{--news-link:#1C64A8;--news-link-hover:#134B80}
  article .article-wrapper.video-news-content{--news-link:#6FB6EA;--news-link-hover:#A5D3F2}
  article .article-wrapper a[href^="/haberleri/"]{color:var(--news-link);text-decoration:underline;text-underline-offset:2px}
/* text-increase */ base: p 1rem/1.5rem, h1 1.875rem/2.25rem; @md h1 44px/3rem; lg h1→3rem/1, xl h1→3.75rem/1
/* Swiper pagination type1..4 */ bullet 10px #fff + 1px #707070; active 16px #000; dotted ayraç #707070
/* Pageskin/sticky */ .top-pageskin-center{position:sticky;top:15px;z-index:48;display:flex;center}
  .masthead_fixed_scroll>div{position:fixed;inset:0 0 auto;z-index:999999;background:#00000080}
  [data-device=MOBILE].widget-sticky-ads[data-device=DESKTOP]:not(.pageskin){display:none!important}
/* AK Portföy logo (sub-menu 5. çocuk + menu-list 3) */ before:url(im.bloomberght.com/assets/bht/ak_portfoy_logo.png)
/* Lazyload blur */ img[data-src]:not(.swiper-lazy),.b-lazy{--tw-blur:blur(4px);filter:var(--tw-blur)} üzeri opacity 1
/* @media print: footer,header,aside,headlinesOfTheDay,…,scroll-to-top{display:none!important} */
```

### 17.3 Bileşen → markup sınıf reçetesi (birebir taslak)
- **Bölüm başlığı bandı (head-type1):** `div.relative before:absolute before:top-0 before:bottom-0 before:m-auto before:w-[230px] before:h-2.5 before:bg-black border-b border-black mb-4` + `h2.font-unna.font-bold.text-3xl` (title text'te siyah üst çubuk deseni)
- **Sayfa h1 (head-type2):** `div.py-4 > h1.font-unna.font-bold.text-2xl.md:text-4xl` (makalede `text-3xl md:text-[44px] md:leading-[3.0rem]`)
- **Haber kartları:** `div[data-type=news-card-type1|2|4|5] data-news-id class="border-b mb-2 pb-2"` → `a>figure` + `figcaption`; t1 düz, t2 `figure.flex.gap-2` (img sol `w-4/12 aspect-square bg-gray-50 max-w-40`), t4 `justify-between` (img sağ), t5 `a.block` featured; kategori `div.category.text-xs`; başlık `div.font-unna.font-bold.text-base.2xl:text-xl.line-clamp-2/3`; özet `div.text-base.line-clamp-3`
- **Hero main-slider:** `[data-widget-type=headline] > div.swiper.slider-custom`; slide `swiper-slide[data-hash]`; `div.aspect-video.bg-gray-50`; oklar `.main-slider` siyah `bg-black/40 text-white w-9 h-9 border-y/r white`; pagination `.type4` (üstte nokta çizgisi)
- **SON DAKİKA:** `bg-[#bd1b2d] h-16 md:h-14`; etiket `text-xl font-unna font-bold`; canlı nokta `span.animate-ping bg-red-400` + `span.bg-[#a00207] h-4 w-4 rounded-full`; swiper ok butonları
- **Finance ticker:** `bg-white border-b min-h-20 max-h-20 overflow-hidden`; arama fade `finance-slider-search absolute right-0 bg-gradient-to-l from-white pl-10 from-80% to-transparent h-20 max-w-[173px]`; satır: ad `text-xs`, fiyat `.lastPrice font-bold setColor`, değişim `.percentChange text-xs` + `setIcon setTextColor bloomberght-icon-font-icon-graphic-up/down/fixed`; mini grafik `canvas.graphChart-wrapper` 96px (Chart.js)
- **Büyük market widget (PİYASALAR):** `[data-widget-type=market-graphic-tab]` + 4 tab `data-tab`; enstrüman alt-sekmeleri; intraday grafikler (Highcharts `.highchart-container`/Chart.js)
- **Piyasa tabloları:** `table data-type=table-type1 class="w-full text-center text-sm bg-white"`; `thead tr.border-b`; ilk `th.sticky.left-0.p-4.text-left.bg-white`; `tbody[data-connection=true]`; `tr.border-b.text-sm.security-{slug}`; `td.py-4.sticky.left-0` (ilk kolon) / `td.py-4.px-4`; değer `.lastPrice.text-green-700`/`text-red-700` + `.setIcon`
- **Sekmeler/haplar:** aktif `span.tab-button…rounded-3xl.bg-gray-200`, pasif `bg-white` (fon sayfası `div.tab-button.w-1/2.border.py-2`); En Çok Okunanlar `tab-button.font-unna.font-bold.px-4.py-2.mr-2.rounded-3xl`; içerik `div.tab-content[data-id]` + `.hidden`
- **Mega nav:** `header.bg-[#384552]` → `div.nav-sub-menu.container.h-full.flex.items-center.justify-between.z-90` → `sub-menu-wrapper.relative.group`; link `py-2.5 px-1.5 flex items-center gap-1 font-medium text-sm lg:text-base text-white`; hover `group-hover:bg-[#4D596A]`; dropdown `ul.drop-down.absolute.top-12.left-0.bg-[#4D596A].w-max.hidden` + `li.hover:bg-[#384552] p-4 border-b border-[#384552]`; mobil arama/menu ikonları
- **Makale:** breadcrumb `div.block.text-xs.text-gray-500` + `i.bloomberght-icon-font-chevron-right text-xxxs text-black` ayracı; h1 Block + spot `ul>li.relative before:w-2 before:h-2 before:bg-black pl-5 mb-4 > h2.description`; Google News bar `bg-gray-800 hover:bg-white hover:border-gray-300 transition-colors min-h-[54px]`; gövde `div.article-wrapper.mb-4.mt-5`; paylaş `ul.absolute.right-0.bg-white.shadow.flex.gap-6.p-4.border.rounded-sm.hidden.group-hover:flex` + ikonlar (`-twitter-x py-2`, `-linkedin py-2`, `-whatsapp`, `-mail`, `-nsosyal`, `-copy`); İlgili Haberler başlığı `h2.font-unna.font-bold.!text-3xl` + swiper `swiper-slide.w-72` (img `swiper-lazy aspect-video`)
- **Sidebar:** `aside.w-full.md:w-[400px].relative` / `md:pr-4`; `[data-widget-type=headlinesOfTheDay]` başlığı "Günün Öne Çıkanlar"; market widget aside; REKLAM çifti
- **İçerik genişliği:** içerik `w-full.md:w-[calc(100%-400px)].md:pr-7` karşı `aside 400px`; sayfalama `div.flex.w-full.gap-2.text-sm` + `button.px-3.py-2.border.bg-gray-200`(aktif)/`bg-gray-50` + Sonraki ikonlu
- **Cookie bar:** `[data-type=cookiePolicy] fixed bottom-0 z-500` + `div.bg-black.text-white.text-xs.p-4.md:p-8.md:max-w-96` (ör. `container w-full fixed inset-x-0 md:inset-x-4 bottom-0 m-auto z-500 hidden`)
- **Scroll-to-top:** `button.scroll-to-top fixed bottom-24.2xl:bottom-16.right-4.md:right-10.z-500.w-12.h-12.bg-black.text-white.shadow.opacity-0.transition.duration-500.translate-y-32`
- **Reklam kutusu:** `div.wrapper.adv-news.w-full.min-h-80.mb-5.py-4.flex.justify-center.bg-gray-50` → `div.head>h2.\!text-xxs` "REKLAM" + `div.body.w-[970px].h-[250px] > div.ads-zone.lazy-init[data-zone-id]` (iframe min-height:250px); mobil varyant ayrı `[data-device]`
- **Footer:** `#footer-menu grid grid-cols-2 sm:grid-cols-5 gap-4 text-sm pt-8 pb-4 border-b-2 border-[#707070]` + `li.mb-4`; paydaş alt satırlar

### 17.4 Utility deseni & responsive
- Ön ekler: `sm: md: lg: xl: 2xl: 3xl:` (ör. `top-32 3xl:top-44`, `min-w-20 2xl:min-w-24`, `!hidden lg:!block`); `!` important (Tailwind `!text-xxs`, `!text-3xl`, `!m-0`)
- Pseudos: `before:/after:` (bölüm çubuğu, başlık noktaları), `group`+`group-hover:` (dropdown/paylaşım), `animate-ping` (canlı), `transition(-colors) duration-200/500`
- Yazı alanı: `font-unna font-bold` başlıklarda; `text-xs/sm/base/lg` /* 0.625rem'den 3rem'e */; `font-medium`; satır `line-clamp-1/2/3`, `text-ellipsis`, `whitespace-nowrap/pre`
- JS ile atanan sınıflar (CSS'te tanımı yok): `lastPrice`, `setTextColor`, `setIcon`, `setColor`, `percentChange` → fiyat yönüne göre `text-green-700` / `text-red-700` / `#9D9D9D` (JS renk eşlemesi birebir)
- Bileşen sınıfları CSS'te YOKSA da markup'a yaz (hook olarak) — klon iskeleti aynı `class=` desenini üretmelidir
