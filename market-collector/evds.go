package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io"
	"log"
	"net/http"
	"net/url"
	"os/exec"
	"strconv"
	"strings"
	"time"
)

// EVDS (TCMB Elektronik Veri Dağıtım Sistemi) — ücretsiz resmi zaman serisi.
//
// Uç: https://evds3.tcmb.gov.tr/igmevdsms-dis/series=...&type=json  (header: key)
//
// Not: evds3 host'undaki WAF (Imperva) Go istemcisinin TLS parmak izine SPA
// sayfası döndürür; curl istemcisine JSON verir. Bu yüzden istekleri önce
// sistemdeki `curl` ile yapar, yoksa Go istemcisine düşeriz.

// evdsSeriesByCode — market_instruments kodu → EVDS seri kodu.
var evdsSeriesByCode = map[string]string{
	"REPO_GECELIK":  "TP.AOFOBAP",   // BİST O/N repo ağırlıklı ortalama faizi
	"REPO_HAFTALIK": "TP.PY.P02.1H", // TCMB 1 haftalık repo (politika faizi) SATIŞ
}

type evdsItem map[string]any

type evdsResp struct {
	Items []evdsItem `json:"items"`
}

func evdsQueryURL(start, end time.Time) string {
	series := make([]string, 0, len(evdsSeriesByCode))
	for _, code := range evdsSeriesByCode {
		series = append(series, code)
	}
	qs := url.Values{}
	qs.Set("series", strings.Join(series, "-"))
	qs.Set("startDate", start.Format("02-01-2006"))
	qs.Set("endDate", end.Format("02-01-2006"))
	qs.Set("type", "json")
	return "https://evds3.tcmb.gov.tr/igmevdsms-dis?" + qs.Encode()
}

func evdsFetch(key string) (map[string]float64, error) {
	// endDate = dün: bugünkü bülten henüz yayımlanmamış olabilir, EVDS bu
	// durumda (hem de hız limitinde) SPA/HTML döndürüyor.
	today := time.Now().In(istanbul)
	end := today.AddDate(0, 0, -1)
	start := end.AddDate(0, 0, -14)
	u := evdsQueryURL(start, end)

	body, err := evdsHTTPGet(u, key)
	if err != nil {
		return nil, err
	}

	var parsed evdsResp
	if err := json.Unmarshal(body, &parsed); err != nil {
		if debugEnabled() {
			log.Printf("evds json parse error: %v (ilk 120 bayt: %q)", err, string(body[:min(len(body), 120)]))
		}
		return nil, err
	}

	series := make([]string, 0, len(evdsSeriesByCode))
	for _, code := range evdsSeriesByCode {
		series = append(series, code)
	}

	out := map[string]float64{}
	for _, code := range series {
		keyJSON := strings.ReplaceAll(code, ".", "_")
		var found float64
		for _, it := range parsed.Items {
			v, ok := it[keyJSON]
			if !ok {
				continue
			}
			s, ok := v.(string)
			if !ok || strings.TrimSpace(s) == "" {
				continue
			}
			f, err := strconv.ParseFloat(strings.TrimSpace(s), 64)
			if err != nil {
				continue
			}
			found = f
		}
		if found != 0 {
			out[code] = found
		}
	}
	return out, nil
}

// evdsHTTPGet — önce curl, yoksa Go istemcisi.
func evdsHTTPGet(u, key string) ([]byte, error) {
	if path, err := exec.LookPath("curl"); err == nil && path != "" {
		cmd := exec.Command("curl", "-s", "--max-time", "20",
			"-H", "key: "+key, "-H", "Accept: application/json", "--http1.1", u)
		if debugEnabled() {
			log.Printf("evds curl: %s", cmd.String())
		}
		out, err := cmd.Output()
		if err != nil {
			return nil, fmt.Errorf("curl: %w", err)
		}
		if debugEnabled() {
			log.Printf("evds curl yanıt ilk 100 bayt: %q", string(out[:min(len(out), 100)]))
		}
		return out, nil
	}

	req, err := http.NewRequest("GET", u, nil)
	if err != nil {
		return nil, err
	}
	req.Header.Set("key", key)
	req.Header.Set("Accept", "application/json")
	client := &http.Client{Timeout: 20 * time.Second}
	resp, err := client.Do(req)
	if err != nil {
		return nil, err
	}
	defer resp.Body.Close()
	if resp.StatusCode != 200 {
		io.Copy(io.Discard, resp.Body)
		return nil, fmt.Errorf("HTTP %d", resp.StatusCode)
	}
	var buf bytes.Buffer
	_, err = io.Copy(&buf, resp.Body)
	return buf.Bytes(), err
}

// fetchEvdsSnapshots — repo grubu için EVDS snapshotları üretir.
func fetchEvdsSnapshots(insts []Instrument, key string) []Snapshot {
	rates, err := evdsFetch(key)
	if err != nil {
		if debugEnabled() {
			log.Printf("evds fetch error: %v", err)
		}
		return nil
	}
	if len(rates) == 0 {
		return nil
	}
	out := []Snapshot{}
	for _, it := range insts {
		if it.Group != "repo" {
			continue
		}
		code, ok := evdsSeriesByCode[it.Code]
		if !ok {
			continue
		}
		rate, ok := rates[code]
		if !ok {
			continue
		}
		out = append(out, Snapshot{
			InstrumentID: it.ID,
			Price:        rate, // faiz oranı (%) — birim değer olarak yazılır
			Source:       "evds",
			RawSymbol:    code,
		})
	}
	return out
}
