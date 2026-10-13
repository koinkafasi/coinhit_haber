package main

import (
	"encoding/json"
	"fmt"
	"io"
	"log"
	"net/http"
	"strings"
	"sync"
	"time"
)

// debugEnabled — COLLECTOR_DEBUG=1 ile kaynak hatalarını loglar.
func debugEnabled() bool { return strings.EqualFold(envOr("COLLECTOR_DEBUG", "0"), "1") }

// Yahoo Finance chart API — anahtar gerektirmez, 15dk gecikmeli piyasa verisi.

const yahooChartURL = "https://query1.finance.yahoo.com/v8/finance/chart/%s?interval=1d&range=5d"

type yahooChartResp struct {
	Chart struct {
		Result []struct {
			Meta struct {
				RegularMarketPrice float64  `json:"regularMarketPrice"`
				ChartPreviousClose float64  `json:"chartPreviousClose"`
				PreviousClose      *float64 `json:"previousClose"`
				DayHigh            *float64 `json:"regularMarketDayHigh"`
				DayLow             *float64 `json:"regularMarketDayLow"`
				Volume             *int64   `json:"regularMarketVolume"`
			} `json:"meta"`
			Indicators struct {
				Quote []struct {
					Volume []*int64 `json:"volume"`
				} `json:"quote"`
			} `json:"indicators"`
		} `json:"result"`
	} `json:"chart"`
}

// quoteVolume — meta yoksa indicators.quote[0].volume son değerine düşer.
func (r *yahooChartResp) quoteVolume() *int64 {
	res := r.Chart.Result
	if len(res) == 0 || res[0].Meta.Volume != nil {
		if len(res) == 0 {
			return nil
		}
		return res[0].Meta.Volume
	}
	if len(res[0].Indicators.Quote) > 0 {
		vols := res[0].Indicators.Quote[0].Volume
		for i := len(vols) - 1; i >= 0; i-- {
			if vols[i] != nil {
				return vols[i]
			}
		}
	}
	return nil
}

// yahooSymbol — enstrümanı Yahoo Finance sembolüne eşler. Boşsa simülatöre kalır.
func yahooSymbol(it Instrument) string {
	g := it.Group
	code := strings.ToUpper(it.Code)
	switch g {
	case "bist_index", "stock":
		// Yahoo'da olmayan suni/BIST dışı kodlar (404 üretir) için filtre.
		switch code {
		case "MSCI_TURKEY", "MSCI_EURO", "MSCI_EMERGING", "BIST_ADANA", "BIST_ANKARA",
			"BIST_ISTANBUL", "BIST_IZMIR", "BIST_ANTALYA", "BIST_BALIKESIR", "BIST_BURSA",
			"XAUTO", "XKOB", "XPDSU", "XTCRT_DIGITAL":
			return ""
		}
		return code + ".IS"
	case "parity":
		if code == "DXY" {
			return "DX-Y.NYB"
		}
		return code + "=X"
	case "emerging_fx":
		return code + "=X" // USDMXN=X ...
	case "world_index":
		return map[string]string{
			"DOW": "^DJI", "SP500": "^GSPC", "NASDAQ": "^NDX", "DAX": "^GDAXI",
			"FTSE100": "^FTSE", "CAC40": "^FCHI", "SHANGHAI": "000001.SS",
			"NIKKEI225": "^N225", "HANGSENG": "^HSI", "KOSPI": "^KS11",
			"SPFUT": "ES=F", "NASDAQFUT": "NQ=F",
		}[code]
	case "gold":
		switch code {
		case "GAU":
			return "GC=F"
		}
		return "" // GAUTRY/GRAM TL türevleri cmderived içinde hesaplanır
	case "precious_metal":
		switch code {
		case "GUMUS_ONS":
			return "SI=F"
		case "PLATIN":
			return "PL=F"
		case "PALADYUM":
			return "PA=F"
		}
		return ""
	case "energy":
		switch code {
		case "WTI":
			return "CL=F"
		case "BRENT":
			return "BZ=F"
		}
		return ""
	case "agriculture":
		switch code {
		case "PAMUK":
			return "CT=F"
		case "KAHVE":
			return "KC=F"
		case "KAKAO":
			return "CC=F"
		}
		return ""
	case "industry":
		switch code {
		case "BAKIR":
			return "HG=F"
		}
		return ""
	case "alternative":
		switch code {
		case "VIX":
			return "^VIX"
		}
		return ""
	case "intl_bond":
		switch code {
		case "US10Y":
			return "^TNX"
		case "US5Y":
			return "^FVX"
		case "US30Y":
			return "^TYX"
		}
		return ""
	}
	return ""
}

// rawQuote — tek sembollü ham Yahoo yanıtı.
type rawQuote struct {
	Symbol string // kaynak sembol (tl türevleri için)
	Meta   struct {
		RegularMarketPrice float64  `json:"regularMarketPrice"`
		ChartPreviousClose float64  `json:"chartPreviousClose"`
		PreviousClose      *float64 `json:"previousClose"`
		DayHigh            *float64 `json:"regularMarketDayHigh"`
		DayLow             *float64 `json:"regularMarketDayLow"`
		Volume             *int64   `json:"regularMarketVolume"`
	} `json:"meta"`
	Volume *int64 // meta yoksa indicators'dan
}

