PHP_SERVICE = php

# Docker basics
up:
	docker compose up -d

down:
	docker compose down --remove-orphans

build:
	docker compose build

composer-install:
	docker compose exec -T $(PHP_SERVICE) composer install

# Doctrine / Database
db-create:
	docker compose exec -T $(PHP_SERVICE) php bin/console doctrine:database:create -n

db-drop:
	docker compose exec -T $(PHP_SERVICE) php bin/console doctrine:database:dro --force -n

db-create-test:
	docker compose exec -T $(PHP_SERVICE) php bin/console doctrine:database:create -n --env=test || true

db-reset: db-drop db-create migrate fixtures

migrate:
	@echo "Vérification de la connexion à la base de données..."
	@docker compose exec -T $(PHP_SERVICE) sh -c 'until nc -z db 3306; do echo "En attente de MySQL..."; sleep 1; done'
	docker compose exec -T $(PHP_SERVICE) php bin/console doctrine:migrations:migrate --no-interaction

migration-up:
	docker compose exec -T $(PHP_SERVICE) php php bin/console doctrine:migrations:execute \
		DoctrineMigrations\\$(VERSION) --up

db-init: db-create migrate

fixtures:
	docker compose exec -T $(PHP_SERVICE) php bin/console doctrine:fixtures:load --no-interaction

db-full: db-init fixtures

install: up composer-install migrate cache-clear

# Cache
cache-clear:
	docker compose exec -T $(PHP_SERVICE) php bin/console cache:clear --no-warmup

clear:
	docker compose exec -T $(PHP_SERVICE) rm -rf var vendor

cache-clear-prod:
	docker compose exec -T $(PHP_SERVICE) php bin/console cache:clear --env=prod --no-debug --no-warmup

# Tests Behat + PHPUnit
test-behat:
	docker compose exec -T $(PHP_SERVICE) php bin/console cache:clear --env=test -n || true
	docker compose exec -T $(PHP_SERVICE) php bin/console doctrine:migrations:migrate --no-interaction --env=test || true
	docker compose exec -T $(PHP_SERVICE) vendor/bin/behat --format=progress --colors

test-phpunit:
	docker compose exec -T $(PHP_SERVICE) php bin/console cache:clear --env=test -n || true
	docker compose exec -T $(PHP_SERVICE) php bin/console doctrine:migrations:migrate --no-interaction --env=test || true
	docker compose exec -T php vendor/bin/phpunit --colors=always

test: test-behat test-phpunit

# Utils
bash:
	docker compose exec $(PHP_SERVICE) sh

logs:
	docker compose logs -f

# Reset / Setup
reset:
	docker compose down -v
	docker system prune --volumes --force

dev-init: composer-install db-init cache-clear

full-init: dev-init fixtures
