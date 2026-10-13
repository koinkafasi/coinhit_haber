#!/usr/bin/env bash
# market-collector başlatıcı — DB_* değişkenlerini coinhit/.env'den alır ve
# binary'i çalıştırır. Cron/scheduler'dan çağrılır.
set -euo pipefail
cd "$(dirname "$0")"

ENV_FILE="$(cd .. && pwd)/.env"
if [[ -f "$ENV_FILE" ]]; then
  set -a
  while IFS='=' read -r k v; do
    [[ "$k" == DB_* || "$k" == COLLECTOR_* || "$k" == GOLDPRICE_* || "$k" == EVDS_* ]] && export "$k=$v"
  done < <(grep -E '^(DB_|COLLECTOR_|GOLDPRICE_|EVDS_)' "$ENV_FILE" || true)
  set +a
fi

exec ./market-collector
