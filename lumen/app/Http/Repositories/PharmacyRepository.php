<?php

namespace App\Http\Repositories;

use App\Exceptions\ApiException;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use JsonException;
use Symfony\Component\HttpFoundation\Response;

class PharmacyRepository
{
    /**
     * @throws JsonException
     * @throws Exception
     */
    public function searchNearestPharmacy(array $data): array
    {
        $response = [];
        $nearestPoint = null;
        $nearestPoints = [];
        $nearestDistance = null;
        $nearestDescriptions = [];
        if (file_exists(storage_path('app/data.geojson'))) {

            /** @var string $geoJsonFile */
            $geoJsonFile = file_get_contents(storage_path('app/data.geojson'));

            /** @var array $json */
            $json = json_decode($geoJsonFile, true, 512, JSON_THROW_ON_ERROR);

        } else {
            try {
                /** @var array $json */
                $json = Http::get(config('buzzoole.url_geojson'))->json();

            } catch (Exception $e) {
                Log::error('Error while saving geojson file ' . $e->getMessage());
                throw new ApiException($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, $e);
            }
        }

        $range = $data['range'] ?? 5000;
        $limit = $data['limit'] ?? 2;

        foreach ($json['features'] as $js) {
            try {
                $coordinates = $js['geometry']['coordinates'];
                [$longitude, $latitude] = $coordinates;

                // calculate haversineDistance in meters
                $distance = $this->haversineDistance($data['currentLocation']['latitude'], $data['currentLocation']['longitude'], $latitude, $longitude);

                if ($distance <= $range) {
                    $js['distance'] = $distance;
                    $nearestPoints[] = $js;

                    $nearestDistances[] = $distance;
                }
                if ($nearestDistance === null || $distance < $nearestDistance) {
                    $js['distance'] = $distance;
                    $nearestPoint = $js;
                    $nearestDistance = $distance;
                }
                // @codeCoverageIgnoreStart
            } catch (Exception $e) {
                Log::error('Error in coordinates calculation ' . $e->getMessage());
                // @codeCoverageIgnoreEnd
            }

        }
        if (isset($nearestDistances, $nearestPoint)) {
            array_multisort($nearestDistances, $nearestPoints);

            //limit the results
            $nearestPoints = array_slice($nearestPoints, 0, $limit);

            foreach ($nearestPoints as $point) {

                $tmp = [];
                $tmp['name'] = $point['properties']['Descrizione'];
                $tmp['distance'] = $point['distance'];
                $tmp['location']['latitude'] = $point['geometry']['coordinates'][0];
                $tmp['location']['longitude'] = $point['geometry']['coordinates'][1];
                $nearestDescriptions[] = $tmp;
            }
        }

        $response['pharmacies'] = $nearestDescriptions;

        return $response;

    }

    /**
     * Haversine distance
     */
    private function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $radius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $radius * $c * 1000;
    }
}
