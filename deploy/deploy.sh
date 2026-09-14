#!/usr/bin/env bash
# Deploys the TomatoPHP demo (demo.tomatophp.com) on the tomatophp LXC:
# Debian 13, nginx, php8.4-fpm, SQLite. Run as root from the app directory.
set -euo pipefail

APP=${APP:-/var/www/demo}
log() { echo "[deploy $(date -u +%H:%M:%S)] $*"; }

cd "$APP"
git fetch -q origin master
git reset -q --hard origin/master
log "commit $(git log -1 --format='%h %s')"

# Locally the plugins resolve from packages/; production installs the released versions.
# The theme is read straight from GitHub until it is listed on Packagist.
php -r '
$file = "composer.json";
$json = json_decode(file_get_contents($file), true);
// no-api: clone the public repo over HTTPS instead of the GitHub API (no token on the server).
$json["repositories"] = [["type" => "vcs", "url" => "https://github.com/tomatophp/filament-tomatophp-theme.git", "no-api" => true]];
file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");'
rm -f composer.lock
COMPOSER_ALLOW_SUPERUSER=1 composer update --no-dev --optimize-autoloader --no-interaction --no-progress

grep -q '^APP_KEY=base64:' .env || php artisan key:generate --force

[ -f database/database.sqlite ] || touch database/database.sqlite
php artisan migrate --force

if [ "$(sqlite3 database/database.sqlite 'select count(*) from users;')" = "0" ]; then
    log "empty database, seeding"
    php artisan db:seed --force
fi

php artisan filament-icons:install
php artisan filament:assets
php artisan optimize

chown -R www-data:www-data storage bootstrap/cache database
systemctl reload php8.4-fpm
log "done"
