param(
    [switch] $ForceEnv,
    [switch] $SkipNpm
)

$ErrorActionPreference = "Stop"
Set-Location $PSScriptRoot

function Require-Command($Name) {
    if (-not (Get-Command $Name -ErrorAction SilentlyContinue)) {
        throw "Command '$Name' is required but was not found. Install it and run this script again."
    }
}

function Run-PhpBin($Name, [string[]] $Arguments) {
    $bin = Join-Path "vendor/bin" $Name
    if (-not (Test-Path $bin)) {
        throw "$bin was not found. Run composer install first."
    }

    & php $bin @Arguments
    if ($LASTEXITCODE -ne 0) {
        throw "$Name failed with exit code $LASTEXITCODE"
    }
}

function Has-CoverageDriver() {
    $modules = & php -m
    return ($modules -match '^xdebug$') -or ($modules -match '^pcov$')
}

function Ensure-Larastan() {
    if ((Test-Path "vendor/bin/phpstan") -and (Test-Path "vendor/larastan/larastan")) {
        return
    }

    Write-Host "Installing Larastan/PHPStan for static analysis..."
    composer require --dev "larastan/larastan:^3.9" --with-all-dependencies --no-interaction --no-progress
}

Require-Command php
Require-Command composer

if ((-not (Test-Path ".env")) -or $ForceEnv) {
    Copy-Item ".env.dev" ".env" -Force
    Write-Host "Created local .env from .env.dev"
} else {
    Write-Host "Existing .env was kept. Use -ForceEnv to recreate it from .env.dev."
}

New-Item -ItemType Directory -Force -Path "database", "storage/framework/cache", "storage/framework/sessions", "storage/framework/views", "bootstrap/cache" | Out-Null
New-Item -ItemType File -Force -Path "database/database.sqlite" | Out-Null

composer install --prefer-dist --no-interaction --no-progress
php artisan key:generate --force
php artisan config:clear
php artisan migrate:fresh --seed --force

if ((-not $SkipNpm) -and (Test-Path "package.json")) {
    if (Get-Command npm -ErrorAction SilentlyContinue) {
        npm install
        npm run build
    } else {
        Write-Host "npm was not found, so frontend dependency installation was skipped."
    }
}

Run-PhpBin "pint" @()
php artisan config:clear
php artisan test

if (Has-CoverageDriver) {
    $env:XDEBUG_MODE = "coverage"
    php artisan test --coverage --min=50
} else {
    Write-Host "Coverage check skipped locally because Xdebug or PCOV is not installed."
    Write-Host "GitHub Actions installs Xdebug and enforces: php artisan test --coverage --min=50"
}

Ensure-Larastan
Run-PhpBin "phpstan" @("analyse", "--configuration=phpstan.neon", "--memory-limit=1G")
Run-PhpBin "pint" @("--test")

Write-Host ""
Write-Host "Lab 10 setup finished successfully."
Write-Host "Local login: admin / admin12345"
Write-Host "Start the project: php artisan serve"
Write-Host "Run checks again: .\run_ci_checks.ps1"
