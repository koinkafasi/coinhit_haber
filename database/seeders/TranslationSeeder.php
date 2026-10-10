<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\BreakingNews;
use App\Models\Category;
use App\Models\News;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Demo TR içeriğinin tamamı için İngilizce çeviri (master spec §18.5).
 */
class TranslationSeeder extends Seeder
{
    /**
     * Kategori slug => İngilizce ad.
     */
    public const CATEGORY_NAMES = [
        'haberler' => 'NEWS',
        'piyasalar' => 'MARKETS',
        'enerji' => 'ENERGY',
        'ekonomik-veriler-ve-gundem' => 'ECONOMIC DATA & AGENDA',
        'finansal-teknoloji' => 'FINTECH',
        'tarim' => 'AGRICULTURE',
        'zirve' => 'SUMMIT',
        'kripto' => 'CRYPTO',
        'siyaset' => 'POLITICS',
        'sektor-haberleri' => 'SECTOR NEWS',
        'emtia' => 'COMMODITIES',
        'gorus' => 'OPINION',
        'hava-durumu' => 'Weather',
        'davos' => 'Davos',
        'turkiye-ekonomisi' => 'Turkish Economy',
        'kuresel-piyasalar' => 'Global Markets',
        'emtia-enerji' => 'Commodities & Energy',
    ];

    /**
     * Yazar slug => [başlık, biyografi].
     */
    public const AUTHOR_META = [
        'irfan-donat' => ['Agriculture Analysis', 'Writes on agriculture, energy and macroeconomics.'],
        'sefer-yuksel' => ['Financial Technology', 'Columnist on financial technology and digital banking.'],
        'ayse-karagoz' => ['Economy', 'Analyses on macroeconomic policy and central banking.'],
        'mehmet-demir' => ['Markets', 'Daily assessments on equities, FX and fixed income markets.'],
        'zeynep-arslan' => ['Global Markets', 'Writes on global markets and geopolitical developments.'],
        'kerem-ozturk' => ['Crypto & Funds', 'Views on crypto assets and investment funds.'],
    ];

    /**
     * Son dakika TR başlık => İngilizce başlık.
     */
    public const BREAKING = [
        'BIST 100 günü yükselişle kapattı' => 'BIST 100 closes the day higher',
        'Dolar/TL 28,50 seviyesinde dengelendi' => 'Dollar/TL steadies around 28.50',
        'Brent petrol 90 doların üzerinde seyrediyor' => 'Brent crude trades above $90',
        'TCMB rezervleri artışını sürdürüyor' => 'CBRT reserves continue to rise',
        'İstanbul için sağanak yağış uyarısı yapıldı' => 'Heavy rain warning issued for Istanbul',
        'Altın ons fiyatı 2.400 doları test ediyor' => 'Gold tests $2,400 an ounce',
        'Hazine ihalesine güçlü talep geldi' => 'Strong demand at Treasury auction',
        'Türkiye kredi notu görünümü iyileştirildi' => 'Turkey credit rating outlook improved',
        'Borsa İstanbul için yeni halka arz başvurusu yapıldı' => 'New IPO application filed for Borsa Istanbul',
        'Elektrik tüketiminde rekor seviye' => 'Record level in electricity consumption',
    ];

