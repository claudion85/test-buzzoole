<?php

namespace Tests;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class DownloadGeoJsonCommandTest extends TestCase
{
    /**
     * @test
     */
    public function that_download_geo_json_command_can_be_executed(): void
    {
        $code = $this->cacheGeoJsonFile();

        $this->assertEquals(Command::SUCCESS, $code);
    }

    /**
     * @test
     */
    public function that_download_geo_json_command_with_bad_url_returns_failure(): void
    {
        Config::set('buzzoole.url_geojson', 'http://fake.url');
        $code = $this->cacheGeoJsonFile();

        $this->assertEquals(Command::FAILURE, $code);
    }
}
