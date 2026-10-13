package main

import (
	"encoding/xml"
	"fmt"
	"io"
	"net/http"
	"strconv"
	"strings"
	"time"
)

// TCMB günlük kur bülteni (resmi, ücretsiz).
//
// Bugün:   https://www.tcmb.gov.tr/kurlar/today.xml
// Arşiv:   https://www.tcmb.gov.tr/kurlar/YYYYMM/DDMMYYYY.xml

const tcmbTodayURL = "https://www.tcmb.gov.tr/kurlar/today.xml"

// tcmbUsdtry — TCMB'den okunan USD/TRY satış kuru (altın TL türevleri için).
var tcmbUsdtry float64

type tcmbRates struct {
	Date string `xml:"Tarih,attr"`
	Cur  []tcmbCurrency `xml:"Currency"`
}

type tcmbCurrency struct {
	Code         string `xml:"Kod,attr"`
	Unit         string `xml:"Unit"`
	ForexBuying  string `xml:"ForexBuying"`
	ForexSelling string `xml:"ForexSelling"`
}

// TcmbRate — tek bir para birimi için günün kurları.
type TcmbRate struct {
	Code    string
	Buying  float64
	Selling float64
}

func parseTcmb(body []byte) (map[string]TcmbRate, error) {
	var doc struct {
		Currency []struct {
			Code         string `xml:"Kod,attr"`
			Unit         string `xml:"Unit"`
			ForexBuying  string `xml:"ForexBuying"`
			ForexSelling string `xml:"ForexSelling"`
		} `xml:"Currency"`
	}
	if err := xml.Unmarshal(body, &doc); err != nil {
		return nil, err
	}
	out := make(map[string]TcmbRate)
	for _, c := range doc.Currency {
		b, _ := strconv.ParseFloat(strings.ReplaceAll(c.ForexBuying, ",", "."), 64)
		s, _ := strconv.ParseFloat(strings.ReplaceAll(c.ForexSelling, ",", "."), 64)
		out[c.Code] = TcmbRate{Code: c.Code, Buying: b, Selling: s}
	}
	return out, nil
}

func tcmbGet(url string, timeout time.Duration) ([]byte, error) {
	client := &http.Client{Timeout: timeout}
	resp, err := client.Get(url)
	if err != nil {
		return nil, err
	}
	defer resp.Body.Close()
	if resp.StatusCode != 200 {
		return nil, fmt.Errorf("HTTP %d", resp.StatusCode)
	}
	return io.ReadAll(io.LimitReader(resp.Body, 2<<20))
}

// fetchTcmbRates — bugünün ve bir önceki iş gününün kurlarını döner.
func fetchTcmbRates() (today map[string]TcmbRate, prev map[string]TcmbRate, err error) {
	body, err := tcmbGet(tcmbTodayURL, 10*time.Second)
	if err != nil {
		return nil, nil, err
	}
	today, err = parseTcmb(body)
	if err != nil {
		return nil, nil, err
	}

	// Bir önceki iş günü: hafta sonu/tatil olduğu için 7 gün geriye bak.
	prev = map[string]TcmbRate{}
	for i := 1; i <= 7; i++ {
		d := time.Now().AddDate(0, 0, -i)
		if d.Weekday() == time.Saturday || d.Weekday() == time.Sunday {
			continue
		}
		u := fmt.Sprintf("https://www.tcmb.gov.tr/kurlar/%s/%s.xml",
			d.Format("200601"), d.Format("02012006"))
		b, err := tcmbGet(u, 10*time.Second)
		if err != nil {
			continue
		}
		p, err := parseTcmb(b)
		if err == nil && len(p) > 0 {
			prev = p
			break
		}
	}
	return today, prev, nil
}
