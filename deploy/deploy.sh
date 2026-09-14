#!/usr/bin/env bash
# Atomic deploys for the TomatoPHP demo (demo.tomatophp.com) on the tomatophp LXC:
# Debian 13, nginx, php8.4-fpm, SQLite. Run as root.
#
# Each deploy builds a fresh release, health-checks it on a private port, and only then
# switches the `current` symlink. A failed deploy leaves the live release untouched.
#
#   ROOT/src          git checkout of tomatophp/tomatophp.com
#   ROOT/shared       .env, database/database.sqlite and storage/, shared by every release
#   ROOT/releases/*   built releases (the last KEEP are kept)
#   ROOT/current      symlink to the live release; nginx serves ROOT/current/public
set -euo pipefail

ROOT=${ROOT:-/srv/tomatophp-demo}
REPO=${REPO:-https://github.com/tomatophp/tomatophp.com.git}
KEEP=${KEEP:-3}
HEALTH_PORT=${HEALTH_PORT:-8099}
SRC=$ROOT/src
SHARED=$ROOT/shared
RELEASES=$ROOT/releases

log() { echo "[deploy $(date -u +%H:%M:%S)] $*"; }

mkdir -p "$SHARED/database" "$SHARED/storage" "$RELEASES"

[ -d "$SRC/.git" ] || git clone -q "$REPO" "$SRC"
git -C "$SRC" fetch -q origin master
git -C "$SRC" reset -q --hard origin/master
SHA=$(git -C "$SRC" rev-parse --short HEAD)
REL=$RELEASES/$(date -u +%Y%m%d%H%M%S)-$SHA
log "commit $(git -C "$SRC" log -1 --format='%h %s')"

# First deploy on a fresh ROOT: bring over an existing single-directory install if there is one.
if [ ! -f "$SHARED/.env" ] && [ -f /var/www/demo/.env ]; then
    log "adopting /var/www/demo .env, database and storage"
    cp -a /var/www/demo/.env "$SHARED/.env"
    [ -f /var/www/demo/database/database.sqlite ] && cp -a /var/www/demo/database/database.sqlite "$SHARED/database/"
    cp -a /var/www/demo/storage/. "$SHARED/storage/"
fi
[ -f "$SHARED/.env" ] || { cp "$SRC/.env.example" "$SHARED/.env"; log "created $SHARED/.env from .env.example, review it"; }
[ -f "$SHARED/database/database.sqlite" ] || touch "$SHARED/database/database.sqlite"
mkdir -p "$SHARED/storage/app/public" "$SHARED/storage/framework/cache" "$SHARED/storage/framework/sessions" \
    "$SHARED/storage/framework/views" "$SHARED/storage/logs"

log "building $REL"
mkdir -p "$REL"
git -C "$SRC" archive HEAD | tar -x -C "$REL"
cd "$REL"
ln -sfn "$SHARED/.env" .env
rm -rf storage && ln -sfn "$SHARED/storage" storage
ln -sfn "$SHARED/database/database.sqlite" database/database.sqlite

# Locally the plugins resolve from packages/; production installs the released versions.
# The theme is read straight from GitHub until it is listed on Packagist.
php -r '
$file = "composer.json";
$json = json_decode(file_get_contents($file), true);
// no-api: clone the public repo over HTTPS instead of the GitHub API (no token on the server).
$json["repositories"] = [["type" => "vcs", "url" => "https://github.com/tomatophp/filament-tomatophp-theme.git", "no-api" => true]];
file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");'
rm -f composer.lock
# Fresh Packagist metadata: a just-released plugin version must not resolve to the previous one.
COMPOSER_ALLOW_SUPERUSER=1 composer clear-cache -q
COMPOSER_ALLOW_SUPERUSER=1 composer update --no-dev --optimize-autoloader --no-interaction --no-progress

grep -q '^APP_KEY=base64:' .env || php artisan key:generate --force
php artisan migrate --force
if [ "$(sqlite3 database/database.sqlite 'select count(*) from users;')" = "0" ]; then
    log "empty database, seeding"
    php artisan db:seed --force
fi
php artisan filament-icons:install
php artisan filament:assets
php artisan optimize
chown -R www-data:www-data "$REL" "$SHARED/storage" "$SHARED/database"

log "health check on 127.0.0.1:$HEALTH_PORT"
# Run as www-data: files the check writes (compiled views, cache) must stay writable by php-fpm.
runuser -u www-data -- php -S "127.0.0.1:$HEALTH_PORT" -t public >/dev/null 2>&1 &
SERVER=$!
trap 'kill $SERVER 2>/dev/null || true' EXIT
STATUS=000
for _ in $(seq 1 20); do
    STATUS=$(curl -s -o /dev/null -w '%{http_code}' -H 'Host: demo.tomatophp.com' -H 'X-Forwarded-Proto: https' \
        "http://127.0.0.1:$HEALTH_PORT/admin/login" || true)
    [ "$STATUS" = "200" ] && break
    sleep 1
done
kill $SERVER 2>/dev/null || true
if [ "$STATUS" != "200" ]; then
    log "FAILED: /admin/login returned $STATUS; live release untouched"
    tail -5 "$SHARED/storage/logs/laravel.log" 2>/dev/null || true
    exit 1
fi

chown -R www-data:www-data "$SHARED/storage" "$SHARED/database"
ln -sfn "$REL" "$ROOT/current.new" && mv -Tf "$ROOT/current.new" "$ROOT/current"
systemctl reload php8.4-fpm
log "live: $REL"

ls -1dt "$RELEASES"/* | tail -n +$((KEEP + 1)) | xargs -r rm -rf
log "done"
