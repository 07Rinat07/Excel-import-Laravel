<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Vite;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $hotFile = storage_path('framework/testing/vite.hot');
        if (! is_dir(dirname($hotFile))) {
            mkdir(dirname($hotFile), 0777, true);
        }
        file_put_contents($hotFile, 'http://localhost:5173');
        Vite::useHotFile($hotFile);
    }

    protected function tearDown(): void
    {
        $hotFile = storage_path('framework/testing/vite.hot');
        if (file_exists($hotFile)) {
            @unlink($hotFile);
        }

        parent::tearDown();
    }
}
