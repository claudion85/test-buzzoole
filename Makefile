docker-start:
	docker-compose -f docker/docker-compose.yml -p test-buzzoole up --build -d --remove-orphans --force-recreate

docker-stop:
	docker-compose -f docker/docker-compose.yml -p test-buzzoole down