    /**
     * TR haber başlık => İngilizce başlık.
     */
    public const NEWS_TITLES = [
        // HABERLER
        "Küresel piyasalar Fed'in para politikası mesajlarını bekliyor" => "Global markets await the Fed's monetary policy signals",
        "Borsa İstanbul'da halka arzlar yeniden hız kazanıyor" => 'IPOs regain momentum on Borsa Istanbul',
        'Yatırımcıların risk iştahı temkinli seyrediyor' => "Investors' risk appetite remains cautious",
        'Şirketlerin üçüncü çeyrek bilançoları gelmeye başladı' => 'Companies begin reporting third-quarter earnings',
        'Konut piyasasında faiz indirimi beklentisi artıyor' => 'Rate-cut expectations grow in the housing market',
        'Otomotiv sektöründe elektrikli araç satışları artıyor' => 'Electric vehicle sales rise in the automotive sector',
        'Perakende sektörü yıl sonu indirimlerine hazırlanıyor' => 'Retail sector prepares for year-end discounts',
        'Genç yatırımcıların tercihleri değişiyor' => "Young investors' preferences are shifting",
        'Şirketler sürdürülebilirlik raporlamasına odaklanıyor' => 'Companies focus on sustainability reporting',
        'Yatırım araçlarında getiri arayışı hızlandı' => 'Search for yield in investment instruments accelerates',
        // PİYASALAR
        'BIST 100 rekor seviyeye yakın işlem görüyor' => 'BIST 100 trades near record highs',
        'Dolar/TL haftanın son işlem gününe dengeli başladı' => 'Dollar/TL starts the last trading day of the week steady',
        "Borsa İstanbul'da bankacılık hisseleri öne çıkıyor" => 'Banking stocks stand out on Borsa Istanbul',
        'Yabancı yatırımcıların hisse alımları sürüyor' => "Foreign investors' equity purchases continue",
        "BIST 100'de işlem hacmi gün boyu yoğun seyretti" => 'Trading volume on BIST 100 stays heavy all day',
        'Küresel borsalarda karmaşık görünüm hakim' => 'Mixed picture dominates global stock markets',
        'Halka arz sonrası hisselerde dalgalanma devam ediyor' => 'Volatility continues in post-IPO shares',
        'Temettü veren hisseler yeniden ilgi odağı oldu' => 'Dividend-paying stocks back in focus',
        "VİOP'ta yeni kontratlar yakından izleniyor" => 'New contracts on VIOP are closely watched',
        'Piyasa katılımcıları yeni haftaya temkinli girdi' => 'Market participants enter the new week cautiously',
        // ENERJİ
        'Brent petrol fiyatları jeopolitik risklerle yükseldi' => 'Brent crude rises on geopolitical risks',
        'Doğal gaz depolama seviyeleri yüksek seyrediyor' => 'Natural gas storage levels remain high',
        'Elektrik üretiminde yenilenebilir kaynakların payı artıyor' => "Renewables' share in power generation rises",
        "Türkiye'nin enerji ithalatı faturası geriliyor" => "Turkey's energy import bill declines",
        'Petrol piyasasında arz endişeleri yeniden gündemde' => 'Supply concerns return to the oil market agenda',
        'Akaryakıt fiyatlarında düzenleme beklentisi' => 'Expectation of an adjustment in fuel prices',
        // EKONOMİK VERİLER VE GÜNDEM
        'TÜİK enflasyon verilerini açıkladı' => 'TurkStat releases inflation data',
        'Cari açık yıllık bazda daraldı' => 'Current account deficit narrows year-on-year',
        'İşsizlik oranı beklentilerin altında kaldı' => 'Unemployment rate comes in below expectations',
        'Sanayi üretimi endeksi aylık bazda arttı' => 'Industrial production index rises month-on-month',
        'Dış ticaret açığı gerilemeye devam ediyor' => 'Foreign trade deficit keeps narrowing',
        'TCMB piyasa katılımcıları anketini yayımladı' => 'CBRT publishes market participants survey',
        // FİNANSAL TEKNOLOJİ
        'Dijital bankacılıkta kullanıcı sayısı yeni rekora koşuyor' => 'Digital banking users hit a new record',
        'Fintech yatırımlarında ödeme teknolojileri öne çıkıyor' => 'Payment technologies lead fintech investment',
        'Merkez bankaları dijital para çalışmalarını hızlandırdı' => 'Central banks speed up digital currency work',
        'Açık bankacılık ekosistemi büyümeye devam ediyor' => 'Open banking ecosystem keeps growing',
        'Yapay zeka destekli yatırım danışmanlığı yaygınlaşıyor' => 'AI-powered investment advice gains ground',
        // TARIM
        'Tahıl rekoltesi beklentileri yukarı yönlü revize edildi' => 'Grain harvest estimates revised upward',
        'Girdi maliyetleri çiftçinin gündeminde' => "Input costs top farmers' agenda",
        'Tarım ürünlerinde ihracat gelirleri arttı' => 'Export revenues from agricultural products rise',
        'Sera yatırımları hız kazanıyor' => 'Greenhouse investments pick up',
        'Genç girişimciler tarımsal teknolojiye ilgi gösteriyor' => 'Young entrepreneurs show interest in agritech',
        // ZİRVE
        'Bloomberg HT Zirve 2026 için kayıtlar başladı' => 'Registration opens for Bloomberg HT Summit 2026',
        'Zirvede ekonomi politikaları masaya yatırılacak' => 'Economic policies to be discussed at the summit',
        'Zirve programında yeni oturumlar açıklandı' => 'New sessions announced in the summit program',
        'İş dünyası liderleri zirvede buluşuyor' => 'Business leaders gather at the summit',
        // KRİPTO
        'Bitcoin 60 bin doların üzerinde tutunuyor' => 'Bitcoin holds above $60,000',
        'Kripto düzenlemeleri küresel ölçekte netleşiyor' => 'Crypto regulation takes shape globally',
        'Ethereum ağında işlem ücretleri geriledi' => 'Transaction fees fall on the Ethereum network',
        'Kurumsal yatırımcılar kripto fonlarına yöneliyor' => 'Institutional investors turn to crypto funds',
        'Stablecoin piyasası büyümeye devam ediyor' => 'Stablecoin market keeps growing',
        'Kripto madenciliğinde enerji tüketimi tartışılıyor' => 'Energy use in crypto mining under debate',
        // SİYASET
        'Ekonomi yönetimi yeni reform paketini açıkladı' => 'Economic team unveils new reform package',
        'Bütçe görüşmeleri TBMM gündeminde' => "Budget talks on parliament's agenda",
        'Vergi reformu çalışmaları sürüyor' => 'Tax reform work continues',
        'Yerel yönetimlerin mali politikaları tartışılıyor' => "Local governments' fiscal policies debated",
        'Parti liderlerinden ekonomiye ilişkin değerlendirmeler' => 'Party leaders assess the economy',
        // SEKTÖR HABERLERİ
        'Bankacılıkta kredi büyümesi güçlü seyrini koruyor' => 'Credit growth in banking stays strong',
        'Telekomünikasyon yatırımları hız kazanıyor' => 'Telecom investments pick up',
        'Sağlık turizminde yabancı hasta sayısı artıyor' => 'Foreign patient numbers rise in health tourism',
        'Çimento sektörü ihracatta rekor kırdı' => 'Cement sector sets an export record',
        'Turizm gelirleri yaz sezonunda yükseldi' => 'Tourism revenues rise in the summer season',
        // EMTİA
        'Altın ons fiyatı yatay bir seyir izliyor' => 'Gold trades sideways an ounce',
        'Gümüş fiyatları endüstriyel talepten destek buluyor' => 'Silver prices supported by industrial demand',
        'Bakır fiyatları Çin talebiyle yükseldi' => 'Copper prices rise on Chinese demand',
        'Kahve fiyatları arz endişeleriyle prim yaptı' => 'Coffee prices gain on supply concerns',
        'Buğday fiyatları denge arayışında' => 'Wheat prices seek a balance',
        'Emtia piyasalarında güvenli liman talebi artıyor' => 'Safe-haven demand rises in commodity markets',
        // GÖRÜŞ
        'Piyasaların yeni dengesi enflasyonla mücadelede belirlenecek' => "Markets' new balance will be shaped by the inflation fight",
        'Merkez bankası politikalarında geçiş dönemi' => 'A transition period in central bank policy',
        'TL varlıklara yabancı ilgisi artabilir' => 'Foreign interest in lira assets may rise',
        'Küresel ticaretin yeniden şekillenmesi' => 'The reshaping of global trade',
        'Altına küresel talep neden artıyor' => 'Why global demand for gold is rising',
        'Türkiye ekonomisinde yapısal reformların önemi' => "The importance of structural reforms in Turkey's economy",
        // HAVA DURUMU
        "İstanbul'da sağanak yağış etkili olacak" => 'Heavy showers to affect Istanbul',
        'Hafta sonu sıcaklıklar mevsim normallerinin üzerinde' => 'Weekend temperatures above seasonal norms',
        'Karadeniz kıyılarında kuvvetli rüzgar bekleniyor' => 'Strong winds expected along the Black Sea coast',
        "İç Anadolu'da don olayları tarımı etkileyebilir" => 'Frost in Central Anatolia may hit agriculture',
        // DAVOS
        "Davos'ta küresel ekonomik görünüm tartışılıyor" => 'Global economic outlook debated in Davos',
        "Dünya Ekonomik Forumu'nda yeşil dönüşüm gündemde" => 'Green transition on the World Economic Forum agenda',
        "Davos'ta Türkiye oturumu yoğun ilgi gördü" => 'Turkey session draws strong interest in Davos',
        // TÜRKİYE EKONOMİSİ
        'Türkiye ekonomisi ikinci çeyrekte büyümeye devam etti' => 'Turkish economy keeps growing in the second quarter',
        'İhracatta yeni pazarların payı artıyor' => 'Share of new markets in exports rises',
        'Yatırım ortamının iyileştirilmesi hedefleniyor' => 'Improving the investment climate targeted',
        'Rekabet gücü için verimlilik vurgusu yapıldı' => 'Productivity emphasized for competitiveness',
        // KÜRESEL PİYASALAR
        'Wall Street rekor kapanışlarla yılı tamamlamaya hazırlanıyor' => 'Wall Street prepares to close the year at records',
        'Asya borsaları karışık seyirle günü kapattı' => 'Asian markets close mixed',
        'Avrupa borsaları enerji hisseleriyle yükseldi' => 'European stocks rise with energy shares',
        "Japonya'da enflasyon ve politika beklentileri" => 'Inflation and policy expectations in Japan',
        // EMTİA & ENERJİ
        'Enerji ve emtia fiyatlarındaki dalgalanma devam ediyor' => 'Volatility in energy and commodity prices continues',
        'Altın ve petrol fiyatlarında ortak seyir izleniyor' => 'Gold and oil prices move in tandem',
        'Küresel emtia görünümü raporu yayımlandı' => 'Global commodities outlook report released',
    ];

