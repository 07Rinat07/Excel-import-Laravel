#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

if [ ! -f .env ]; then
  cp .env.example .env
fi

if [ ! -f vendor/autoload.php ]; then
  composer install --no-interaction --prefer-dist
fi

# Wait for MySQL to be ready
echo "Waiting for database connection..."
until php artisan db:monitor > /dev/null 2>&1; do
  sleep 2
done
echo "Database is up!"

if ! grep -q "^APP_KEY=" .env || [ -z "$(grep '^APP_KEY=' .env | cut -d= -f2)" ]; then
  php artisan key:generate --force
fi

# Run migrations and seeders
# Use --no-interaction to avoid prompts
# We use || true to prevent entrypoint from failing if migrations are already running or table exists
php artisan migrate --force --no-interaction || echo "Migration failed or already handled"
php artisan db:seed --force
# Optional: Seed demo data if explicitly requested via env
if [ "${SEED_DEMO:-false}" = "true" ]; then
  php artisan db:seed --class=DemoDataSeeder --force
fi

mkdir -p storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

exec "$@"
