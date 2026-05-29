#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

rm -f tests/Feature/BrokenPipelineTest.php app/Services/BadFormattedExample.php
rmdir app/Services 2>/dev/null || true

echo "Demo failure files removed."
