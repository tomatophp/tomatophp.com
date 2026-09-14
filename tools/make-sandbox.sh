#!/usr/bin/env bash
# Usage: tools/make-sandbox.sh <name>
#
# Creates E:/Sites/tomatophp-sandboxes/<name>: a fresh copy of the committed host app
# (Laravel 13 + Filament 5) whose path repository points at E:/Sites/tomatophp/packages,
# so a package can be installed and exercised in a real project before it is pushed.
# Packages are junctioned in, so the sandbox always runs the package's working tree.
set -euo pipefail

name=${1:?usage: make-sandbox.sh <name>}
src=/e/Sites/tomatophp
dst=/e/Sites/tomatophp-sandboxes/$name

if [ -f "$dst/artisan" ]; then
    echo "Sandbox already exists: $dst"
    exit 0
fi

mkdir -p "$dst"
git -C "$src" archive HEAD | tar -x -C "$dst"

cd "$dst"
cp "$src/.env" .env
sed -i -E 's#^APP_ENV=.*#APP_ENV=local#; s#^APP_DEBUG=.*#APP_DEBUG=true#; s#^APP_URL=.*#APP_URL=http://localhost#; /^SESSION_SECURE_COOKIE=/d' .env

php -r '
$j = json_decode(file_get_contents("composer.json"), true);
$j["repositories"] = [["type" => "path", "url" => "E:/Sites/tomatophp/packages/*", "options" => ["symlink" => true], "canonical" => false]];
file_put_contents("composer.json", json_encode($j, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");'

# The host lock file records relative path-repo URLs, so resolve fresh.
rm -f composer.lock
touch database/database.sqlite
composer.bat update --no-interaction --no-audit
php artisan migrate --force
php artisan test --filter=FilamentPanelSmokeTest

echo "Sandbox ready: $dst"
