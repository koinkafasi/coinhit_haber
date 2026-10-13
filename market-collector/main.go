// market-collector — BloombergHT klonu için ücretsiz gerçek piyasa verisi toplayıcı.
//
// Kaynaklar:
//   - TCMB        (döviz/TL çaprazları, resmi, ücretsiz)
//   - Yahoo Finance (BIST hisse/endeks, pariteler, emtia, dünya endeksleri; 15dk gecikmeli, ücretsiz)
//   - goldprice.dev (XAU/USD spot, ~30-60sn gecikmeli, ücretsiz, opsiyonel anahtar)
//
// Veriler periyodik (cron/scheduler) ile PostgreSQL `market_snapshots`
// tablosuna yazılır; Laravel MarketDataService taze satırları okuyarak
// simülatörü gerçek veriyle besler.

package main

import (
	"database/sql"
	"fmt"
	"log"
	"os"
	"sync"
	"time"
)

func main() {
	db, err := sql.Open("postgres", dsnFromEnv())
	if err != nil {
		log.Fatalf("db open: %v", err)
	}
	defer db.Close()
	db.SetMaxOpenConns(12)

	insts, err := loadInstruments(db)
	if err != nil {
		log.Fatalf("load instruments: %v", err)
	}
	log.Printf("katalog: %d aktif enstrüman", len(insts))

	// 1) TCMB — döviz (TL çaprazları)
	today, prev, err := fetchTcmbRates()
	if err != nil {
		log.Printf("TCMB hata (döviz simülatörde kalır): %v", err)
	} else {
		usdt := today["USD"].Selling
		if usdt == 0 {
			usdt = today["USD"].Buying
		}
		tcmbUsdtry = usdt
		log.Printf("TCMB: %d kur okundu, USD/TRY=%.4f", len(today), usdt)

		snap := make([]Snapshot, 0, 16)
		for _, it := range insts {
			if it.Group != "currency" {
				continue
			}
			c := it.Code[:len(it.Code)-3] // USDTRY -> USD ; JPYTRY -> JPY (birim 100)
			r, ok := today[c]
			if !ok {
				continue
			}
			price := r.Selling
			if price == 0 {
				price = r.Buying
			}
			if price == 0 {
				continue
			}
			snap = append(snap, Snapshot{
				InstrumentID: it.ID,
				Price:        price,
				ChangePct:    pctChange(price, prev[c].Selling),
				Source:       "tcmb",
			})
		}
		if len(snap) > 0 {
			if err := writeSnapshots(db, snap); err != nil {
				log.Printf("TCMB yazım hatası: %v", err)
			} else {
				log.Printf("TCMB: %d enstrüman yazıldı", len(snap))
			}
		}
	}

	// 2) Yahoo — paralel toplayıcı
	yahooInsts := make([]Instrument, 0)
	for _, it := range insts {
		if yahooSymbol(it) != "" {
			yahooInsts = append(yahooInsts, it)
		} else if it.Group == "gold" && (it.Code == "GAUTRY" || it.Code == "GRAM") {
			yahooInsts = append(yahooInsts, it) // TL türevleri kaynak için gerekli
		} else if it.Group == "precious_metal" && it.Code == "GUMUS_GRAM" {
			yahooInsts = append(yahooInsts, it)
		}
	}
	log.Printf("Yahoo hedef: %d enstrüman", len(yahooInsts))

	yahooSnaps := fetchYahooParallel(yahooInsts, 6)
	if len(yahooSnaps) > 0 {
		if err := writeSnapshots(db, yahooSnaps); err != nil {
			log.Printf("Yahoo yazım hatası: %v", err)
		} else {
			log.Printf("Yahoo: %d enstrüman yazıldı", len(yahooSnaps))
		}
	}

	// 3) Altın/gümüş TL türevleri (GC=F + TCMB USD/TRY)
	derived := deriveGoldTl(yahooInsts, yahooSnaps, tcmbUsdtry)
	if len(derived) > 0 {
		if err := writeSnapshots(db, derived); err != nil {
			log.Printf("altın TL yazım hatası: %v", err)
		} else {
			log.Printf("Altın TL: %d enstrüman yazıldı", len(derived))
		}
	}

	// 3b) Madeni altın / takı türetmeleri (darphane standardı, GC=F bazlı)
	coins := deriveGoldCoins(insts, yahooSnaps, tcmbUsdtry)
	if len(coins) > 0 {
		if err := writeSnapshots(db, coins); err != nil {
			log.Printf("madeni altın yazım hatası: %v", err)
		} else {
			log.Printf("Madeni altın: %d enstrüman yazıldı", len(coins))
		}
	}

	// 4) goldprice.dev — opsiyonel ikinci XAU/XAG kaynağı (anahtar verilirse)
	if key := os.Getenv("GOLDPRICE_API_KEY"); key != "" {
		goldSnaps := fetchGoldPrice(insts, tcmbUsdtry, key)
		if len(goldSnaps) > 0 {
			if err := writeSnapshots(db, goldSnaps); err != nil {
				log.Printf("goldprice yazım hatası: %v", err)
			} else {
				log.Printf("goldprice: %d enstrüman yazıldı", len(goldSnaps))
			}
		}
	}

	// 5) EVDS (TCMB) — repo faizleri (ücretsiz resmi seri)
	if key := os.Getenv("EVDS_API_KEY"); key != "" {
		evdSnaps := fetchEvdsSnapshots(insts, key)
		if len(evdSnaps) > 0 {
			if err := writeSnapshots(db, evdSnaps); err != nil {
				log.Printf("EVDS yazım hatası: %v", err)
			} else {
				log.Printf("EVDS: %d enstrüman yazıldı", len(evdSnaps))
			}
		} else {
			log.Print("EVDS: veri alınamadı (anahtar/seri kontrolü)")
		}
	}

	prune(db)
	log.Println("tamamlandı")
}

