<?php

namespace Tests;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\Response;

class PharmacyRepositoryTest extends TestCase
{
    /**
     * @test
     *
     * @throws Exception
     */
    public function that_repository_error_returns_a_bad_response(): void
    {
        File::delete(storage_path('app/data.geojson'));
        Config::set('buzzoole.url_geojson', 'http://fake.url');

        $this->post('api/v1/rpc', [
            'id' => 1,
            'jsonrpc' => '2.0',
            'method' => 'searchNearestPharmacy',
            'params' => [
                'currentLocation' => [
                    'latitude' => 41.10938993,
                    'longitude' => 15.0321010,
                ],
                'range' => 5000,
                'limit' => 20,
            ],
        ]);

        $this->assertResponseStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->seeJsonStructure([
            'id',
            'jsonrpc',
            'error' => [
                'code',
                'message',
            ],
        ]);
        $this->response->assertJsonFragment(['code' => -32601, 'message' => 'API communication error: cURL error 6: Could not resolve host: fake.url (see https://curl.haxx.se/libcurl/c/libcurl-errors.html) for http://fake.url']);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function that_repository_with_cached_data_returns_a_successful_response(): void
    {
        $this->cacheGeoJsonFile();
        $this->post('api/v1/rpc', [
            'id' => 1,
            'jsonrpc' => '2.0',
            'method' => 'searchNearestPharmacy',
            'params' => [
                'currentLocation' => [
                    'latitude' => 41.10938993,
                    'longitude' => 15.0321010,
                ],
                'range' => 5000,
                'limit' => 20,
            ],
        ]);

        $this->assertResponseOk();
        $this->seeJsonStructure([
            'id',
            'jsonrpc',
            'result' => [
                'pharmacies' => [
                    [
                        'name',
                        'distance',
                        'location' => [
                            'latitude',
                            'longitude',
                        ],
                    ],
                ],
            ],
        ]);
    }
}
