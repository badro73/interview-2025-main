PHP_SERVICE = php

up:
	docker compose up -d

down:
	docker compose down --remove-orphans

build:
	docker compose build

composer-install:
	docker compose exec -T $(PHP_SERVICE) composer install

migrate:
	docker compose exec -T $(PHP_SERVICE) php bin/console doctrine:migrations:migrate --no-interaction

fixtures:
	docker compose exec -T $(PHP_SERVICE) php bin/console doctrine:fixtures:load --no-interaction

bash:
	docker compose exec $(PHP_SERVICE) bash

logs:
	docker compose logs -f

reset:
	docker compose down -v
	docker system prune --volumes --force