// Snapshot — market_snapshots satırı.
type Snapshot struct {
	InstrumentID int64
	Price        float64
	ChangePct    *float64 // 24s değişim % (yoksa nil)
	High         *float64
	Low          *float64
	Volume       *float64
	Source       string
	RawSymbol    string // kaynak sembol (tl türevleri/izleme için)
}

// Instrument — market_instruments satırının ilgili alanları.
type Instrument struct {
	ID       int64
	Group    string
	Code     string
	Currency string
}

func dsnFromEnv() string {
	if d := os.Getenv("COLLECTOR_DB_DSN"); d != "" {
		return d
	}
	h := envOr("DB_HOST", "127.0.0.1")
	port := envOr("DB_PORT", "5432")
	user := envOr("DB_USERNAME", "arastam")
	pass := envOr("DB_PASSWORD", "")
	name := envOr("DB_DATABASE", "coinhit")
	sslmode := envOr("DB_SSLMODE", "disable")
	return fmt.Sprintf("postgres://%s:%s@%s:%s/%s?sslmode=%s", user, pass, h, port, name, sslmode)
}

// istanbul — uygulama zaman dilimi (Laravel APP_TIMEZONE=Europe/Istanbul ile uyumlu).
var istanbul = func() *time.Location {
	l, err := time.LoadLocation("Europe/Istanbul")
	if err != nil {
		l = time.UTC
	}
	return l
}()

// nowApp — uygulama yerel saatiyle "duvar saati" döner (naive timestamp, Laravel ile aynı kural).
func nowApp() time.Time { return time.Now().In(istanbul) }

func envOr(k, def string) string {
	if v := os.Getenv(k); v != "" {
		return v
	}
	return def
}

// pctChange — (a-b)/b*100 ; b==0 ise nil.
func pctChange(a, b float64) *float64 {
	if b == 0 {
		return nil
	}
	v := (a - b) / b * 100
	return &v
}

func loadInstruments(db *sql.DB) ([]Instrument, error) {
	rows, err := db.Query(`
		SELECT id, "group", code, currency
		FROM market_instruments
		WHERE is_active = true
		ORDER BY "group", sort_order, code`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	out := []Instrument{}
	for rows.Next() {
		var it Instrument
		if err := rows.Scan(&it.ID, &it.Group, &it.Code, &it.Currency); err != nil {
			return nil, err
		}
		out = append(out, it)
	}
	return out, rows.Err()
}

func parallel[T any](items []T, workers int, fn func(T)) {
	if len(items) == 0 {
		return
	}
	if workers < 1 {
		workers = 1
	}
	ch := make(chan T)
	var wg sync.WaitGroup
	for i := 0; i < workers; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()
			for it := range ch {
				fn(it)
			}
		}()
	}
	for _, it := range items {
		ch <- it
	}
	close(ch)
	wg.Wait()
}