    public const SETTINGS = [
        'site_tagline' => "Turkey's economy platform",
        'copyright' => 'LEGAL NOTICE © 2026',
        'legal_notice' => 'The data on our site is provided by Foreks. NASDAQ, Dow Jones Industrial Average, SHCOM, FTSE 100, CAC 40, DAX 30, Hang Seng, BOVESPA, VIOP and bond data are delayed by 15 minutes; CME, NYMEX and S&P data by 10 minutes.',
        'data_license' => 'Unauthorized use of CME data is prohibited.',
        'no_ai_training' => 'The content on this site may not be used to train any artificial intelligence model.',
    ];

    public function run(): void
    {
        $this->seedCategories();
        $this->seedAuthors();
        $this->seedNews();
        $this->seedBreaking();
        $this->seedSettings();
    }

    private function seedCategories(): void
    {
        Category::all()->each(function (Category $category) {
            $en = self::CATEGORY_NAMES[$category->getRawOriginal('slug')] ?? null;

            if ($en === null) {
                return;
            }

            $category->translations()->updateOrCreate(
                ['locale' => 'en'],
                ['name' => $en, 'slug' => Str::slug($en) ?: $category->getRawOriginal('slug')]
            );
        });
    }

    private function seedAuthors(): void
    {
        Author::all()->each(function (Author $author) {
            $meta = self::AUTHOR_META[$author->getRawOriginal('slug')] ?? null;

            if ($meta === null) {
                return;
            }

            $author->translations()->updateOrCreate(
                ['locale' => 'en'],
                [
                    'name' => $author->getRawOriginal('name'),
                    'title' => $meta[0],
                    'bio' => $meta[1],
                ]
            );
        });
    }

