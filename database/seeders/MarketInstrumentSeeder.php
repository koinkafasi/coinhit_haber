<?php

namespace Database\Seeders;

use App\Models\MarketInstrument;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Foreks benzeri piyasa veri katmanı enstrüman kataloğu (spec §5).
 *
 * Kataloğun tamamı DB'ye yazılır; canlı değerler MarketDataService
 * simülatörü tarafından üretilir.
 */
class MarketInstrumentSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->rows() as $row) {
            MarketInstrument::updateOrCreate(
                ['group' => $row['group'], 'code' => $row['code']],
                $row
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function rows(): array
    {
        return array_merge(
            $this->bistIndices(),
            $this->worldIndices(),
            $this->stocks(),
            $this->currencies(),
            $this->parities(),
            $this->emergingFx(),
            $this->gold(),
            $this->preciousMetals(),
            $this->agriculture(),
            $this->industry(),
            $this->energy(),
            $this->bondsTr(),
            $this->repo(),
            $this->eurobonds(),
            $this->intlBonds(),
            $this->alternative(),
            $this->baltic(),
            $this->crypto(),
        );
    }

    /**
     * @param  array<int, array{0: string, 1: string, 2: float}>  $data
     * @return array<int, array<string, mixed>>
     */
    private function map(string $group, array $data, array $opts = []): array
    {
        $rows = [];

        foreach ($data as $i => [$code, $name, $base]) {
            $rows[] = $this->row($group, $code, $name, $base, $opts + ['sort_order' => $i]);
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $opts
     * @return array<string, mixed>
     */
    private function row(string $group, string $code, string $name, float $base, array $opts = []): array
    {
        $currency = $opts['currency'] ?? 'TRY';
        $source = $opts['source'] ?? 'foreks';

        return [
            'group' => $group,
            'code' => $code,
            'name' => $name,
            'slug' => $opts['slug'] ?? Str::slug($name),
            'currency' => $currency,
            'decimals' => $opts['decimals'] ?? ($currency === 'TRY' ? 4 : 2),
            'unit' => $opts['unit'] ?? null,
            'base_price' => $base,
            'previous_close' => $opts['prev'] ?? round($base * 0.9915, $opts['decimals'] ?? ($currency === 'TRY' ? 4 : 2)),
            'meta' => $opts['meta'] ?? [],
            'source' => $source,
            'delay_minutes' => $opts['delay'] ?? (int) (config("market.sources.{$source}.delay_minutes") ?? 15),
            'sort_order' => $opts['sort_order'] ?? 0,
            'is_active' => true,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function bistIndices(): array
    {
        $data = [
            ['XU100', 'BIST 100', 10850.75], ['XU050', 'BIST 50', 9800.30], ['XU030', 'BIST 30', 11200.10],
            ['XTUMY', 'BIST TÜM', 10500.40], ['XTMTU', 'BIST TÜM-100', 10200.55], ['XBANK', 'BIST BANKA', 13500.20],
            ['XUSIN', 'BIST SINAİ', 9800.60], ['XBLSM', 'BIST BİLİŞİM', 14200.80], ['XELKT', 'BIST ELEKTRİK', 5600.30],
            ['XFINK', 'BIST FİNANSAL KİRALAMA', 2450.10], ['XGIDA', 'BIST GIDA İÇECEK', 7800.40], ['XGMYO', 'BIST GAYRİMENKUL YO', 4200.65],
            ['XHARZ', 'BIST HİZMETLER', 6900.20], ['XHOLD', 'BIST HOLDİNG', 8500.75], ['XILTM', 'BIST İLETİŞİM', 4100.35],
            ['XINSA', 'BIST İNŞAAT', 7200.90], ['XKAGT', 'BIST KAĞIT', 3300.55], ['XKMYA', 'BIST KİMYA PETROL', 9500.10],
            ['XKURY', 'BIST KURUMLAR', 6100.45], ['XMADN', 'BIST MADENCİLİK', 8800.25], ['XMANA', 'BIST METAL ANA', 10600.70],
            ['XMESY', 'BIST METAL EŞYA MAKİNA', 9900.15], ['XSPOR', 'BIST SPOR', 2200.80], ['XTAST', 'BIST TAŞ TOPRAK', 7600.35],
            ['XTCRT', 'BIST TİCARET', 4300.60], ['XTEKS', 'BIST TEKSTİL', 3400.25], ['XTM25', 'BIST 25', 11700.40],
            ['XTRZM', 'BIST TURİZM', 5100.90], ['XUHIZ', 'BIST HİZMETLER', 8900.55], ['XULAS', 'BIST ULAŞTIRMA', 12000.30],
            ['XUMAL', 'BIST MALİ', 12600.80], ['XUTEK', 'BIST TEKNOLOJİ', 15600.20], ['XUTUM', 'BIST TÜM-100', 10800.45],
            ['XYORT', 'BIST YATIRIM ORTAKLIKLARI', 3600.15], ['XYUZO', 'BIST YÜZ ÖLÇÜM DEĞERLERİ', 5400.30],
            ['XAUTO', 'BIST OTOMOTİV', 13400.55], ['XKTUM', 'BIST KATILIM TÜM', 11200.40], ['XK050', 'BIST KATILIM 50', 12050.25],
            ['XK030', 'BIST KATILIM 30', 12600.15], ['XSGRT', 'BIST SÜRDÜRÜLEBİLİRLİK', 9200.70],
            // Bölgesel + MSCI endeksleri (spec §5, §8; sitemap'te 65 endeks detay sayfası)
            ['MSCI_EURO', 'MSCI EURO BÖLGESİ', 2250.40], ['BIST_ADANA', 'BIST ADANA', 48500.25],
            ['MSCI_TURKEY', 'MSCI TÜRKİYE', 98500.60], ['MSCI_EMERGING', 'MSCI GELİŞEN PİYASALAR', 1120.35],
            ['BIST_ANKARA', 'BIST ANKARA', 74200.15], ['BIST_IZMIR', 'BIST İZMİR', 68800.45],
            ['BIST_ISTANBUL', 'BIST İSTANBUL', 126400.80], ['BIST_ANTALYA', 'BIST ANTALYA', 41200.55],
        ];

        return $this->map(MarketInstrument::GROUP_BIST_INDEX, $data, ['decimals' => 2]);
    }

    /**
     * Dünya borsa endeksleri (spec §2.4, §7, §8 /borsa/dunya-borsalari).
     *
     * @return array<int, array<string, mixed>>
     */
    private function worldIndices(): array
    {
        $data = [
            ['DOW', 'DOW JONES', 44100.30], ['SP500', 'S&P 500', 6050.40], ['NASDAQ', 'NASDAQ 100', 21800.75],
            ['DAX', 'DAX 40', 18450.20], ['FTSE100', 'FTSE 100', 8280.60], ['CAC40', 'CAC 40', 7620.90],
            ['SHANGHAI', 'SHANGHAI', 3310.45], ['NIKKEI225', 'NIKKEI 225', 39250.35], ['HANGSENG', 'HANG SENG', 19850.10],
            ['KOSPI', 'KORE KOSPI', 2760.55], ['SPFUT', 'S&P FUT', 6045.80], ['NASDAQFUT', 'NASDAQ 100 FUT', 21790.25],
        ];

        return $this->map(MarketInstrument::GROUP_WORLD_INDEX, $data, ['currency' => 'USD', 'decimals' => 2, 'source' => 'foreks']);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function stocks(): array
    {
        $data = [
            ['GARAN', 'Garanti BBVA', 145.30], ['AKBNK', 'Akbank', 68.40], ['YKBNK', 'Yapı Kredi', 34.20],
            ['ISCTR', 'İş Bankası (C)', 15.80], ['HALKB', 'Halkbank', 22.10], ['VAKBN', 'Vakıfbank', 20.90],
            ['THYAO', 'Türk Hava Yolları', 315.50], ['ASELS', 'Aselsan', 78.20], ['TUPRS', 'Tüpraş', 158.70],
            ['EREGL', 'Ereğli Demir Çelik', 52.40], ['SISE', 'Şişecam', 48.90], ['KCHOL', 'Koç Holding', 195.60],
            ['SAHOL', 'Sabancı Holding', 88.30], ['TCELL', 'Turkcell', 102.40], ['TTKOM', 'Türk Telekom', 45.70],
            ['BIMAS', 'BİM Birleşik Mağazalar', 520.30], ['AEFES', 'Anadolu Efes', 165.20], ['FROTO', 'Ford Otosan', 1280.00],
            ['TOASO', 'Tofaş', 285.40], ['PGSUS', 'Pegasus', 1450.00], ['TAVHL', 'TAV Havalimanları', 285.60],
            ['PETKM', 'Petkim', 24.30], ['KOZAL', 'Koza Altın', 38.70], ['KOZAA', 'Koza Anadolu Metal', 62.10],
            ['TKFEN', 'Tekfen Holding', 78.90], ['MGROS', 'Migros', 480.20], ['SOKM', 'Şok Marketler', 62.40],
            ['ARCLK', 'Arçelik', 145.80], ['VESTL', 'Vestel', 38.20], ['ENKAI', 'Enka İnşaat', 48.60],
            ['ODAS', 'Odaş Elektrik', 12.30], ['ZOREN', 'Zorlu Enerji', 6.80], ['EKGYO', 'Emlak Konut GYO', 12.90],
            ['ISDMR', 'İskenderun Demir', 32.40], ['GUBRF', 'Gübre Fabrikaları', 185.60], ['HEKTS', 'Hektaş', 18.70],
            ['OYAKC', 'Oyak Çimento', 62.30], ['DOHOL', 'Doğan Holding', 15.20], ['KRDMD', 'Kardemir (D)', 28.90],
            ['TRGYO', 'Torunlar GYO', 78.40],
        ];

        return $this->map(MarketInstrument::GROUP_STOCK, $data, ['decimals' => 2]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function currencies(): array
    {
        $data = [
            ['USDTRY', 'ABD DOLARI', 43.2500], ['EURTRY', 'EURO', 47.1000], ['GBPTRY', 'İNGİLİZ STERLİNİ', 55.4000],
            ['CHFTRY', 'İSVİÇRE FRANGI', 49.8000], ['JPYTRY', '100 JAPON YENİ', 28.7000, ['decimals' => 4]],
            ['SARTRY', 'SUUDİ ARABİSTAN RİYALİ', 11.5300], ['NOKTRY', 'NORVEÇ KRONU', 3.9500], ['DKKTRY', 'DANİMARKA KRONU', 6.3200],
            ['AUDTRY', 'AVUSTRALYA DOLARI', 28.4000], ['CADTRY', 'KANADA DOLARI', 31.7000], ['SEKTRY', 'İSVEÇ KRONU', 4.1000],
            ['RUBTRY', 'RUS RUBLESİ', 0.4500], ['CNYTRY', 'ÇİN YUANI', 5.9800], ['AEDTRY', 'BAE DİRHEMİ', 11.7800],
        ];

        $rows = [];

        foreach ($data as $i => $item) {
            [$code, $name, $base] = $item;
            $opts = $item[3] ?? [];
            $rows[] = $this->row(MarketInstrument::GROUP_CURRENCY, $code, $name, $base, $opts + ['sort_order' => $i, 'currency' => 'TRY', 'decimals' => 4]);
        }

        return $rows;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function parities(): array
    {
        $data = [
            ['EURUSD', 'EUR/USD', 1.0850], ['GBPUSD', 'GBP/USD', 1.2650], ['USDJPY', 'USD/JPY', 149.20],
            ['USDCHF', 'USD/CHF', 0.8850], ['USDCAD', 'USD/CAD', 1.3650], ['AUDUSD', 'AUD/USD', 0.6550],
            ['NZDUSD', 'NZD/USD', 0.6050], ['EURGBP', 'EUR/GBP', 0.8580], ['EURJPY', 'EUR/JPY', 161.90],
            ['EURCHF', 'EUR/CHF', 0.9600], ['EURCAD', 'EUR/CAD', 1.4810], ['EURAUD', 'EUR/AUD', 1.6560],
            ['EURNZD', 'EUR/NZD', 1.7930], ['GBPJPY', 'GBP/JPY', 188.70], ['GBPCHF', 'GBP/CHF', 1.1190],
            ['GBPCAD', 'GBP/CAD', 1.7260], ['GBPAUD', 'GBP/AUD', 1.9310], ['AUDJPY', 'AUD/JPY', 97.70],
            ['CADJPY', 'CAD/JPY', 109.30], ['CHFJPY', 'CHF/JPY', 168.60], ['NZDJPY', 'NZD/JPY', 90.30],
            ['AUDNZD', 'AUD/NZD', 1.0820], ['AUDCAD', 'AUD/CAD', 0.8940], ['AUDCHF', 'AUD/CHF', 0.5790],
            ['NZDCAD', 'NZD/CAD', 0.8260], ['NZDCHF', 'NZD/CHF', 0.5350], ['CADCHF', 'CAD/CHF', 0.6480],
            ['USDSGD', 'USD/SGD', 1.3450], ['EURSGD', 'EUR/SGD', 1.4590], ['GBPSGD', 'GBP/SGD', 1.7010],
            ['AUDSGD', 'AUD/SGD', 0.8810], ['USDHKD', 'USD/HKD', 7.8200], ['EURHKD', 'EUR/HKD', 8.4850],
            ['GBPHKD', 'GBP/HKD', 9.8930], ['USDCNH', 'USD/CNH', 7.2450], ['EURCNH', 'EUR/CNH', 7.8620],
            ['USDINR', 'USD/INR', 83.2000], ['EURINR', 'EUR/INR', 90.3000], ['USDBRL', 'USD/BRL', 4.9800],
            ['EURBRL', 'EUR/BRL', 5.4030], ['DXY', 'DOLAR ENDEKSİ', 104.50],
        ];

        $yenDecimals = ['USDJPY', 'EURJPY', 'GBPJPY', 'AUDJPY', 'CADJPY', 'CHFJPY', 'NZDJPY'];
        $rows = [];

        foreach ($data as $i => [$code, $name, $base]) {
            $rows[] = $this->row(MarketInstrument::GROUP_PARITY, $code, $name, $base, [
                'decimals' => in_array($code, $yenDecimals, true) ? 2 : 4,
                'currency' => 'USD',
                'sort_order' => $i,
            ]);
        }

        return $rows;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function emergingFx(): array
    {
        $data = [
            ['USDMXN', 'USD/MXN', 17.1500], ['USDCZK', 'USD/CZK', 23.2000], ['USDPLN', 'USD/PLN', 3.9800],
            ['USDHUF', 'USD/HUF', 355.6000], ['USDRUB', 'USD/RUB', 92.5000], ['USDZAR', 'USD/ZAR', 18.7500],
        ];

        return $this->map(MarketInstrument::GROUP_EMERGING_FX, $data, ['decimals' => 4, 'currency' => 'USD']);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function gold(): array
    {
        $data = [
            ['GAU', 'ALTIN/ONS ($)', 2650.50, ['currency' => 'USD', 'decimals' => 2, 'source' => 'cme']],
            ['GAUTRY', 'ALTIN/ONS (TL)', 114000.00, ['decimals' => 2]],
            ['GRAM', 'GRAM ALTIN', 3670.00, ['decimals' => 2]],
            ['CUMHUR', 'CUMHURİYET ALTINI', 24500.00, ['decimals' => 2]],
            ['TAM', 'TAM ALTIN', 16300.00, ['decimals' => 2]],
            ['ZIYNET', 'ZİYNET ALTINI', 16000.00, ['decimals' => 2]],
            ['CEYREK', 'ÇEYREK ALTIN', 6100.00, ['decimals' => 2]],
            ['YARIM', 'YARIM ALTIN', 12200.00, ['decimals' => 2]],
            ['ATA', 'ATA ALTIN', 16800.00, ['decimals' => 2]],
            ['BILEZIK14', '14 AYAR BİLEZİK', 2050.00, ['decimals' => 2]],
            ['BILEZIK22', '22 AYAR BİLEZİK', 3200.00, ['decimals' => 2]],
            ['GREMSE', 'GREMSE ALTIN', 32700.00, ['decimals' => 2]],
            ['HAS', 'HAS ALTIN', 3700.00, ['decimals' => 2]],
            ['IKIBUCUK', 'İKİBUÇUK ALTIN', 30500.00, ['decimals' => 2]],
            ['BESLI', 'BEŞLİ ALTIN', 61000.00, ['decimals' => 2]],
            ['KULCE_EUR', 'KÜLÇE EURO', 4100.00, ['decimals' => 2]],
            ['KULCE_USD', 'KÜLÇE DOLAR', 4250.00, ['decimals' => 2]],
        ];

        $rows = [];

        foreach ($data as $i => $item) {
            [$code, $name, $base] = $item;
            $rows[] = $this->row(MarketInstrument::GROUP_GOLD, $code, $name, $base, ($item[3] ?? []) + ['sort_order' => $i]);
        }

        return $rows;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function preciousMetals(): array
    {
        $data = [
            ['GUMUS_GRAM', 'GÜMÜŞ GRAM', 42.50], ['GUMUS_ONS', 'GÜMÜŞ ONS', 31.20],
            ['PLATIN', 'PLATİN', 980.00], ['PALADYUM', 'PALADYUM', 1050.00],
        ];

        return $this->map(MarketInstrument::GROUP_PRECIOUS_METAL, $data, ['currency' => 'USD', 'source' => 'cme']);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function agriculture(): array
    {
        $data = [
            ['PAMUK', 'PAMUK', 72.40], ['KAKAO', 'KAKAO', 9800.00], ['KAHVE', 'KAHVE', 245.00],
        ];

        return $this->map(MarketInstrument::GROUP_AGRICULTURE, $data, ['currency' => 'USD', 'source' => 'cme']);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function industry(): array
    {
        $data = [
            ['BAKIR', 'BAKIR', 4.15], ['ALUMINYUM', 'ALÜMİNYUM', 2.45], ['CINKO', 'ÇİNKO', 2.85],
            ['NIKEL', 'NİKEL', 16.20], ['KALAY', 'KALAY', 32.50], ['KURSUN', 'KURŞUN', 2.10],
            ['BAKIR3A', 'BAKIR (3 Aylık)', 4.22], ['ALUMINYUM3A', 'ALÜMİNYUM (3 Aylık)', 2.50],
            ['CINKO3A', 'ÇİNKO (3 Aylık)', 2.90], ['NIKEL3A', 'NİKEL (3 Aylık)', 16.45],
            ['KALAY3A', 'KALAY (3 Aylık)', 32.80], ['KURSUN3A', 'KURŞUN (3 Aylık)', 2.14],
        ];

        return $this->map(MarketInstrument::GROUP_INDUSTRY, $data, ['currency' => 'USD', 'source' => 'cme']);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function energy(): array
    {
        $data = [
            ['WTI', 'WTI HAM PETROL', 78.40], ['BRENT', 'BRENT PETROL', 82.60],
        ];

        return $this->map(MarketInstrument::GROUP_ENERGY, $data, ['currency' => 'USD', 'source' => 'cme']);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function bondsTr(): array
    {
        $data = [
            ['TR2Y', 'TR 2 Yıllık Tahvil', 42.50], ['TR5Y', 'TR 5 Yıllık Tahvil', 38.20], ['TR10Y', 'TR 10 Yıllık Tahvil', 34.70],
        ];

        return $this->map(MarketInstrument::GROUP_BOND_TR, $data, ['unit' => '%', 'decimals' => 2]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function repo(): array
    {
        $data = [
            ['REPO_GECELIK', 'Gecelik Repo', 45.00], ['REPO_HAFTALIK', 'Haftalık Repo', 45.50], ['REPO_AYLIK', 'Aylık Repo', 46.20],
        ];

        return $this->map(MarketInstrument::GROUP_REPO, $data, ['unit' => '%', 'decimals' => 2]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function eurobonds(): array
    {
        $data = [
            ['EUROBOND_TR_1Y', 'TR Eurobond 1 Yıl', 6.20], ['EUROBOND_TR_2Y', 'TR Eurobond 2 Yıl', 6.50],
            ['EUROBOND_TR_3Y', 'TR Eurobond 3 Yıl', 6.85], ['EUROBOND_TR_4Y', 'TR Eurobond 4 Yıl', 7.15],
            ['EUROBOND_TR_5Y', 'TR Eurobond 5 Yıl', 7.45], ['EUROBOND_TR_6Y', 'TR Eurobond 6 Yıl', 7.70],
            ['EUROBOND_TR_7Y', 'TR Eurobond 7 Yıl', 7.95], ['EUROBOND_TR_8Y', 'TR Eurobond 8 Yıl', 8.20],
            ['EUROBOND_TR_10Y', 'TR Eurobond 10 Yıl', 8.55], ['EUROBOND_TR_20Y', 'TR Eurobond 20 Yıl', 8.90],
            ['EUROBOND_TR_30Y', 'TR Eurobond 30 Yıl', 9.10],
        ];

        return $this->map(MarketInstrument::GROUP_EUROBOND, $data, ['unit' => '%', 'decimals' => 3]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function intlBonds(): array
    {
        $data = [
            ['US2Y', 'ABD 2 Yıllık', 4.35], ['US5Y', 'ABD 5 Yıllık', 4.20], ['US10Y', 'ABD 10 Yıllık', 4.25],
            ['DE2Y', 'ALMANYA 2 Yıllık', 2.85], ['DE5Y', 'ALMANYA 5 Yıllık', 2.65], ['DE10Y', 'ALMANYA 10 Yıllık', 2.55],
            ['GB2Y', 'İNGİLTERE 2 Yıllık', 4.45], ['GB5Y', 'İNGİLTERE 5 Yıllık', 4.30], ['GB10Y', 'İNGİLTERE 10 Yıllık', 4.35],
            ['JP2Y', 'JAPONYA 2 Yıllık', 0.35], ['JP5Y', 'JAPONYA 5 Yıllık', 0.55], ['JP10Y', 'JAPONYA 10 Yıllık', 0.95],
        ];

        return $this->map(MarketInstrument::GROUP_INTL_BOND, $data, ['unit' => '%', 'decimals' => 3]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function alternative(): array
    {
        $data = [
            ['VIX', 'VIX Korku Endeksi', 15.40], ['BCOM', 'Bloomberg Emtia Endeksi', 105.20],
        ];

        return $this->map(MarketInstrument::GROUP_ALTERNATIVE, $data, ['source' => 'cme']);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function baltic(): array
    {
        $data = [
            ['BDIY', 'Baltık Kuru Endeksi', 1650.00], ['BCI14', 'Capesize Endeksi', 2400.00],
            ['BPIY', 'Panamax Endeksi', 1450.00], ['BSI58A', 'Supramax Endeksi', 980.00], ['BHSI', 'Handysize Endeksi', 620.00],
        ];

        return $this->map(MarketInstrument::GROUP_BALTIC, $data, ['currency' => 'USD', 'decimals' => 2]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function crypto(): array
    {
        $data = [
            ['BTC', 'Bitcoin', 95000.00, 19_700_000, 42_000_000_000],
            ['ETH', 'Ethereum', 3500.00, 120_000_000, 18_000_000_000],
            ['USDT', 'Tether', 1.00, 120_000_000_000, 55_000_000_000],
            ['BNB', 'BNB', 620.00, 145_000_000, 1_800_000_000],
            ['XRP', 'XRP', 2.35, 57_000_000_000, 3_200_000_000],
            ['USDC', 'USD Coin', 1.00, 42_000_000_000, 7_000_000_000],
            ['SOL', 'Solana', 195.00, 470_000_000, 4_100_000_000],
            ['TRX', 'TRON', 0.16, 87_000_000_000, 680_000_000],
            ['DOGE', 'Dogecoin', 0.38, 147_000_000_000, 2_400_000_000],
            ['ADA', 'Cardano', 0.95, 35_000_000_000, 1_100_000_000],
            ['LINK', 'Chainlink', 22.50, 620_000_000, 720_000_000],
            ['XMR', 'Monero', 165.00, 18_400_000, 90_000_000],
            ['LTC', 'Litecoin', 105.00, 75_000_000, 480_000_000],
            ['AVAX', 'Avalanche', 42.00, 400_000_000, 560_000_000],
            ['DOT', 'Polkadot', 8.20, 1_400_000_000, 340_000_000],
            ['BCH', 'Bitcoin Cash', 480.00, 19_800_000, 620_000_000],
            ['SHIB', 'Shiba Inu', 0.000028, 589_000_000_000_000, 780_000_000, ['decimals' => 8]],
            ['TON', 'Toncoin', 5.20, 2_500_000_000, 260_000_000],
            ['MATIC', 'Polygon', 0.85, 9_900_000_000, 410_000_000],
            ['DAI', 'Dai', 1.00, 5_300_000_000, 180_000_000],
        ];

        $rows = [];

        foreach ($data as $i => $item) {
            [$code, $name, $base, $supply, $volume] = $item;
            $opts = $item[5] ?? [];
            $rows[] = $this->row(MarketInstrument::GROUP_CRYPTO, $code, $name, $base, $opts + [
                'currency' => 'USD',
                'source' => 'crypto',
                'decimals' => 2,
                'meta' => ['supply' => $supply, 'volume_24h' => $volume],
                'sort_order' => $i,
            ]);
        }

        return $rows;
    }
}
