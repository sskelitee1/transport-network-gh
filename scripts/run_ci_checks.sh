#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

run_php_bin() {
    local bin="$1"
    shift

    if [ -f "vendor/bin/$bin" ]; then
        php "vendor/bin/$bin" "$@"
    else
        echo "vendor/bin/$bin was not found. Run bash setup_lab10.sh first."
        exit 1
    fi
}

has_coverage_driver() {
    php -m | grep -Eiq '^(xdebug|pcov)$'
}

ensure_larastan() {
    if [ -f vendor/bin/phpstan ] && [ -d vendor/larastan/larastan ]; then
        return
    fi

    echo "Installing Larastan/PHPStan for static analysis..."
    composer require --dev "larastan/larastan:^3.9" --with-all-dependencies --no-interaction --no-progress
}

if [ ! -f .env ]; then
    cp .env.ci .env
    php artisan key:generate --force
fi

php artisan config:clear

if has_coverage_driver; then
    XDEBUG_MODE=coverage php artisan test --coverage --min=50
else
    echo "Xdebug or PCOV is not installed; running tests without local coverage gate."
    php artisan test
fi

ensure_larastan
run_php_bin phpstan analyse --configuration=phpstan.neon --memory-limit=1G
run_php_bin pint --test

echo "All local Lab 10 checks passed."
