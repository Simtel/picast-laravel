.DEFAULT_GOAL := help

help: ## Show this help.
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'

up: ## Up containers
	@docker compose up -d --remove-orphans

build: ## Build containers
	docker compose build

down: ## Down containers
	@docker compose down

stop: ## Stop contrainers
	@docker compose stop

restart: stop up ## Restart docker containers	

mysql-console: ## Mysql Console Failed
	@docker exec -it picast_db /usr/bin/mysql -uroot -pexample

cli: ## PHP console
	docker exec -it --user www-data picast_php bash

migrate: ## Up Migrate
	docker exec -it picast_php sh -c "php artisan migrate"
	docker exec -it picast_php sh -c "php artisan migrate --env=testing"

phpstan: ##Run phpstan analyse
	docker exec -it picast_php sh -c "./vendor/bin/phpstan analyse --memory-limit=2G"

set-githooks: ##Set githooks
	@cd .git/hooks && \
    	ln -sfn ../../.hooks/pre-commit pre-commit && \
    	chmod -R +x pre-commit

pint: ##Run pint analyse
	docker exec -it picast_php sh -c "./vendor/bin/pint"

test: ##Run pint analyse
	docker exec -it picast_php sh -c "php artisan test"
