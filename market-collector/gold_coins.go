package main

// Madeni altın / takı türetmeleri — gram has altın fiyatından (>GC=F × USD/TRY)
// darphane standart ağırlık × ayar (saflık) ile hesaplanır. Serbest piyasa
// fiyatı bu değere çok yakındır (küçük işçilik/kar payı premium olarak eklenir).

// GoldCoin — kod → ağırlık (g), saflık (milyem), premium çarpanı.
type GoldCoin struct {
	Weight   float64 // gram
	Fineness float64 // 0.9166 = 22 ayar, 0.585 = 14 ayar, 0.995 = has
	Premium  float64 // Kapalıçarşı makas/işçilik yaklaşımı (>1)
}

var goldCoins = map[string]GoldCoin{
	"CUMHUR":   {7.216, 0.9166, 1.015},
	"ATA":      {7.216, 0.9166, 1.015},
	"TAM":      {7.216, 0.9166, 1.010},
	"YARIM":    {3.608, 0.9166, 1.010},
	"IKIBUCUK": {18.04, 0.9166, 1.010},
	"BESLI":    {36.08, 0.9166, 1.010},
	"CEYREK":   {1.754, 0.9166, 1.010},
	"GREMSE":   {7.020, 0.9166, 1.000}, // yaklaşık tek adet
	"ZIYNET":   {1.754, 0.9166, 1.020},
	"HAS":      {1.000, 0.9950, 1.000},
	"BILEZIK14": {1.000, 0.5850, 1.040},
	"BILEZIK22": {1.000, 0.9166, 1.020},
	"KULCE_USD": {1.000, 1.0000, 1.000}, // külçe = has gram
	"KULCE_EUR": {1.000, 1.0000, 1.000},
}

// deriveGoldCoins — GC=F ons altın bazlı tüm madeni altın/takıları üretir.
func deriveGoldCoins(insts []Instrument, snaps []Snapshot, usdtry float64) []Snapshot {
	if usdtry == 0 {
		return nil
	}
	xau := 0.0
	for _, s := range snaps {
		if s.RawSymbol == "GC=F" && s.Price > 0 {
			xau = s.Price
		}
	}
	if xau <= 0 {
		return nil
	}
	// has (24 ayar) gram altın, TL
	pureGramTl := xau / 31.1035 * usdtry

	out := []Snapshot{}
	for _, it := range insts {
		if it.Group != "gold" {
			continue
		}
		coin, ok := goldCoins[it.Code]
		if !ok {
			continue
		}
		price := coin.Weight * coin.Fineness * pureGramTl * coin.Premium
		out = append(out, Snapshot{
			InstrumentID: it.ID,
			Price:        price,
			Source:       "yahoo",
			RawSymbol:    "GC=F",
		})
	}
	return out
}
