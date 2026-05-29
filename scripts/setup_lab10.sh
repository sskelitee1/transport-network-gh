#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

FORCE_ENV=0
SKIP_NPM=0

for arg in "$@"; do
    case "$arg" in
        --force-env)
            FORCE_ENV=1
            ;;
        --skip-npm)
            SKIP_NPM=1
            ;;
        *)
            echo "Unknown argument: $arg"
            echo "Usage: bash setup_lab10.sh [--force-env] [--skip-npm]"
            exit 1
            ;;
    esac
done

require_command() {
    if ! command -v "$1" >/dev/null 2>&1; then
        echo "Command '$1' is required but was not found. Install it and run the script again."
        exit 1
    fi
}

check_php_extensions() {
    php -r '
        $required = ["ctype", "curl", "dom", "fileinfo", "mbstring", "openssl", "pdo", "pdo_sqlite", "sqlite3", "tokenizer", "xml", "xmlwriter"];
        $loaded = array_map("strtolower", get_loaded_extensions());
        $missing = array_values(array_filter($required, fn ($extension) => ! in_array(strtolower($extension), $loaded, true)));
        if ($missing !== []) {
            fwrite(STDERR, "Missing PHP extensions: ".implode(", ", $missing).PHP_EOL);
            fwrite(STDERR, "Install the missing extensions, then run this script again.".PHP_EOL);
            exit(1);
        }
    '
}

has_coverage_driver() {
    php -m | grep -Eiq '^(xdebug|pcov)$'
}

run_php_bin() {
    local bin="$1"
    shift

    if [ -f "vendor/bin/$bin" ]; then
        php "vendor/bin/$bin" "$@"
    else
        echo "vendor/bin/$bin was not found. Run composer install first."
        exit 1
    fi
}

ensure_larastan() {
    if [ -f vendor/bin/phpstan ] && [ -d vendor/larastan/larastan ]; then
        return
    fi

    echo "Installing Larastan/PHPStan for static analysis..."
    composer require --dev "larastan/larastan:^3.9" --with-all-dependencies --no-interaction --no-progress
}

require_command php
require_command composer
check_php_extensions

if [ ! -f .env ] || [ "$FORCE_ENV" -eq 1 ]; then
    cp .env.dev .env
    echo "Created local .env from .env.dev"
else
    echo "Existing .env was kept. Use --force-env to recreate it from .env.dev."
fi

mkdir -p database storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
: > database/database.sqlite

composer install --prefer-dist --no-interaction --no-progress
php artisan key:generate --force
php artisan config:clear
php artisan storage:link >/dev/null 2>&1 || true
php artisan migrate:fresh --seed --force

if [ "$SKIP_NPM" -eq 0 ] && [ -f package.json ]; then
    if command -v npm >/dev/null 2>&1; then
        npm install
        npm run build
    else
        echo "npm was not found, so frontend dependency installation was skipped."
    fi
fi

run_php_bin pint
php artisan config:clear
php artisan test

if has_coverage_driver; then
    XDEBUG_MODE=coverage php artisan test --coverage --min=50
else
    echo "Coverage check skipped locally because Xdebug or PCOV is not installed."
    echo "GitHub Actions installs Xdebug and enforces: php artisan test --coverage --min=50"
fi

ensure_larastan
run_php_bin phpstan analyse --configuration=phpstan.neon --memory-limit=1G
run_php_bin pint --test

cat <<'MESSAGE'

Lab 10 setup finished successfully.

Local login for the seeded app:
  URL:   http://127.0.0.1:8000
  Login: admin
  Pass:  admin12345

Start the project:
  php artisan serve

Run checks again:
  bash scripts/run_ci_checks.sh

MESSAGE
