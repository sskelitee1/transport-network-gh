#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

mkdir -p app/Services
cat > app/Services/BadFormattedExample.php <<'PHP'
<?php

namespace App\Services;

class BadFormattedExample{public function run( ){return true;}}
PHP

echo "Created app/Services/BadFormattedExample.php. Commit and push it to capture the failing linter screenshot."
