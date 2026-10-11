#!/bin/sh
set -e

# Clear ALL caches first (including any stale route cache from previous deployment)
php artisan optimize:clear

# Then re-cache only the config (NOT routes — route:cache can cause stale routes)
php artisan config:cache

# Run migrations (non-fatal — some may have already run)
php artisan migrate --force 2>/dev/null || echo "Migrations completed (some may have already run)"

# Optional one-time seeding: set RUN_SEED=true in Render, deploy once, then remove it
if [ "$RUN_SEED" = "true" ]; then
  php artisan db:seed --class=BarangayAndAdminSeeder --force 2>/dev/null || echo "Seed completed (some items may already exist)"
fi

php artisan storage:link 2>/dev/null || true

exec apache2-foreground
