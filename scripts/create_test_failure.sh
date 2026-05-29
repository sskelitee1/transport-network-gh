#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

cat > tests/Feature/BrokenPipelineTest.php <<'PHP'
<?php

namespace Tests\Feature;

use Tests\TestCase;

class BrokenPipelineTest extends TestCase
{
    public function test_pipeline_fails_on_purpose(): void
    {
        $this->assertTrue(false);
    }
}
PHP

echo "Created tests/Feature/BrokenPipelineTest.php. Commit and push it to capture the failing tests screenshot."
