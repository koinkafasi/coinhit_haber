<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Category;
use App\Models\News;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    /**
     * Kategori slug'ı => başlık listesi (Bloomberg HT tarzı demo içerik).
     */
    public const TITLES = [
        'haberler' => [
            "Küresel piyasalar Fed'in para politikası mesajlarını bekliyor",
            "Borsa İstanbul'da halka arzlar yeniden hız kazanıyor",
            'Yatırımcıların risk iştahı temkinli seyrediyor',
            'Şirketlerin üçüncü çeyrek bilançoları gelmeye başladı',
            'Konut piyasasında faiz indirimi beklentisi artıyor',
            'Otomotiv sektöründe elektrikli araç satışları artıyor',
            'Perakende sektörü yıl sonu indirimlerine hazırlanıyor',
            'Genç yatırımcıların tercihleri değişiyor',
            'Şirketler sürdürülebilirlik raporlamasına odaklanıyor',
            'Yatırım araçlarında getiri arayışı hızlandı',
        ],
        'piyasalar' => [
            'BIST 100 rekor seviyeye yakın işlem görüyor',
            'Dolar/TL haftanın son işlem gününe dengeli başladı',
            "Borsa İstanbul'da bankacılık hisseleri öne çıkıyor",
            'Yabancı yatırımcıların hisse alımları sürüyor',
            "BIST 100'de işlem hacmi gün boyu yoğun seyretti",
            'Küresel borsalarda karmaşık görünüm hakim',
            'Halka arz sonrası hisselerde dalgalanma devam ediyor',
            'Temettü veren hisseler yeniden ilgi odağı oldu',
            "VİOP'ta yeni kontratlar yakından izleniyor",
            'Piyasa katılımcıları yeni haftaya temkinli girdi',
        ],
        'enerji' => [
            'Brent petrol fiyatları jeopolitik risklerle yükseldi',
            'Doğal gaz depolama seviyeleri yüksek seyrediyor',
            'Elektrik üretiminde yenilenebilir kaynakların payı artıyor',
            "Türkiye'nin enerji ithalatı faturası geriliyor",
            'Petrol piyasasında arz endişeleri yeniden gündemde',
            'Akaryakıt fiyatlarında düzenleme beklentisi',
        ],
        'ekonomik-veriler-ve-gundem' => [
            'TÜİK enflasyon verilerini açıkladı',
            'Cari açık yıllık bazda daraldı',
            'İşsizlik oranı beklentilerin altında kaldı',
            'Sanayi üretimi endeksi aylık bazda arttı',
            'Dış ticaret açığı gerilemeye devam ediyor',
            'TCMB piyasa katılımcıları anketini yayımladı',
        ],
        'finansal-teknoloji' => [
            'Dijital bankacılıkta kullanıcı sayısı yeni rekora koşuyor',
            'Fintech yatırımlarında ödeme teknolojileri öne çıkıyor',
            'Merkez bankaları dijital para çalışmalarını hızlandırdı',
            'Açık bankacılık ekosistemi büyümeye devam ediyor',
            'Yapay zeka destekli yatırım danışmanlığı yaygınlaşıyor',
        ],
        'tarim' => [
            'Tahıl rekoltesi beklentileri yukarı yönlü revize edildi',
            'Girdi maliyetleri çiftçinin gündeminde',
            'Tarım ürünlerinde ihracat gelirleri arttı',
            'Sera yatırımları hız kazanıyor',
            'Genç girişimciler tarımsal teknolojiye ilgi gösteriyor',
            'Buğday ithalatında yeni dönem',
            'TMO çeltik alım fiyatlarını açıkladı',
            'Şeker pancarı alım fiyatı belli oldu',
            'Fındıkta rekolte tahmini açıklandı',
            'Pamuk üretiminde rekor beklentisi',
            'Ayçiçeğinde hasat dönemi başladı',
            'Zeytinyağı fiyatlarında denge arayışı',
            'Tarımsal destek ödemeleri hesaplara yattı',
            'Kuraklık tarımsal üretimi etkiliyor',
            'Gübre fiyatlarında gerileme sürüyor',
            'Kırsal kalkınmada yeni destek programı',
            'Tarım sigortalarında kapsam genişliyor',
            'Kooperatiflere sıfır faizli kredi desteği',
            'Akıllı tarım uygulamaları yaygınlaşıyor',
            'Organik tarımda üretim alanları artıyor',
            'Süt fiyatlarında üretici memnuniyeti',
            'Seracılıkta jeotermal yatırımlar',
            'Tarımsal istihdamda gençleşme',
            'Hasat döneminde lojistik planlamalar',
        ],
        'zirve' => [
            'Bloomberg HT Zirve 2026 için kayıtlar başladı',
            'Zirvede ekonomi politikaları masaya yatırılacak',
            'Zirve programında yeni oturumlar açıklandı',
            'İş dünyası liderleri zirvede buluşuyor',
        ],
        'kripto' => [
            'Bitcoin 60 bin doların üzerinde tutunuyor',
            'Kripto düzenlemeleri küresel ölçekte netleşiyor',
            'Ethereum ağında işlem ücretleri geriledi',
            'Kurumsal yatırımcılar kripto fonlarına yöneliyor',
            'Stablecoin piyasası büyümeye devam ediyor',
            'Kripto madenciliğinde enerji tüketimi tartışılıyor',
        ],
        'siyaset' => [
            'Ekonomi yönetimi yeni reform paketini açıkladı',
            'Bütçe görüşmeleri TBMM gündeminde',
            'Vergi reformu çalışmaları sürüyor',
            'Yerel yönetimlerin mali politikaları tartışılıyor',
            'Parti liderlerinden ekonomiye ilişkin değerlendirmeler',
        ],
        'sektor-haberleri' => [
            'Bankacılıkta kredi büyümesi güçlü seyrini koruyor',
            'Telekomünikasyon yatırımları hız kazanıyor',
            'Sağlık turizminde yabancı hasta sayısı artıyor',
            'Çimento sektörü ihracatta rekor kırdı',
            'Turizm gelirleri yaz sezonunda yükseldi',
        ],
        'emtia' => [
            'Altın ons fiyatı yatay bir seyir izliyor',
            'Gümüş fiyatları endüstriyel talepten destek buluyor',
            'Bakır fiyatları Çin talebiyle yükseldi',
            'Kahve fiyatları arz endişeleriyle prim yaptı',
            'Buğday fiyatları denge arayışında',
            'Emtia piyasalarında güvenli liman talebi artıyor',
        ],
        'gorus' => [
            'Piyasaların yeni dengesi enflasyonla mücadelede belirlenecek',
            'Merkez bankası politikalarında geçiş dönemi',
            'TL varlıklara yabancı ilgisi artabilir',
            'Küresel ticaretin yeniden şekillenmesi',
            'Altına küresel talep neden artıyor',
            "Türkiye ekonomisinde yapısal reformların önemi",
            'Tarımda fiyat istikrarı için politikalar gerekli',
            'Çiftçinin finansmana erişimi kolaylaşmalı',
            'Tarımın geleceği teknolojide',
        ],
        'hava-durumu' => [
            "İstanbul'da sağanak yağış etkili olacak",
            'Hafta sonu sıcaklıklar mevsim normallerinin üzerinde',
            "Karadeniz kıyılarında kuvvetli rüzgar bekleniyor",
            "İç Anadolu'da don olayları tarımı etkileyebilir",
        ],
        'davos' => [
            "Davos'ta küresel ekonomik görünüm tartışılıyor",
            "Dünya Ekonomik Forumu'nda yeşil dönüşüm gündemde",
            "Davos'ta Türkiye oturumu yoğun ilgi gördü",
        ],
        'turkiye-ekonomisi' => [
            "Türkiye ekonomisi ikinci çeyrekte büyümeye devam etti",
            'İhracatta yeni pazarların payı artıyor',
            'Yatırım ortamının iyileştirilmesi hedefleniyor',
            'Rekabet gücü için verimlilik vurgusu yapıldı',
        ],
        'kuresel-piyasalar' => [
            'Wall Street rekor kapanışlarla yılı tamamlamaya hazırlanıyor',
            'Asya borsaları karışık seyirle günü kapattı',
            'Avrupa borsaları enerji hisseleriyle yükseldi',
            "Japonya'da enflasyon ve politika beklentileri",
        ],
        'emtia-enerji' => [
            'Enerji ve emtia fiyatlarındaki dalgalanma devam ediyor',
            'Altın ve petrol fiyatlarında ortak seyir izleniyor',
            'Küresel emtia görünümü raporu yayımlandı',
        ],
    ];
    /**
     * Kategori slug'ı => ek başlıklar (konumsal sayacı bozmadan eklenen içerik).
     *
     * Zirve kaynakta 12 kartlık grid ile gösteriliyor; TITLES'ta yalnızca 4
     * başlık tanımlıydı. Yeni başlıklar burada tutulur ve EXTRA_ID_BASE'ten
     * başlayan AYRI bir ID aralığına yazılır — böylece TITLES'a içerik eklemek
     * mevcut kayıtların `3791000 + $count` numaralarını kaydırmaz, seed
     * idempotent kalır.
     */
    public const EXTRA_ID_BASE = 3792000;

    public const EXTRA_TITLES = [
        'zirve' => [
            'Yapay zeka altyapısına yatırım hızlanıyor',
            'Elektrikli araç batarya zincirinde Avrupa hamlesi',
            'Finansal teknolojilerde regülasyon güncellemesi',
            'Girişim sermayesi fonları teknolojiye yöneliyor',
            'Küresel tedarik zincirinde çeşitlendirme arayışı',
            'Veri merkezlerinde enerji verimliliği gündemi',
            'Yarı iletken yatırımları bölgesel hâle geliyor',
            'Dijital bankacılıkta müşteri deneyimi öncelik kazandı',
        ],
    ];


    public const OPINION_AUTHORS = [
        'gorus' => [
            'irfan-donat',
            'sefer-yuksel',
            'ayse-karagoz',
            'mehmet-demir',
            'zeynep-arslan',
            'kerem-ozturk',
            // Tarım Analiz köşesi (referans: İrfan Donat'ın tarım temalı yazıları)
            'irfan-donat',
            'irfan-donat',
            'irfan-donat',
        ],
    ];

    public function run(): void
    {
        $categories = Category::whereIn('slug', array_keys(self::TITLES))->get()->keyBy('slug');
        $authors = Author::whereIn('slug', array_merge(...array_values(self::OPINION_AUTHORS)))->get()->keyBy('slug');

        $count = 0;

        foreach (self::TITLES as $slug => $titles) {
            $category = $categories->get($slug);

            if (! $category) {
                continue;
            }

            foreach ($titles as $index => $title) {
                $this->upsertNews($category, $slug, $authors, $index, $title, 3791000 + $count, $count);

                $count++;
            }
        }

        $this->seedExtraTitles($authors, $count);
    }

    /**
     * Yalnızca EXTRA_TITLES içeriğini tohumlar; TITLES kayıtlarına dokunmaz.
     *
     * Mevcut veritabanına yeni içerik eklerken `db:seed --class=NewsSeeder`
     * çalıştırmak TITLES'taki tüm satırların published_at/views değerlerini
     * yeniden yazar. Bu metot yalnızca ek başlıkları kapsar.
     */
    public function seedExtraTitles($authors = null, int $sequence = 0): void
    {
        $authors ??= collect();
        $categories = Category::whereIn('slug', array_keys(self::EXTRA_TITLES))->get()->keyBy('slug');

        $extra = 0;

        foreach (self::EXTRA_TITLES as $slug => $titles) {
            $category = $categories->get($slug);

            if (! $category) {
                continue;
            }

            foreach ($titles as $index => $title) {
                $this->upsertNews($category, $slug, $authors, $index, $title, self::EXTRA_ID_BASE + $extra, $sequence + $extra);

                $extra++;
            }
        }
    }

    /**
     * Tek bir haber satırını idempotent olarak yazar (updateOrCreate: news_id).
     *
     * @param  \Illuminate\Support\Collection  $authors
     */
    private function upsertNews(Category $category, string $slug, $authors, int $index, string $title, int $newsId, int $sequence): void
    {
        $content = $this->buildContent($category->name, $title);
        $publishedAt = now()->subHours((92 - $sequence) * 2);

        News::updateOrCreate(
            ['news_id' => $newsId],
            [
                'category_id' => $category->id,
                'author_id' => ($slug === 'gorus') ? $authors->get(self::OPINION_AUTHORS['gorus'][$index])?->id : null,
                'title' => $title,
                'slug' => Str::slug($title),
                'spot' => $content['spot'],
                'body' => $content['body'],
                'image' => $this->placeholderFor($slug),
                'image_og' => '/img/og-default.png',
                'source' => 'Bloomberg HT',
                'tags' => $this->buildTags($title),
                'status' => 'published',
                'is_breaking' => false,
                'is_featured' => in_array($slug, ['haberler', 'piyasalar', 'enerji'], true) && $index < 2,
                'views' => fake()->numberBetween(1000, 500000),
                'editor_id' => 1,
                'published_at' => $publishedAt,
                'modified_at' => $publishedAt->copy()->addMinutes(15),
            ]
        );
    }

    /**
     * Başlık + kategori bağlamında anlamlı demo gövde ve spot üretir.
     *
     * @return array{spot: string, body: string}
     */
    private function buildContent(string $categoryName, string $title): array
    {
        $spot = implode("\n", [
            '- Piyasa katılımcıları, "'.$title.'" başlığını yakından takip ediyor.',
            '- Uzmanlar konuya ilişkin değerlendirmelerini paylaştı.',
        ]);

        $paragraphs = [
            sprintf('Bloomberg HT analizi: %s konusu bu hafta piyasaların en çok konuşulan başlıkları arasında yer alıyor. Gelişmeler hem yurt içi hem de küresel ölçekte yatırımcı kararlarını etkilemeye devam ediyor.', $title),
            sprintf('Konuya ilişkin değerlendirmelerde bulunan uzmanlar, %s kategorisindeki gelişmelerin önümüzdeki dönemde daha da önem kazanacağını belirtiyor. Özellikle veri akışı ve merkez bankası politikaları belirleyici olacak.', $categoryName),
            'Piyasa verilerine göre ilgili varlıklarda işlem hacminin gün içinde artış gösterdiği, yatırımcıların fiyat hareketlerine göre yeni pozisyonlar almaya başladığı görülüyor. Uzmanlar kısa vadede dalgalanmanın sürebileceği uyarısında bulunuyor.',
            'Analistler, gelişmelerin şirket bilançolarına ve büyüme görünümüne yansımalarını değerlendiriyor. Piyasa beklentileri, önümüzdeki hafta açıklanacak verilerle birlikte yeniden şekillenecek.',
            'Bloomberg HT olarak konuya ilişkin tüm gelişmeleri aktarmaya devam edeceğiz. Güncel fiyatlamalar ve uzman görüşleri için piyasalar sayfamızı takip edebilirsiniz.',
        ];

        return [
            'spot' => $spot,
            'body' => implode("\n\n", $paragraphs),
        ];
    }

    /**
     * Kategori için boş durumda yer tutucu görsel seçer.
     */
    private function placeholderFor(string $categorySlug): string
    {
        $map = [
            'piyasalar' => 1,
            'doviz' => 2,
            'enerji' => 3,
            'ekonomik-veriler-ve-gundem' => 4,
            'finansal-teknoloji' => 5,
            'tarim' => 6,
            'kripto' => 7,
            'emtia-enerji' => 2,
            'kuresel-piyasalar' => 2,
        ];

        $n = $map[$categorySlug] ?? (mb_ord($categorySlug[0] ?? 'a') % 8 + 1);

        return '/img/placeholders/placeholder-'.$n.'.svg';
    }

    /**
     * Başlıktan ilgili etiketler üretir (spec §4 inline keyword bağlantıları).
     *
     * @return array<int, string>
     */
    private function buildTags(string $title): array
    {
        $tags = [];

        $map = [
            'Dolar' => ['dolar', 'kur'],
            'TL' => ['tl', 'lira'],
            'Fed' => ['fed', 'merkez-bankasi'],
            'TCMB' => ['tcmb', 'merkez-bankasi'],
            'Altın' => ['altin'],
            'BIST' => ['bist',
                'borsa', ],
        ];

        foreach ($map as $needle => $candidates) {
            if (str_contains($title, $needle)) {
                $tags = array_merge($tags, $candidates);
            }
        }

        foreach (['Enflasyon', 'Faiz', 'İhracat', 'Petrol', 'Bitcoin'] as $word) {
            if (str_contains($title, $word)) {
                $tags[] = Str::slug($word);
            }
        }

        return array_values(array_unique($tags)) ?: ['ekonomi'];
    }
}
