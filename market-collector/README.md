# Market Collector (Go)

BloombergHT klonu için **ücretsiz gerçek piyasa verisi** toplayıcı.
Tüm verileri PostgreSQL `market_snapshots` tablosuna yazar; Laravel
`MarketDataService` taze satırları okuyarak simülatörü gerçek veriyle besler.

## Kaynaklar

| Kaynak | Kapsam | Gecikme | Lisans/Maliyet |
|---|---|---|---|
| **TCMB** (`tcmb.go`) | TL çaprazları (USDTRY, EURTRY, ...) | Resmi gösterge kur | ✅ Ücretsiz, resmi |
| **Yahoo Finance** (`yahoo.go`) | BIST endeks/hisse (`.IS`), pariteler (`=X`), emtia/vadeli (`=F`), dünya endeksleri (`^`), ABD tahvil getirileri (`^TNX`...) | 15 dk gecikmeli | ✅ Ücretsiz, anahtarsız |
| **TCMB EVDS** (`evds.go`) | Repo faizleri (BİST O/N + TCMB haftalık repo) | İş günü bazlı | ✅ Ücretsiz (üyelik API key, `EVDS_API_KEY`) |
| **goldprice.dev** (`goldprice.go`) | XAU/XAG spot (~30-60sn) — opsiyonel | Spot | ✅ Ücretsiz (anahtar opsiyonel) |
| **CoinGecko** (mevcut PHP) | Kripto | Anlık | ✅ Mevcut `.env` anahtarı |

## Kurulum & Derleme

```bash
cd ~/coinhit/market-collector
# Go kurulumu (yoksa):
# curl -sSL https://go.dev/dl/go1.23.4.linux-amd64.tar.gz | sudo tar -C /usr/local -xzf -
export PATH="$PATH:/usr/local/go/bin"
go mod tidy
go build -o market-collector .
```

## Çalıştırma

```bash
# DB değişkenleri coinhit/.env'den otomatik alınır (.env DB_* bloğu)
./run.sh

# Elle / özel ortam
COLLECTOR_DEBUG=1 ./market-collector
```

## Otomasyon

`routes/console.php` içinde Laravel zamanlayıcısına bağlıdır
(Schedule::exec → `market-collector/run.sh`, 15 dakikada bir).
Laravel zamanlayıcısı WSL `crontab` aracılığıyla her dakika çalışır:

```
* * * * * cd /home/alper/coinhit && /usr/bin/php artisan schedule:run >> /tmp/laravel-schedule.log 2>&1
```

## Enstrüman haritası

- `bist_index`/`stock` → `<KOD>.IS`
- `parity` → `<KOD>=X` (DXY → `DX-Y.NYB`)
- `world_index` → tablo içinde `^DJI`, `^GSPC`, `^N225` vb.
- `gold` GAU → `GC=F` ; GRAM/GAUTRY → `GC=F` × USD/TRY ÷ 31.1035 (türev)
- `precious_metal` GUMUS_ONS→`SI=F`, PLATIN→`PL=F`, PALADYUM→`PA=F`; GUMUS_GRAM türev
- `energy` WTI→`CL=F`, BRENT→`BZ=F`
- `agriculture` PAMUK→`CT=F`, KAHVE→`KC=F`, KAKAO→`CC=F`
- `industry` BAKIR→`HG=F`
- `alternative` VIX→`^VIX`
- `intl_bond` US10Y→`^TNX`, US5Y→`^FVX`, US30Y→`^TYX`

Eşleşmeyen enstrümanlar (tahvil/repo/eurobond/baltık/madeni altın vb.)
Laravel simülatörüne düşer; veri bulunamazsa site asla kırılmaz.

## Not

- `captured_at` uygulama saat diliminde (Europe/Istanbul) yazılır — Laravel'in
  naive timestamp kuralıyla uyumlu.
- 7 günden eski snapshot'lar her koşuda temizlenir (`prune`).
- **EVDS**: evds3 host'undaki WAF (Imperva) Go yerine `curl` istemcisine JSON
  verir; bu yüzden EVDS istekleri sistemdeki `curl` ile yapılır (`evds.go`).
  `endDate` bilinçli olarak "dün" tutulur (bugünün bülteni henüz yayımlanmamış
  olabileceği için EVDS HTML dönebilir). EVDS hız limiti kısa süre blok atabilir;
  toplayıcı 15 dk'da bir denediği için pencere açılınca kendiliğinden dolar.
- Repo seri kodları: `REPO_GECELIK`→`TP.AOFOBAP`, `REPO_HAFTALIK`→`TP.PY.P02.1H`.
  TR 2/5/10 yıllık gösterge tahvil için ücretsiz sabit-vade serisi yok (EVDS
  per-ISIN yayımlar); bu enstrümanlar simülatörde kalır.
- Madeni altın: darphane ağırlık × ayar × gram-has fiyatından türetilir
  (serbest piyasa fiyatına çok yakın; `gold_coins.go`).