func fetchYahooRaw(symbol string, timeout time.Duration) (*rawQuote, error) {
	url := fmt.Sprintf(yahooChartURL, symbol)
	client := &http.Client{Timeout: timeout}
	req, err := http.NewRequest("GET", url, nil)
	if err != nil {
		return nil, err
	}
	req.Header.Set("User-Agent", "Mozilla/5.0 (X11; Linux x86_64)")

	resp, err := client.Do(req)
	if err != nil {
		return nil, err
	}
	defer resp.Body.Close()
	if resp.StatusCode != 200 {
		return nil, fmt.Errorf("HTTP %d", resp.StatusCode)
	}
	body, err := io.ReadAll(io.LimitReader(resp.Body, 4<<20))
	if err != nil {
		return nil, err
	}

	var parsed yahooChartResp
	if err := json.Unmarshal(body, &parsed); err != nil {
		return nil, err
	}
	if len(parsed.Chart.Result) == 0 {
		return nil, fmt.Errorf("boş yanıt")
	}
	m := parsed.Chart.Result[0].Meta
	if m.RegularMarketPrice == 0 {
		return nil, fmt.Errorf("fiyat yok")
	}
	return &rawQuote{Symbol: symbol, Meta: m, Volume: parsed.quoteVolume()}, nil
}

// snapFromRaw — raw Yahoo yanıtını Snapshot'a çevirir.
func snapFromRaw(q *rawQuote, it Instrument) Snapshot {
	prev := q.Meta.ChartPreviousClose
	if q.Meta.PreviousClose != nil && *q.Meta.PreviousClose != 0 {
		prev = *q.Meta.PreviousClose
	}
	var pct *float64
	if prev != 0 {
		v := (q.Meta.RegularMarketPrice - prev) / prev * 100
		pct = &v
	}
	var high, low *float64
	if q.Meta.DayHigh != nil {
		h := *q.Meta.DayHigh
		high = &h
	}
	if q.Meta.DayLow != nil {
		l := *q.Meta.DayLow
		low = &l
	}
	var vol *float64
	if q.Volume != nil {
		v := float64(*q.Volume)
		vol = &v
	}
	return Snapshot{
		InstrumentID: it.ID,
		Price:        q.Meta.RegularMarketPrice,
		ChangePct:    pct,
		High:         high,
		Low:          low,
		Volume:       vol,
		Source:       "yahoo",
		RawSymbol:    q.Symbol,
	}
}

func fetchYahooParallel(insts []Instrument, workers int) []Snapshot {
	out := make([]Snapshot, 0, len(insts))
	var mu sync.Mutex
	parallel(insts, workers, func(it Instrument) {
		sym := yahooSymbol(it)
		if sym == "" {
			return
		}
		q, err := fetchYahooRaw(sym, 12*time.Second)
		if err != nil {
			if debugEnabled() {
				log.Printf("yahoo hata %s (%s): %v", it.Code, sym, err)
			}
			return
		}
		snap := snapFromRaw(q, it)
		mu.Lock()
		out = append(out, snap)
		mu.Unlock()
	})
	return out
}

// deriveGoldTl — GC=F (ons altın) ve SI=F (ons gümüş) baz alarak TL
// türevlerini hesaplar: GRAM = XAU/31.1035*USDTRY, GAUTRY = XAU*USDTRY,
// GUMUS_GRAM = XAG/31.1035*USDTRY. Diğer kaynaklardan değil, sadece
// hedef TL enstrümanlarından snapshot üretir.
func deriveGoldTl(insts []Instrument, snaps []Snapshot, usdtry float64) []Snapshot {
	if usdtry == 0 {
		return nil
	}
	xau, xag := 0.0, 0.0
	for _, s := range snaps {
		switch s.RawSymbol {
		case "GC=F":
			xau = s.Price
		case "SI=F":
			xag = s.Price
		}
	}
	if xau <= 0 {
		return nil
	}
	const oz = 31.1035
	out := []Snapshot{}
	for _, it := range insts {
		if it.Group == "gold" {
			switch it.Code {
			case "GAUTRY":
				out = append(out, Snapshot{InstrumentID: it.ID, Price: xau * usdtry, Source: "yahoo", RawSymbol: "GC=F"})
			case "GRAM":
				out = append(out, Snapshot{InstrumentID: it.ID, Price: xau / oz * usdtry, Source: "yahoo", RawSymbol: "GC=F"})
			}
		}
		if it.Group == "precious_metal" && it.Code == "GUMUS_GRAM" && xag > 0 {
			out = append(out, Snapshot{InstrumentID: it.ID, Price: xag / oz * usdtry, Source: "yahoo", RawSymbol: "SI=F"})
		}
	}
	return out
}
