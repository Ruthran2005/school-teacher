#!/usr/bin/env bash
# School ERP - Teacher app (standalone). Needs PHP 8.2+ and Composer.   Usage: bash setup.sh
set -e
cd "$(dirname "$0")"
PORT=8001

setenv() { sed -i.bak "/^#* *$1=/d" .env && rm -f .env.bak && echo "$1=$2" >> .env; }

[ -f .env ] || cp .env.example .env
composer install --no-interaction

setenv APP_NAME "\"School ERP Teacher\""
setenv APP_URL "http://127.0.0.1:$PORT"
setenv SESSION_DRIVER file
setenv SESSION_COOKIE school_teacher_session
setenv CACHE_STORE file
setenv QUEUE_CONNECTION sync
# Own database by default (SQLite file inside this project).
# To share the database with the Admin app, run like:
#   DB_CONNECTION=mysql DB_DATABASE=school_erp DB_USERNAME=root DB_PASSWORD= bash setup.sh
if [ -n "$DB_CONNECTION" ]; then
  setenv DB_CONNECTION "$DB_CONNECTION"
  setenv DB_HOST "${DB_HOST:-127.0.0.1}"; setenv DB_PORT "${DB_PORT:-3306}"
  setenv DB_DATABASE "${DB_DATABASE:-school_erp}"; setenv DB_USERNAME "${DB_USERNAME:-root}"; setenv DB_PASSWORD "${DB_PASSWORD:-}"
else
  touch database/database.sqlite
  setenv DB_CONNECTION sqlite
fi

php artisan key:generate --force
php artisan migrate --seed --force
php artisan db:seed --class=DemoDataSeeder --force

# Frontend assets are already built in public/build. Rebuild only if missing.
[ -f public/build/manifest.json ] || (npm install && npm run build)

echo
echo "Done. Start:  php artisan serve --port=$PORT"
echo "Demo login: teacher@school.test / password"
