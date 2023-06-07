<?php

namespace Tests;

use App\Console\Commands\DownloadGeoJson;
use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /** {@inheritdoc} */
    public function createApplication(): Application
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    protected function cacheGeoJsonFile(): int
    {
        return $this->artisan((string) (new DownloadGeoJson)->getName());
    }
}
