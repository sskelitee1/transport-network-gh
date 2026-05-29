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
