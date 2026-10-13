package main

import (
	"encoding/json"
	"fmt"
	"io"
	"net/http"
	"net/url"
	"time"
)

// goldprice.dev — XAU/XAG spot (ücretsiz, ~30-60sn; anahtar opsiyonel).
// Docs: https://api.goldprice.dev/v1/prices?symbol=XAU-USD-SPOT

type goldPriceResp struct {
	Symbols []struct {
		Symbol string  `json:"symbol"`
		Price  float64 `json:"price"`
	} `json:"symbols"`
}

func fetchGoldPrice(insts []Instrument, usdtry float64, key string) []Snapshot {
	q := url.Values{}
	q.Set("symbol", "XAU-USD-SPOT,XAG-USD-SPOT")
	u := "https://api.goldprice.dev/v1/prices?" + q.Encode()

	req, err := http.NewRequest("GET", u, nil)
	if err != nil {
		return nil
	}
	req.Header.Set("Authorization", "Bearer "+key)
	client := &http.Client{Timeout: 12 * time.Second}
	resp, err := client.Do(req)
	if err != nil {
		return nil
	}
	defer resp.Body.Close()
	if resp.StatusCode != 200 {
		return nil
	}
	body, err := io.ReadAll(io.LimitReader(resp.Body, 1<<20))
	if err != nil {
		return nil
	}

	var parsed goldPriceResp
	if err := json.Unmarshal(body, &parsed); err != nil {
		return nil
	}

	xau, xag := 0.0, 0.0
	for _, s := range parsed.Symbols {
		switch s.Symbol {
		case "XAU-USD-SPOT":
			xau = s.Price
		case "XAG-USD-SPOT":
			xag = s.Price
		}
	}
	if xau <= 0 {
		return nil
	}

	out := []Snapshot{}
	const oz = 31.1035
	for _, it := range insts {
		switch it.Group + ":" + it.Code {
		case "gold:GAU":
			out = append(out, Snapshot{InstrumentID: it.ID, Price: xau, Source: "goldprice", RawSymbol: "XAU-USD-SPOT"})
		case "gold:GAUTRY":
			out = append(out, Snapshot{InstrumentID: it.ID, Price: xau * usdtry, Source: "goldprice", RawSymbol: "XAU-USD-SPOT"})
		case "gold:GRAM":
			out = append(out, Snapshot{InstrumentID: it.ID, Price: xau / oz * usdtry, Source: "goldprice", RawSymbol: "XAU-USD-SPOT"})
		case "precious_metal:GUMUS_ONS":
			out = append(out, Snapshot{InstrumentID: it.ID, Price: xag, Source: "goldprice", RawSymbol: "XAG-USD-SPOT"})
		case "precious_metal:GUMUS_GRAM":
			out = append(out, Snapshot{InstrumentID: it.ID, Price: xag / oz * usdtry, Source: "goldprice", RawSymbol: "XAG-USD-SPOT"})
		}
	}
	return out
}

var _ = fmt.Sprintf // import guard (kullanım yoksa derleme uyarısı yok)
