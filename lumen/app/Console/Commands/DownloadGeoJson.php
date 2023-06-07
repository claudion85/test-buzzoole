<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DownloadGeoJson extends Command
{
    /** {@inheritdoc} */
    protected $signature = 'download:geojson';

    /** {@inheritdoc} */
    protected $description = 'Download GeoJson FIle';

    public function handle(): int
    {
        try {

            $geoJson = Http::get(config('buzzoole.url_geojson'))->body();

            file_put_contents(storage_path('app/data.geojson'), $geoJson);

            $this->info('GeoJson file successfully downloaded');

            return self::SUCCESS;

        } catch (Exception $e) {
            $this->error('GeoJson download file failed: ' . $e->getMessage());
            Log::error('Download GeoJson command failed: ' . $e->getMessage());

            return self::FAILURE;
        }

    }
}
