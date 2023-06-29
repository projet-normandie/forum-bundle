install: ## install project
	composer install

update: ## update project
	composer update

test: ## launch tests
	./vendor/bin/simple-phpunit

lint: ##
	composer run lint

phpstan: ##
	composer run lint:phpstan -- --memory-limit=1G

phpcs: ##
	composer run lint:phpcs

phpcs-fix:
	composer run lint:phpcs:fix