    private function seedNews(): void
    {
        $categoryNames = Category::all()->mapWithKeys(
            fn (Category $category) => [$category->getRawOriginal('slug') => $category->getRawOriginal('name')]
        );

        News::with(['category'])->get()->each(function (News $news) use ($categoryNames) {
            $trTitle = $news->getRawOriginal('title');
            $enTitle = self::NEWS_TITLES[$trTitle] ?? null;

            if ($enTitle === null) {
                return;
            }

            $trCategorySlug = $news->category?->getRawOriginal('slug');
            $enCategoryName = self::CATEGORY_NAMES[$trCategorySlug]
                ?? $categoryNames[$trCategorySlug]
                ?? ($news->category?->getRawOriginal('name') ?? 'Markets');

            [$spot, $body] = $this->englishContent($enTitle, $enCategoryName);

            $news->translations()->updateOrCreate(
                ['locale' => 'en'],
                [
                    'title' => $enTitle,
                    'slug' => Str::slug($enTitle),
                    'summary' => $spot,
                    'body' => $body,
                    'meta_title' => $enTitle,
                    'meta_description' => mb_substr(strip_tags($spot), 0, 255),
                ]
            );
        });
    }

    private function seedBreaking(): void
    {
        BreakingNews::all()->each(function (BreakingNews $item) {
            $en = self::BREAKING[$item->getRawOriginal('title')] ?? null;

            if ($en === null) {
                return;
            }

            $item->translations()->updateOrCreate(['locale' => 'en'], ['text' => $en]);
        });
    }

    private function seedSettings(): void
    {
        foreach (self::SETTINGS as $key => $value) {
            SiteSetting::where('key', $key)->first()?->translations()->updateOrCreate(
                ['locale' => 'en'],
                ['value' => $value]
            );
        }
    }

    /**
     * İngilizce demo spot + gövde üretir.
     *
     * @return array{0: string, 1: string}
     */
    private function englishContent(string $title, string $categoryName): array
    {
        $spot = implode("\n", [
            '- Market participants are closely following the headline "'.$title.'".',
            '- Experts shared their assessments on the matter.',
        ]);

        $paragraphs = [
            sprintf('Bloomberg HT analysis: %s is among the most discussed topics in the markets this week. The developments continue to shape investor decisions both domestically and globally.', $title),
            sprintf('Experts commenting on the issue say that developments in the %s category will gain even more importance in the period ahead. Data flow and central bank policy will be decisive.', $categoryName),
            'Market data shows that trading volume in the related assets increased during the day, with investors starting to take new positions according to price action. Experts warn that volatility may persist in the short term.',
            'Analysts are assessing how the developments will be reflected in corporate earnings and the growth outlook. Market expectations will be reshaped with the data due next week.',
            'As Bloomberg HT, we will continue to report all developments on the matter. You can follow our markets page for current pricing and expert views.',
        ];

        return [$spot, implode("\n\n", $paragraphs)];
    }
}
