#!/usr/bin/env bash
# Sunucuya yüklenecek tek bir zip üretir. Sunucuda ana dizinde (home) açılır:
#
#     cd ~ && unzip -o wbn-deploy-*.zip
#
# Zip içinde iki klasör vardır: wbnturkiye.com/ (web kökü) ve wbn_app/ (uygulama).
# Sunucudaki config.php ve storage/ (dekontlar, log'lar) zip'e HİÇ girmez; bu yüzden `unzip -o` onları ezemez.
# Değişkenler: WEB_DIR (varsayılan wbnturkiye.com), APP_DIR (varsayılan wbn_app).
set -euo pipefail

WEB_DIR="${WEB_DIR:-wbnturkiye.com}"
APP_DIR="${APP_DIR:-wbn_app}"

cd "$(dirname "$0")/.."
ROOT="$PWD"

echo "→ PHP sözdizimi kontrol ediliyor..."
fail=0
while IFS= read -r -d '' f; do
  out="$(php -l "$f" 2>&1)" || { echo "$out"; fail=1; }
done < <(git ls-files -z 'public_html/*.php' 'wbn_app/src/*.php' 'wbn_app/templates/*.php' 'wbn_app/bin/*.php')
[ "$fail" = 0 ] || { echo "Hata var, zip üretilmedi."; exit 1; }

if [ -n "$(git status --porcelain -- public_html wbn_app)" ]; then
  echo "! Uyarı: commit edilmemiş değişiklikler var; zip çalışma klasöründeki hâliyle üretilecek."
fi

STAGE="$(mktemp -d)"
trap 'rm -rf "$STAGE"' EXIT
mkdir -p "$STAGE/$WEB_DIR" "$STAGE/$APP_DIR"

# Yalnızca git'in izlediği dosyalar (çöp/yerel dosyalar girmez). Silinmiş ama izlenen dosyalar atlanır.
copy_tracked() { # $1 kaynak klasör, $2 hedef
  git ls-files -z --cached --exclude-standard "$1" | while IFS= read -r -d '' f; do
    [ -f "$f" ] || continue
    mkdir -p "$2/$(dirname "${f#"$1"/}")"
    cp -p "$f" "$2/${f#"$1"/}"
  done
}
copy_tracked public_html "$STAGE/$WEB_DIR"
copy_tracked wbn_app "$STAGE/$APP_DIR"

# Sunucuya girmemesi gerekenler: yerel/Docker ayarı, gerçek ayar ve kullanıcı verisi.
rm -f  "$STAGE/$APP_DIR/config.php" "$STAGE/$APP_DIR/config.docker.php"
rm -rf "$STAGE/$APP_DIR/storage"
find "$STAGE" -name '.DS_Store' -delete

# Inode tasarrufu: PHPMailer'ın çalışması için gerekmeyen test/örnek/doküman dosyaları.
PM="$STAGE/$APP_DIR/vendor/phpmailer/phpmailer"
if [ -d "$PM" ]; then
  rm -rf "$PM/test" "$PM/examples" "$PM/docs" "$PM/.github" "$PM/.phan"
  find "$PM" -maxdepth 1 -name '.*' -type f -delete
  rm -f  "$PM"/phpunit.xml.dist "$PM"/phpdoc.dist.xml "$PM"/phpcs.xml.dist "$PM"/changelog.md \
         "$PM"/get_oauth_token.php "$PM"/*.md "$PM"/composer.json
  find "$PM/language" -type f ! -name 'phpmailer.lang-en.php' ! -name 'phpmailer.lang-tr.php' -delete
fi

mkdir -p dist
ZIP="$ROOT/dist/wbn-deploy-$(date +%Y%m%d-%H%M).zip"
(cd "$STAGE" && zip -qrX "$ZIP" "$WEB_DIR" "$APP_DIR")

# Güvenlik kontrolü: ezilmemesi gereken dosyalar zip'te olmamalı.
# (grep -q + pipefail birlikte yanlış sonuç verdiği için liste önce değişkene alınır.)
LIST="$(unzip -Z1 "$ZIP")"
if grep -Eq "^$APP_DIR/(config\.php|config\.docker\.php|storage/)" <<<"$LIST"; then
  echo "HATA: zip içinde config.php veya storage var!"; rm -f "$ZIP"; exit 1
fi

files=$(grep -vc '/$' <<<"$LIST" || true)
dirs=$(grep -c '/$' <<<"$LIST" || true)
echo "✓ $ZIP"
echo "  $(du -h "$ZIP" | cut -f1) · $files dosya + $dirs klasör (≈ $((files + dirs)) inode)"
echo
echo "Sunucuda:  cd ~ && unzip -o $(basename "$ZIP")"
