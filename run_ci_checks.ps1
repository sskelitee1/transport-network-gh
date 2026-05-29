$ErrorActionPreference = "Stop"
Set-Location $PSScriptRoot

function Run-PhpBin($Name, [string[]] $Arguments) {
    $bin = Join-Path "vendor/bin" $Name
    if (-not (Test-Path $bin)) {
        throw "$bin was not found. Run setup_lab10.ps1 first."
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

if (-not (Test-Path ".env")) {
    Copy-Item ".env.ci" ".env" -Force
    php artisan key:generate --force
}

php artisan config:clear

if (Has-CoverageDriver) {
    $env:XDEBUG_MODE = "coverage"
    php artisan test --coverage --min=50
} else {
    Write-Host "Xdebug or PCOV is not installed; running tests without local coverage gate."
    php artisan test
}

Ensure-Larastan
Run-PhpBin "phpstan" @("analyse", "--configuration=phpstan.neon", "--memory-limit=1G")
Run-PhpBin "pint" @("--test")

Write-Host "All local Lab 10 checks passed."
