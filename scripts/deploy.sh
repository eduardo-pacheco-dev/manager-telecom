#!/usr/bin/env bash
set -euo pipefail

export PATH="$HOME/.local/bin:$HOME/bin:/usr/local/bin:/usr/bin:/bin:$PATH"

if [ -s "$HOME/.nvm/nvm.sh" ]; then
    set +u
    export NVM_DIR="$HOME/.nvm"
    . "$NVM_DIR/nvm.sh"
    set -u
fi

if [ -n "${APP_DIR:-}" ] && [ "$APP_DIR" != "." ]; then
    cd "$APP_DIR"
fi

echo "==> Pulling latest changes"
git pull origin main

echo "==> Installing Composer dependencies"
composer install --no-interaction --prefer-dist --no-dev --optimize-autoloader

if [ -f package.json ]; then
    echo "==> Installing frontend dependencies"
    npm ci --no-audit --no-fund

    if grep -q '"build"' package.json; then
        echo "==> Building frontend assets"
        npm run build
    fi
fi

echo "==> Running database migrations"
php artisan migrate --force

echo "==> Caching config, routes and views"
php artisan optimize

echo "==> Restarting queue workers"
php artisan queue:restart || true

if [ -n "${APP_OWNER:-}" ]; then
    echo "==> Fixing storage permissions"
    chown -R "${APP_OWNER}:${APP_GROUP:-$APP_OWNER}" storage bootstrap/cache
fi

echo "==> Deploy finished"