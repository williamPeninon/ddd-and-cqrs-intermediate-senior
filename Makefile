install:
	composer install

bdd:
	vendor/bin/behat

bdd-domain:
	vendor/bin/behat --suite=domain

bdd-infra:
	vendor/bin/behat --suite=infra

unit:
	vendor/bin/phpunit --testsuite Unit

integration:
	vendor/bin/phpunit --testsuite Integration

analyse:
	vendor/bin/phpstan analyse src --level=8

postgres-up:
	docker compose up -d postgres

postgres-down:
	docker compose down -v
