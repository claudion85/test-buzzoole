# Nearest pharmacies

Application which could be used to retrieve the nearest pharmacies given a geo position.


## Installation

- Clone the repository

  ```bash
  git clone https://github.com/claudion85/test-buzzoole.git
  cd test-buzzoole
  ```

- Copy [.env.example](./lumen/.env.example) to .env

  ```shell
  cd lumen
  cp .env.example .env
  ```

## Running Locally

From `lumen` folder, please execute:

```shell
composer install
php artisan download:geojson
php -S localhost:9994 -t public
```

## Running in a Docker Environment

From root project folder, simply run:

```shell
make docker-start
```

You can change Nginx public port (default to 9994) in Docker Compose [.env](docker/.env) file.

To stop containers, please execute:

```shell
make docker-stop
```
  
## Caching data

You can cache geo JSON data at any time by executing:

```shell
php artisan download:geojson
```

## Usage

Send a POST request to http://localhost:9994/api/v1/rpc endpoint like:

```shell
curl --location 'http://localhost:9994/api/v1/rpc' \
--header 'Content-Type: application/json' \
--data '{
    "id": 1,
    "jsonrpc": "2.0",
    "method": "searchNearestPharmacy",
    "params": {
        "currentLocation": {
            "latitude": 41.10938993,
            "longitude": 15.0321010
        },
        "range": 5000,
        "limit": 20
    }
}'
```

where:

- `latitude`: Your current latitude <float>
- `longitude`: Your current longitude <float>
- `range`: The maximum distance in meters <int>
- `limit`: The maximum number of entries to show <int>

You should expect a response like:

```json
{
    "jsonrpc": "2.0",
    "id": 1,
    "result": {
        "pharmacies": [
            {
                "name": "Belmonte Di Dott.sse Belmonte S. Ed E. Snc",
                "distance": 30.286631055969107,
                "location": {
                    "latitude": 15.0324625,
                    "longitude": 41.10938993
                }
            }
        ]
    }
}
```

## Project Structure

### [RpcController](./lumen/app/Http/Controllers/RpcController.php)

This is the controller that validates the request, calls the requested methods and provides a response.

### [PharmacyRepository](./lumen/app/Http/Repositories/PharmacyRepository.php)

This is the repository that contains the application's methods and functions.

## Code Style & Static Analysis

Project is PHPStan level 8 compliant. From `lumen` folder, please run:

```shell
./vendor/bin/duster lint
```

## Testing

Project has a 100% code coverage.  From `lumen` folder, please run one of the following:

```shell
./vendor/bin/phpunit
./vendor/bin/phpunit --coverage-text
```
