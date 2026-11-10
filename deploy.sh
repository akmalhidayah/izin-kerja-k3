#!/usr/bin/env bash
# Deploy ke hosting cPanel: tarik kode, build aset, salin ke folder web.
#
# Pakai (dari folder aplikasi Laravel di server):
#   ./deploy.sh              tarik kode, build, salin aset
#   ./deploy.sh --ci         sama, tetapi pasang ulang dependensi npm dulu
#   ./deploy.sh --migrate    sama, plus php artisan migrate --force
#   ./deploy.sh --no-build   hanya tarik kode (perubahan PHP saja)
# Opsi boleh digabung. Ganti folder web: WEB_ROOT=/path ./deploy.sh  |  ganti PHP: PHP_BIN=/path/php ./deploy.sh

set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
WEB_ROOT="${WEB_ROOT:-$HOME/public_html}"
NODE_BIN="${NODE_BIN:-/opt/alt/alt-nodejs20/root/usr/bin}"
# PHP versi terminal (CLI). Di cPanel, "php" biasa sering berupa CGI dan tidak bisa menjalankan artisan.
PHP_BIN="${PHP_BIN:-}"

DO_CI=0; DO_MIGRATE=0; DO_BUILD=1
for arg in "$@"; do
  case "$arg" in
    --ci) DO_CI=1 ;;
    --migrate) DO_MIGRATE=1 ;;
    --no-build) DO_BUILD=0 ;;
    -h|--help) sed -n '2,10p' "$0"; exit 0 ;;
    *) echo "Opsi tidak dikenal: $arg (lihat --help)"; exit 1 ;;
  esac
done

step() { printf '\n==> %s\n' "$1"; }

find_php() {
  local c
  for c in "$PHP_BIN" /opt/cpanel/ea-php84/root/usr/bin/php /opt/cpanel/ea-php83/root/usr/bin/php \
           /opt/cpanel/ea-php82/root/usr/bin/php php; do
    [ -n "$c" ] || continue
    command -v "$c" >/dev/null 2>&1 || continue
    if [ "$("$c" -r 'echo PHP_SAPI;' 2>/dev/null)" = "cli" ]; then
      echo "$c"; return 0
    fi
  done
  return 1
}

# Dibungkus fungsi agar bash membaca seluruh skrip sebelum git pull bisa menggantinya.
main() {
  cd "$APP_DIR"

  step "Menarik kode terbaru"
  git pull --ff-only

  if [ "$DO_BUILD" -eq 1 ]; then
    export PATH="$NODE_BIN:/bin:/usr/bin:/usr/local/bin:$PATH"
    command -v npm >/dev/null || { echo "npm tidak ditemukan. Cek NODE_BIN: $NODE_BIN"; exit 1; }

    if [ "$DO_CI" -eq 1 ] || [ ! -d node_modules ]; then
      step "Memasang dependensi npm"
      npm ci --no-audit --no-fund
    fi

    step "Build aset"
    npm run build

    [ -f public/build/manifest.json ] || { echo "Build gagal: manifest.json tidak ada."; exit 1; }

    step "Menyalin aset ke $WEB_ROOT/build"
    [ -d "$WEB_ROOT" ] || { echo "Folder web tidak ditemukan: $WEB_ROOT"; exit 1; }
    mkdir -p "$WEB_ROOT/build"
    cp -r public/build/. "$WEB_ROOT/build/"
  fi

  local php
  php="$(find_php || true)"

  if [ "$DO_MIGRATE" -eq 1 ]; then
    [ -n "$php" ] || { echo "PHP CLI tidak ditemukan. Atur PHP_BIN=/path/ke/php lalu ulangi."; exit 1; }
    step "Menjalankan migrasi"
    "$php" artisan migrate --force
  fi

  if [ -n "$php" ]; then
    step "Membersihkan cache view"
    "$php" artisan view:clear
  else
    echo "PHP CLI tidak ditemukan, cache view dilewati (tidak masalah)."
  fi

  printf '\nSelesai. Muat ulang browser dengan Cmd+Shift+R.\n'
}

main
exit $?
