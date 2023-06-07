<?php

namespace Tests;

use Symfony\Component\HttpFoundation\Response;

class RpcControllerTest extends TestCase
{
    /**
     * @test
     */
    public function that_endpoint_without_body_returns_an_unprocessable_entity_response(): void
    {
        $this->post('api/v1/rpc');

        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->response->assertJsonFragment(['code' => -32600]);
    }

    /**
     * @test
     */
    public function that_endpoint_with_not_existing_method_returns_an_unprocessable_entity_response(): void
    {
        $this->post('api/v1/rpc', [
            'id' => 1,
            'jsonrpc' => '2.0',
            'method' => 'unexistingMethod',
            'params' => [
                'currentLocation' => [
                    'latitude' => 41.10938993,
                    'longitude' => 15.0321010,
                ],
                'range' => 5000,
                'limit' => 20,
            ],
        ]);

        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'id',
            'jsonrpc',
            'error' => [
                'code',
                'message',
                'data',
            ],
        ]);
        $this->response->assertJsonFragment(['code' => -32600, 'data' => ['The selected method is invalid.']]);
    }

    /**
     * @test
     */
    public function that_endpoint_with_wrong_jsonrpc_version_returns_an_unprocessable_entity_response(): void
    {
        $this->post('api/v1/rpc', [
            'id' => 1,
            'jsonrpc' => '1.0',
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

        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'id',
            'jsonrpc',
            'error' => [
                'code',
                'message',
            ],
        ]);
        $this->response->assertJsonFragment(['code' => -32600, 'data' => ['The selected jsonrpc is invalid.']]);
    }

    /**
     * @test
     */
    public function that_endpoint_with_missing_parameters_returns_an_unprocessable_entity_response(): void
    {
        $this->post('api/v1/rpc', [
            'id' => 1,
            'jsonrpc' => '2.0',
            'method' => 'searchNearestPharmacy',
        ]);

        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->seeJsonStructure([
            'id',
            'jsonrpc',
            'error' => [
                'code',
                'message',
            ],
        ]);
        $this->response->assertJsonFragment(['code' => -32600, 'data' => ['The params field is required.']]);
    }

    /**
     * @test
     */
    public function that_endpoint_returns_a_successful_response(): void
    {
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

        $this->assertResponseStatus(Response::HTTP_OK);
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
