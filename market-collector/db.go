package main

import (
	"database/sql"
	"fmt"
	"log"

	_ "github.com/lib/pq"
)

// writeSnapshots — market_snapshots'a yeni satırlar yazar.
func writeSnapshots(db *sql.DB, snaps []Snapshot) error {
	if len(snaps) == 0 {
		return nil
	}
	tx, err := db.Begin()
	if err != nil {
		return err
	}
	defer tx.Rollback()

	stmt, err := tx.Prepare(`
		INSERT INTO market_snapshots (
			market_instrument_id, price, change_24h, high_24h, low_24h,
			volume_24h, source, captured_at, created_at, updated_at
		) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $9)`)
	if err != nil {
		return err
	}
	defer stmt.Close()

	captured := nowApp()
	for _, s := range snaps {
		if s.Price <= 0 {
			continue
		}
		if _, err := stmt.Exec(
			s.InstrumentID,
			s.Price,
			nullableFloat(s.ChangePct),
			nullableFloat(s.High),
			nullableFloat(s.Low),
			nullableFloat(s.Volume),
			s.Source,
			captured,
			captured,
		); err != nil {
			return fmt.Errorf("insert %d (%s): %w", s.InstrumentID, s.Source, err)
		}
	}
	return tx.Commit()
}

func nullableFloat(f *float64) any {
	if f == nil {
		return nil
	}
	return *f
}

// prune — 7 günden eski snapshotları temizler (tablo şişmesini önler).
func prune(db *sql.DB) {
	res, err := db.Exec(`DELETE FROM market_snapshots WHERE captured_at < now() - interval '7 days'`)
	if err != nil {
		log.Printf("prune hatası: %v", err)
		return
	}
	n, _ := res.RowsAffected()
	if n > 0 {
		log.Printf("prune: %d eski satır temizlendi", n)
	}
}
