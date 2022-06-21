start: docker-up install
up: docker-up
stop: docker-down

UID := $(shell id -u)
GID := $(shell id -g)

docker-up:
	UID=${UID} GID=${GID} docker-compose up -d --build

install:
	docker exec -u appjobsuser:appjobsgroup appjobs-php composer install

docker-down:
	docker-compose down

test:
	docker exec -u appjobsuser:appjobsgroup appjobs-php ./vendor/bin/phpunit

