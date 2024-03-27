.DEFAULT_GOAL := help

help: ## Show this help.
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'

up: ## Up containers
	@docker compose up -d --remove-orphans
	@echo -e "Make: Up containers.\n"

build: ## Build containers
	docker compose build
	@echo -e "Make: Up containers.\n"

down: ## Down containers
	@docker compose down

stop: ## Stop contrainers
	@docker compose stop

restart: stop up ## Restart docker containers	

mysql-console: ## Mysql Console Failed
	@docker exec -it picast_db /usr/bin/mysql -uroot -pexample

php-console: ## PHP console
	docker exec -it --user www-data picast_web bash

migrate-up: ## Up MIgrate
	docker exec -it picast_web sh -c "php artisan migrate"

phpstan: ##Run phpstan analyse
	docker exec -it picast_web sh -c "./vendor/bin/phpstan analyse --memory-limit=2G"

set-githooks: ##Set githooks
	@cd .git/hooks && \
    	ln -sfn ../../hooks/pre-commit pre-commit && \
    	chmod -R +x pre-commit

pint: ##Run pint analyse
	docker exec -it picast_web sh -c "./vendor/bin/pint"

test: ##Run pint analyse
	docker exec -it picast_web sh -c "php artisan test"
